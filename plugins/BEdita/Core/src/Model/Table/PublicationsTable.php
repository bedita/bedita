<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Table\ObjectsBaseTable as Table;
use Cake\Validation\Validator;

/**
 * Publications Model
 *
 * @method \BEdita\Core\Model\Entity\Publication get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Publication newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Publication[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Publication>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Publication> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Publication>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Publication[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Publication> deleteManyOrFail(iterable $entities, array $options = [])
 */
class PublicationsTable extends Table
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('publications');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->extensionOf('Objects');
    }

    /**
     * @inheritDoc
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->nonNegativeInteger('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->allowEmptyString('public_name');

        $validator
            ->allowEmptyString('public_url');

        $validator
            ->allowEmptyString('staging_url');

        $validator
            ->allowEmptyString('stats_code');

        return $validator;
    }
}
