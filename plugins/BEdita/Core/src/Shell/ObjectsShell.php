<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Shell;

use BEdita\Core\Model\Action\ListObjectsAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Table\UsersTable;
use BEdita\Core\Utility\JsonApiSerializable;
use BEdita\Core\Utility\LoggedUser;
use Cake\Console\Shell;
use Cake\Datasource\EntityInterface;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Objects shell commands: export and import CSV/JSON files
 *
 * @since 4.0.0
 */
class ObjectsShell extends Shell
{
    /**
     * Max number of objects to export in a single file
     *
     * @var int
     */
    public const MAX_OBJECT_NUM = 1000;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('export', [
            'help' => 'export objects to JSON or CSV',
            'parser' => [
                'description' => [
                    'Export objects list to file',
                    'Use option --filter (optional, JSON) to define objects set to export'
                ],
                'options' => [
                    'type' => [
                        'help' => 'Object type name',
                        'short' => 't',
                        'required' => true,
                        'default' => null,
                    ],
                    'file' => [
                        'help' => 'output CSV or JSON file',
                        'short' => 'f',
                        'required' => true,
                    ],
                    'filter' => [
                        'help' => 'Objects list filter in JSON format',
                        'required' => false,
                    ],
                ],
            ]
        ]);

        $parser->addSubcommand('import', [
            'help' => 'export objects from JSON or CSV file',
            'parser' => [
                'options' => [
                    'file' => [
                        'help' => 'input CSV or JSON file',
                        'short' => 'f',
                        'required' => true,
                    ],
                ],
            ]
        ]);

        return $parser;
    }

    /**
     * Export objects
     *
     * @return void
     */
    public function export()
    {
        $table = $this->getTable();
        $action = new ListObjectsAction(compact('table'));
        $filter = [];
        if (!empty($this->param('filter'))) {
            $filter = json_decode($this->param('filter'), true);
        }
        $query = $action(compact('filter'));
        $query = $query->limit(self::MAX_OBJECT_NUM);

        $ext = $this->getFileType();
        $method = 'export' . $ext;

        $count = $this->{$method}($query);
        $this->out(sprintf('<success>Done. %d objects(s) exported</success>', $count));
    }

    /**
     * Get import or export file type: JSON or CSV
     *
     * @return string
     * @throws BadRequestException
     */
    protected function getFileType()
    {
        if (empty($this->param('file'))) {
            throw new BadRequestException('Missing mandatory --file argument');
        }

        $file = $this->param('file');
        $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, ['JSON', 'CSV'])) {
            throw new BadRequestException(sprintf('File extension not recognized "%s"', $ext));
        }

        return $ext;
    }

    /**
     * Export objects to JSON file
     *
     * @param Query $query Query object
     * @return int Number of exported objects
     */
    protected function exportJSON(Query $query)
    {
        $export = [];
        foreach ($query as $entity) {
            $export[] = $this->entityData($entity);
        }
        file_put_contents($this->param('file'), json_encode($export, JSON_PRETTY_PRINT));

        return count($export);
    }

    /**
     * Retrieve object entity data in array format from JSON API attributes + meta
     *
     * @param EntityInterface $entity Object entity
     * @return array Object data in array format
     */
    protected function entityData(EntityInterface $entity)
    {
        $options = JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS | JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS;
        if (!$entity instanceof JsonApiSerializable) {
            throw new \InvalidArgumentException(sprintf(
                'Objects must implement "%s", got "%s" instead',
                JsonApiSerializable::class,
                is_object($entity) ? get_class($entity) : gettype($entity)
            ));
        }

        return $entity->jsonApiSerialize($options);
    }

    /**
     * Export objects to CSV file
     *
     * @param Query $query Query object
     * @return int Number of exported objects
     */
    protected function exportCSV(Query $query)
    {
        $count = 0;
        $header = false;
        $fp = fopen($this->param('file'), 'w');
        foreach ($query as $entity) {
            $data = $this->entityData($entity);
            $data = $data + $data['attributes'] + $data['meta'];
            unset($data['attributes'], $data['meta']);
            if (!$header) {
                fputcsv($fp, array_keys($data));
                $header = true;
            }
            fputcsv($fp, $this->csvValues($data));
            $count++;
        }
        fclose($fp);

        return $count;
    }

    /**
     * Object data in CSV format
     *
     * @param array $data Object data
     * @return array CSV values format
     */
    protected function csvValues(array $data)
    {
        $res = [];
        foreach ($data as $value) {
            if (is_array($value) || is_object($value)) {
                $res[] = json_encode($value);
            } else {
                $res[] = (string)$value;
            }
        }

        return $res;
    }

    /**
     * Init model table using `--type|-t` option
     *
     * @return \Cake\ORM\Table
     */
    protected function getTable()
    {
        $modelName = Inflector::camelize($this->param('type'));

        return TableRegistry::get($modelName);
    }

    /**
     * Import objects from CSV or JSON file
     *
     * @return void
     */
    public function import()
    {
        $ext = $this->getFileType();
        $method = 'import' . $ext;
        $count = $this->{$method}();
        $this->out(sprintf('<success>Done. %d objects(s) imported</success>', $count));
    }

    /**
     * Import objects from JSON file
     *
     * @return int Number of exported objects
     */
    protected function importJSON()
    {
        $content = file_get_contents($this->param('file'));
        $data = json_decode($content, true);
        $count = 0;
        foreach ($data as $object) {
            $this->importObject($object);
            $count++;
        }

        return $count;
    }

    /**
     * Import objects from CSV file
     *
     * @return int Number of exported objects
     */
    protected function importCSV()
    {
        $fp = fopen($this->param('file'), 'r');
        $keys = [];
        while (($data = fgetcsv($fp)) !== false) {
            if (empty($keys)) {
                $keys = $data;
            } else {
                $object = array_combine($keys, $data);
                $this->importObject($object);
                $count++;
            }
        }
        fclose($fp);

        return $count;
    }

    /**
     * Create new object from array
     *
     * @param array $data Object data
     * @return void
     */
    protected function importObject(array $data)
    {
        $modelName = Inflector::camelize($data['type']);
        $table = TableRegistry::get($modelName);
        $entity = $table->newEntity();
        $action = new SaveEntityAction(compact('table'));
        if (!empty($data['attributes'])) {
            $data += $data['attributes'];
        }
        unset($data['id'], $data['type'], $data['uname'], $data['meta'], $data['attributes']);
        LoggedUser::setUser(['id' => UsersTable::ADMIN_USER]);
        $action(compact('entity', 'data'));
    }
}
