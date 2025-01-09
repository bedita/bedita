<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Action;

use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Entity\Stream;
use BEdita\Core\Utility\LoggedUser;
use BEdita\Core\Utility\SchemaTools;
use BEdita\Core\Utility\Text;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

/**
 * Clone object action
 */
class CloneObjectAction extends BaseAction
{
    use LocatorAwareTrait;

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * @inheritDoc
     */
    protected function initialize(array $data)
    {
        $this->Table = $this->getConfig('table');
        if (empty(LoggedUser::id())) {
            throw new UnauthorizedException('Cannot clone object without a logged user');
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(array $data = [])
    {
        $sourceId = (int)Hash::get($data, 'id');
        $include = (array)Hash::get($data, 'data._meta.include');
        $attributes = array_filter($data['data'], function ($key) {
            return !in_array($key, ['_meta']);
        }, ARRAY_FILTER_USE_KEY);

        return $this->Table->getConnection()->transactional(function () use ($sourceId, $attributes, $include) {
            $objectType = $this->Table->objectType();
            $contain = $objectType->get('associations');
            $action = new GetObjectAction(['table' => $this->Table]);
            $source = $action(['primaryKey' => $sourceId, 'contain' => $contain]);
            $entity = $this->cloneEntity($source, $attributes);
            if (!empty($source->get('streams'))) {
                $this->cloneStreams($source->get('streams'), $entity);
            }
            if (in_array('relationships', $include)) {
                $this->cloneRelationships($sourceId, $entity->id, 'left_id');
                $this->cloneRelationships($sourceId, $entity->id, 'right_id');
            }
            if (in_array('translations', $include)) {
                $this->cloneTranslations($sourceId, $entity->id);
            }

            return $entity;
        });
    }

    /**
     * Clone entity
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $sourceEntity Source object
     * @param array $attributes Attributes to set
     * @return \Cake\Datasource\EntityInterface
     */
    public function cloneEntity(ObjectEntity $sourceEntity, array $attributes): EntityInterface
    {
        $schema = $this->Table->getSchema();
        $reset = SchemaTools::getPrimaryFields($schema, ['count' => 1]) + ['created', 'modified'];
        $unique = SchemaTools::getUniqueFields($schema, ['count' => 1]);
        $nullable = SchemaTools::getNullableFields($schema);
        $schemaInfo = compact('reset', 'unique', 'nullable', 'attributes');
        /** @var \BEdita\Core\Model\Entity\ObjectEntity $entity */
        $entity = $this->Table->newEmptyEntity();
        $entityAttributes = $sourceEntity->getVisible();
        foreach ($entityAttributes as $field) {
            $this->setEntityField($schemaInfo, $sourceEntity, $entity, $field);
        }
        if (!empty($entity->get('streams'))) {
            $entity->set('streams', []);
        }

        return $this->Table->saveOrFail($entity);
    }

    /**
     * Set entity field
     *
     * @param array $schemaInfo Schema information
     * @param \BEdita\Core\Model\Entity\ObjectEntity $sourceEntity Source object
     * @param \BEdita\Core\Model\Entity\ObjectEntity $entity Destination object
     * @param string $field Field name
     * @return mixed
     */
    protected function setEntityField(array $schemaInfo, ObjectEntity $sourceEntity, ObjectEntity $entity, string $field)
    {
        if (in_array($field, (array)Hash::get($schemaInfo, 'reset'))) {
            return null; // skip
        }
        $attributes = (array)Hash::get($schemaInfo, 'attributes');
        if (array_key_exists($field, $attributes)) {
            $entity->set($field, $attributes[$field]);

            return $attributes[$field];
        }
        $value = $sourceEntity->get($field);
        if (!in_array($field, (array)Hash::get($schemaInfo, 'unique'))) {
            $entity->set($field, $value);

            return $value;
        }
        // unique and string? then generate a new value
        if (is_string($value)) {
            $value = sprintf('%s-%s', $value, Text::uuid());
            $entity->set($field, $value);

            return $value;
        }
        // unique, not a string and nullable? then 'null'
        if (in_array($field, (array)Hash::get($schemaInfo, 'nullable'))) {
            $entity->set($field, null);

            return null;
        }

        throw new \RuntimeException(sprintf('Cannot set unique field "%s"', $field));
    }

    /**
     * Clone streams
     *
     * @param array $streams Source streams
     * @param \BEdita\Core\Model\Entity\ObjectEntity $destination Destination object
     * @return array
     */
    public function cloneStreams(array $streams, ObjectEntity $destination): array
    {
        $clonedStreams = [];
        foreach ($streams as $stream) {
            $clonedStreams[] = $this->cloneStream($stream, $destination);
        }

        return $clonedStreams;
    }

    /**
     * Clone stream
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Source stream
     * @param \BEdita\Core\Model\Entity\ObjectEntity $entity Destination object
     * @return \Cake\Datasource\EntityInterface
     */
    public function cloneStream(Stream $stream, ObjectEntity $entity): EntityInterface
    {
        // clone stream and files
        $streamsTable = $this->fetchTable('Streams');
        $clonedStream = $streamsTable->clone($stream);

        // add stream to media
        $clonedStream->set('object_id', $entity->id);
        $tmp = $streamsTable->saveOrFail($clonedStream);

        return $clonedStream;
    }

    /**
     * Clone relationships
     *
     * @param int $sourceId Source object ID
     * @param int $destinationId Destination object ID
     * @param string $key Relationship key ('left_id' or 'right_id')
     * @return array
     */
    public function cloneRelationships(int $sourceId, int $destinationId, string $key): array
    {
        $objectRelationsTable = $this->fetchTable('ObjectRelations');
        $objectRelations = $objectRelationsTable->find()->where([$key => $sourceId])->toArray();
        if (empty($objectRelations)) {
            return [];
        }
        $objectRelations = array_map(function ($objectRelation) use ($destinationId, $key) {
            $objectRelation->set($key, $destinationId);
            $objectRelation->setNew(true);

            return $objectRelation;
        }, $objectRelations);

        return $objectRelationsTable->saveManyOrFail($objectRelations);
    }

    /**
     * Clone translations
     *
     * @param int $sourceId Source object ID
     * @param int $destinationId Destination object ID
     * @return array
     */
    public function cloneTranslations(int $sourceId, $destinationId): array
    {
        $translationsTable = $this->fetchTable('Translations');
        $objectTranslations = $translationsTable->find()->where(['object_id' => $sourceId])->toArray();
        if (empty($objectTranslations)) {
            return [];
        }
        $objectTranslations = array_map(function ($objectTranslation) use ($destinationId) {
            $objectTranslation->set('object_id', $destinationId);
            $objectTranslation->setNew(true);
            unset($objectTranslation->id);

            return $objectTranslation;
        }, $objectTranslations);

        return $translationsTable->saveManyOrFail($objectTranslations);
    }
}
