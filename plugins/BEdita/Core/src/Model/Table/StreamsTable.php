<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Model\Entity\Stream;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Streams Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable|\Cake\ORM\Association\BelongsTo $Objects
 * @method \BEdita\Core\Model\Entity\Stream get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Stream newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Stream[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Stream|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Stream patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Stream[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Stream findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @since 4.0.0
 */
class StreamsTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('streams');
        $this->setPrimaryKey('uuid');
        $this->setDisplayField('uri');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.Uploadable', [
            'files' => [
                [
                    'path' => 'uri',
                    'contents' => 'contents',
                ],
            ],
        ]);

        $this->belongsTo('Objects');
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->uuid('uuid')
            ->add('uuid', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->allowEmptyString('uuid', null, 'create');

        $validator
            ->naturalNumber('version')
            ->allowEmptyString('version', null, 'create');

        $validator
            ->allowEmptyString('uri', null, 'create');

        $validator
            ->requirePresence('file_name', 'create')
            ->notEmptyString('file_name');

        $validator
            ->requirePresence('mime_type', 'create')
            ->notEmptyString('mime_type');

        $validator
            ->naturalNumber('file_size')
            ->allowEmptyString('file_size', null, 'create');

        $validator
            ->ascii('hash_md5')
            ->allowEmptyString('hash_md5', null, 'create');

        $validator
            ->ascii('hash_sha1')
            ->allowEmptyString('hash_sha1', null, 'create');

        $validator
            ->naturalNumber('width')
            ->allowEmptyString('width');

        $validator
            ->naturalNumber('height')
            ->allowEmptyString('height');

        $validator
            ->naturalNumber('duration')
            ->allowEmptyString('duration');

        $validator
            ->notEmptyString('contents')
            ->requirePresence('contents', 'create');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['uri']));
        $rules->add($rules->existsIn(['object_id'], 'Objects'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        return $schema->setColumnType('uuid', 'uuid');
    }

    /**
     * Generate file path before entity is saved.
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\Stream $entity Entity.
     * @return void
     */
    public function beforeSave(EventInterface $event, Stream $entity)
    {
        if (!$entity->isNew()) {
            return;
        }

        if (!$entity->has('uri')) {
            // Fill path where file contents will be stored.
            $entity->uri = $entity->filesystemPath();
        }
    }

    /**
     * Clean up all thumbnails after deleting a stream.
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\Stream $stream Entity.
     * @return void
     */
    public function afterDelete(EventInterface $event, Stream $stream)
    {
        Thumbnail::delete($stream);
    }
}
