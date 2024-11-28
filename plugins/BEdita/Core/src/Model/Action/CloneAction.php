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
use BEdita\Core\Utility\Schema;
use BEdita\Core\Utility\Text;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

/**
 * Clone action
 */
class CloneAction extends BaseAction
{
    use LocatorAwareTrait;

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * Author ID.
     *
     * @var int
     */
    protected $authorId;

    /**
     * @inheritDoc
     */
    protected function initialize(array $data)
    {
        $this->Table = $this->getConfig('table');
        $this->authorId = empty(LoggedUser::id()) ? LoggedUser::getUserAdmin()['id'] : LoggedUser::id();
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
        $entity = null;
        $this->Table->getConnection()->transactional(function () use (&$entity, $sourceId, $attributes, $include) {
            $objectType = $this->Table->objectType();
            $options = $objectType->hasAssoc('Streams') ? ['contain' => ['Streams']] : [];
            $source = $this->Table->get($sourceId, $options);
            $entity = $this->cloneEntity($source, $attributes);
            if (!empty($source->get('streams'))) {
                $this->cloneStreams($source->get('streams'), $entity);
            }
            if (in_array('relationships', $include)) {
                $this->cloneRelationships($sourceId, $entity->id);
            }
            if (in_array('translations', $include)) {
                $this->cloneTranslations($sourceId, $entity->id);
            }
        });

        return $entity;
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
        $reset = Schema::getPrimaryFields($schema) + ['created', 'modified'];
        $unique = Schema::getUniqueFields($schema);
        $nullable = Schema::getNullableFields($schema);
        $schemaInfo = compact('reset', 'unique', 'nullable', 'attributes');
        /** @var \BEdita\Core\Model\Entity\ObjectEntity $entity */
        $entity = $this->Table->newEmptyEntity();
        $entityAttributes = $sourceEntity->getVisible();
        foreach ($entityAttributes as $field) {
            $this->setEntityField($schemaInfo, $sourceEntity, $entity, $field);
        }
        $entity->set('created_by', $this->authorId);
        $entity->set('modified_by', $this->authorId);

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
    protected function setEntityField(array $schemaInfo, ObjectEntity $sourceEntity, ObjectEntity &$entity, string $field)
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
        $clonedStream = $streamsTable->clone($streamsTable->get($stream->uuid));

        // add stream to media
        $association = $this->Table->associations()->getByProperty('streams');
        $action = new AddRelatedObjectsAction(compact('association'));
        $relatedEntities = [$clonedStream];
        $action(compact('entity', 'relatedEntities'));

        return $clonedStream;
    }

    /**
     * Clone relationships
     *
     * @param int $sourceId Source object ID
     * @param int $destinationId Destination object ID
     * @return array
     */
    public function cloneRelationships(int $sourceId, int $destinationId): array
    {
        $objectRelationsTable = $this->fetchTable('ObjectRelations');
        $objectRelations = $objectRelationsTable->find()->where(['left_id' => $sourceId]);
        $related = [];
        foreach ($objectRelations as $objectRelation) {
            $newRecord = $objectRelationsTable->newEmptyEntity();
            $newRecord->set('relation_id', $objectRelation->relation_id);
            $newRecord->set('left_id', $destinationId);
            $newRecord->set('right_id', $objectRelation->right_id);
            $newRecord->set('priority', $objectRelation->priority);
            $newRecord->set('params', $objectRelation->params);
            $related[] = $newRecord;
        }
        if (!empty($related)) {
            $objectRelationsTable->saveManyOrFail($related);
        }

        return $related;
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
        $objectTranslations = $translationsTable->find()->where(['object_id' => $sourceId]);
        $translations = [];
        foreach ($objectTranslations as $objectTranslation) {
            $newTranslation = $translationsTable->newEmptyEntity();
            $newTranslation->set('object_id', $destinationId);
            $newTranslation->set('lang', $objectTranslation->lang);
            $newTranslation->set('status', $objectTranslation->status);
            $newTranslation->set('translated_fields', $objectTranslation->translated_fields);
            $newTranslation->set('created_by', $this->authorId);
            $newTranslation->set('modified_by', $this->authorId);
            $translations[] = $newTranslation;
        }
        if (!empty($translations)) {
            $translationsTable->saveManyOrFail($translations);
        }

        return $translations;
    }
}
