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
        $title = (string)Hash::get($data, 'title');
        $status = (string)Hash::get($data, 'status');
        $include = (array)Hash::get($data, 'include');
        $entity = null;
        $this->Table->getConnection()->transactional(function () use (&$entity, $sourceId, $title, $status, $include) {
            $source = $this->Table->get($sourceId, ['contain' => ['Streams']]);
            $entity = $this->cloneEntity($source, $title, $status);
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
     * @param string $title Title
     * @param string $status Status
     * @return \Cake\Datasource\EntityInterface
     */
    public function cloneEntity(ObjectEntity $sourceEntity, string $title, string $status): EntityInterface
    {
        $schema = $this->Table->getSchema();
        $reset = Schema::getPrimaryFields($schema) + ['created', 'modified', 'created_by', 'modified_by'];
        $unique = Schema::getUniqueFields($schema);
        /** @var \BEdita\Core\Model\Entity\ObjectEntity $entity */
        $entity = $this->Table->newEmptyEntity();
        $attributes = $sourceEntity->getVisible();
        foreach ($attributes as $field) {
            if (in_array($field, $reset)) {
                continue;
            }
            $source = $sourceEntity->get($field);
            $value = in_array($field, $unique) ? sprintf('%s-copy-%s', $source, date('YmdHis')) : $source;
            $entity->set($field, $value);
        }
        $entity->set('title', !empty($title) ? $title : $sourceEntity->get('title'));
        $entity->set('status', !empty($status) ? $status : 'draft');
        $entity->set('created_by', $this->authorId);
        $entity->set('modified_by', $this->authorId);

        return $this->Table->saveOrFail($entity);
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
