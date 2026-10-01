<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Table\ObjectsBaseTable as Table;
use BEdita\Core\Model\Validation\LocationsValidator;

/**
 * Locations Model
 *
 * @method \BEdita\Core\Model\Entity\Location get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\Location newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\Location[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Location>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Location> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Location>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\Location> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \BEdita\Core\Model\Behavior\RelationsBehavior
 */
class LocationsTable extends Table
{
    /**
     * @inheritDoc
     */
    protected string $_validatorClass = LocationsValidator::class;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('locations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->extensionOf('Objects');

        $this->addBehavior('BEdita/Core.Geometry');

        $this->setupSimpleSearch([
            'fields' => [
                'title',
                'description',
                'body',
                'address',
                'locality',
                'country_name',
                'region',
            ],
        ]);
    }
}
