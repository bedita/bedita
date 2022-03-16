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

use BEdita\Core\Model\Entity\ObjectCategory;

/**
 * ObjectTags Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsTo $Objects
 * @property \BEdita\Core\Model\Table\TagsTable&\Cake\ORM\Association\BelongsTo $Tags
 * @method \BEdita\Core\Model\Entity\ObjectCategory get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectCategory findOrCreate($search, callable $callback = null, $options = [])
 */
class ObjectTagsTable extends ObjectCategoriesTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setEntityClass(ObjectCategory::class);

        $this->belongsTo('Tags', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Tags',
        ]);
        $this->associations()->remove('Categories');
    }
}
