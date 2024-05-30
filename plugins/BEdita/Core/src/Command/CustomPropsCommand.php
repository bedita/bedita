<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Exception\CakeException as Exception;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Generator;

/**
 * CustomProps command.
 *
 * Check custom properties formatting & validity
 *
 * @since 4.5.0
 */
class CustomPropsCommand extends Command
{
    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->addOption('id', [
                'help' => 'Object ID to check',
                'required' => false,
            ])
            ->addOption('type', [
                'help' => 'Object type name to check',
                'short' => 't',
                'required' => false,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $types = TableRegistry::getTableLocator()->get('ObjectTypes')
            ->find('list', ['valueField' => 'name'])
            ->where(['is_abstract' => false])
            ->all()
            ->toList();
        if ($args->getOption('type')) {
            $types = [(string)$args->getOption('type')];
        }
        $errors = 0;
        foreach ($types as $type) {
            $errors += $this->customPropsByType($type, $args->getOption('id'), $io);
        }
        if ($errors) {
            $io->error(sprintf('Errors found (%d)', $errors));

            return self::CODE_ERROR;
        }

        $io->success('Done');

        return null;
    }

    /**
     * Check custom properties of an object type.
     *
     * @param string $type Object type
     * @param int|null $id Object ID
     * @param \Cake\Console\ConsoleIo $io Console IO
     * @return int Number of errors found
     */
    protected function customPropsByType(string $type, ?int $id, ConsoleIo $io): int
    {
        $io->info(sprintf('Processing %s...', $type));
        $this->Table = TableRegistry::getTableLocator()->get(Inflector::camelize($type));
        $query = $this->Table
            ->find('type', (array)$type);
        if ($id) {
            $query = $query->where(compact('id'));
        }

        $count = $err = 0;
        foreach ($this->objectsGenerator($query) as $object) {
            $props = (array)$object->get('custom_props');
            $object->set($props);
            try {
                $this->Table->saveOrFail($object);
                $count++;
            } catch (Exception $ex) {
                $msg = sprintf(
                    'Failed update on %s "%s" [id %d] - exception: %s',
                    $type,
                    $object->get('title'),
                    $object->id,
                    $ex->getMessage()
                );
                $this->log($msg, 'error');
                $err++;
            }
        }
        $io->success(sprintf('Updated %d %s without errors', $count, $type));
        if ($err) {
            $io->warning(sprintf('%d errors updating %s', $err, $type));
        }

        return $err;
    }

    /**
     * Objects generator.
     *
     * @param \Cake\ORM\Query $query Query object
     * @return \Generator
     */
    protected function objectsGenerator(Query $query): Generator
    {
        $pageSize = 1000;
        $pages = ceil($query->count() / $pageSize);

        for ($page = 1; $page <= $pages; $page++) {
            yield from $query
                ->page($page, $pageSize)
                ->toArray();
        }
    }
}
