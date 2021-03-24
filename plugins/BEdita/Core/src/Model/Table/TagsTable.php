<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Model\Validation\Validation;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tags Model
 *
 * @property \BEdita\Core\Model\Table\ObjectCategoriesTable&\Cake\ORM\Association\HasMany $ObjectTags
 *
 * @method \BEdita\Core\Model\Entity\Tag get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Tag newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Tag saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Tag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Tag findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TagsTable extends CategoriesTagsBaseTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ObjectTags', [
            'foreignKey' => 'category_id',
            'className' => 'BEdita/Core.ObjectTags'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $this->validationRules($validator);

        $validator
            ->add('object_type_id', 'requireNull', ['rule' => [Validation::class, 'requireNull']])
            ->add('tree_left', 'requireNull', ['rule' => [Validation::class, 'requireNull']])
            ->add('tree_right', 'requireNull', ['rule' => [Validation::class, 'requireNull']])
            ->add('parent_id', 'requireNull', ['rule' => [Validation::class, 'requireNull']]);

        return $validator;
    }

    /**
     * Add `object_typ_id` condition and remove some fields when retrieved as association.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\ORM\Query $query Query object instance.
     * @param \ArrayObject $options Options array.
     * @param bool $primary Primary flag.
     * @return void
     */
    public function beforeFind(Event $event, Query $query, \ArrayObject $options, $primary)
    {
        $query->andWhere([$this->aliasField('object_type_id') . ' IS NULL']);
        if ($primary) {
            return;
        }
        $this->hideFields($query);
    }
}
