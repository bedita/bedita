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

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\ORM\Inheritance\Table;
use BEdita\Core\Utility\Database;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Validation\Validator;

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
     * DB version supported
     * @var array
     */
    protected $geoDbSupport = ['vendor' => 'mysql', 'version' => '5.7'];

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

        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'title' => 10,
                'description' => 7,
                'body' => 5,
                'address' => 1,
                'locality' => 2,
                'country_name' => 2,
                'region' => 2,
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('coords');

        $validator
            ->allowEmpty('address');

        $validator
            ->allowEmpty('locality');

        $validator
            ->allowEmpty('postal_code');

        $validator
            ->allowEmpty('country_name');

        $validator
            ->allowEmpty('region');

        return $validator;
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
     * $table->find('geo', ['center' => [44.4944183 ,11.3464055]]);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable geo localization conditions.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    public function findGeo(Query $query, array $options)
    {
        $center = !empty($options['center']) ? $options['center'] : [];
        if (empty($center)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => '"center" parameter was not found',
            ]);
        }
        if (!is_array($center)) {
            $center = preg_split('/[\s,]/', $center, 2);
        }
        $center = filter_var_array(array_values($center), FILTER_VALIDATE_FLOAT);
        if (count($center) !== 2 || in_array(false, $center, true) || abs($center[0]) > 180 || abs($center[1]) > 90) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'bad geo data format: ' . implode(' ', $center),
            ]);
        }

        $this->checkGeoDbSupport();

        $center = sprintf('POINT(%s)', implode(' ', $center));
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
            ->select(['distance' => $distanceExpression])
            ->enableAutoFields(true)
            ->where(function (QueryExpression $exp) {
                return $exp->isNotNull($this->aliasField('coords'));
            })
            ->order(['distance' => 'ASC']);
    }

    /**
     * Check if current DB supports geo operations
     *
     * @return void
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    public function checkGeoDbSupport()
    {
        if (!Database::supportedVersion($this->geoDbSupport)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'operation supported only on MySQL 5.7',
            ]);
        }
    }
}
