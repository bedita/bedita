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

use BEdita\Core\Model\Validation\LocationsValidator;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Database\Expression\FunctionExpression;
use Cake\ORM\Query;

/**
 * Locations Model
 *
 * @method \BEdita\Core\Model\Entity\Location get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Location newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location findOrCreate($search, callable $callback = null, $options = [])
 */
class LocationsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    protected $_validatorClass = LocationsValidator::class;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('locations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->extensionOf('Objects', [
            'className' => 'BEdita/Core.Objects'
        ]);

        $this->addBehavior('BEdita/Core.Relations');
    }

    /**
     * Find objects by geo coordinates.
     * Create a query to filter objects using geo data: location objects are
     * ordered by distance, from the nearest to the farthest using a center geo point.
     *
     * Accepted options are:
     *   - 'center' with point coordinates, latitude and longitude
     *
     * Examples
     * ```
     * // find location objects near a given center, string with comma separated values or array
     * $table->find('geo', ['center' => '44.4944183,11.3464055']);
     * $table->find('geo', ['center' => [44.4944183, 11.3464055]]);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable geo localization conditions.
     * @return \Cake\ORM\Query
     */
    public function findGeo(Query $query, array $options)
    {
        $center = !empty($options['center']) ? $options['center'] : [];
        if (empty($center)) {
            return $query;
        }
        if (is_array($center)) {
            $center = implode(' ', $center);
        }
        $center = sprintf('POINT(%s)', str_replace(',', ' ', $center));
        $distance = 'meta__distance';

        $distanceExpression = new FunctionExpression(
            'ST_Distance_sphere',
            [
                new FunctionExpression('ST_GeomFromText', [$this->aliasField('coords') => 'identifier']),
                new FunctionExpression('ST_GeomFromText', [$center]),
            ],
            [],
            'float'
        );

        return $query
            ->select([$distance => $distanceExpression])
            ->enableAutoFields(true)
            ->order([$distance => 'ASC']);
    }
}
