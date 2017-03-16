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

use BEdita\Core\ORM\Inheritance\Table;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
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
     */
    public function findGeo(Query $query, array $options)
    {
        $center = !empty($options['center']) ? $options['center'] : [];
        if (empty($center)) {
            return $query;
        }
        if (is_array($center)) {
            $center = implode(' ', $center);
        } else {
            $center = str_replace(',', ' ', $center);
        }
        $coords = $this->aliasField('coords');
        $distance = 'meta__distance';

        return $query->select([$distance => 'ST_Distance_sphere(ST_GeomFromText(' . $coords . '), ST_GeomFromText(\'POINT(' . $center . ')\'))'])
                ->enableAutoFields(true)
                ->order([$distance => 'ASC']);
    }
}
