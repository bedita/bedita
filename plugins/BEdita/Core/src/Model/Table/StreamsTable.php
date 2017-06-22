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

use BEdita\Core\Model\Entity\Stream;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Streams Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable|\Cake\ORM\Association\BelongsTo $Objects
 *
 * @method \BEdita\Core\Model\Entity\Stream get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Stream newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Stream[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Stream|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Stream patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Stream[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Stream findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 *
 * @since 4.0.0
 */
class StreamsTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
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
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('uuid')
            ->add('uuid', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->allowEmpty('uuid', 'create');

        $validator
            ->naturalNumber('version')
            ->allowEmpty('version', 'create');

        $validator
            ->allowEmpty('uri', 'create');

        $validator
            ->requirePresence('file_name', 'create')
            ->notEmpty('file_name');

        $validator
            ->allowEmpty('mime_type', 'create');

        $validator
            ->naturalNumber('file_size')
            ->allowEmpty('file_size', 'create');

        $validator
            ->ascii('hash_md5')
            ->allowEmpty('hash_md5', 'create');

        $validator
            ->ascii('hash_sha1')
            ->allowEmpty('hash_sha1', 'create');

        $validator
            ->notEmpty('contents')
            ->requirePresence('contents', 'create');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['uri']));
        $rules->add($rules->existsIn(['object_id'], 'Objects'));

        return $rules;
    }

    /**
     * Generate file path before entity is saved.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \BEdita\Core\Model\Entity\Stream $entity Entity.
     * @return void
     */
    public function beforeSave(Event $event, Stream $entity)
    {
        if (!$entity->isNew()) {
            return;
        }

        if (!$entity->has('uri')) {
            // Fill path where file contents will be stored.
            $entity->uri = $entity->filesystemPath();
        }
    }
}
