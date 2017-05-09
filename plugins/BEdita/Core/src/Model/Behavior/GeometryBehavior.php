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

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query as DatabaseQuery;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\Utility\Hash;

/**
 * Behavior for geographic searches.
 *
 * @since 4.0.0
 */
class GeometryBehavior extends Behavior
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'distanceFunction' => 'ST_Distance_Sphere',
        'field' => 'coords',
        'implementedFinders' => [
            'geo' => 'findGeo',
        ],
    ];

    /**
     * Store geometry support for current connection.
     *
     * @var bool
     */
    protected $hasGeoSupport;

    /**
     * Get database expression to find distance between two points on a spheroid.
     *
     * @param string|float[] $point1 First point. Can be either a field name or a pair of floats.
     * @param string|float[] $point2 Second point. Can be either a field name or a pair of floats.
     * @return \Cake\Database\Expression\FunctionExpression
     */
    protected function getDistanceExpression($point1, $point2)
    {
        $point1 = is_string($point1) ? [$point1 => 'identifier'] : [sprintf('POINT (%s %s)', ...array_reverse($point1))];
        $point2 = is_string($point2) ? [$point2 => 'identifier'] : [sprintf('POINT (%s %s)', ...array_reverse($point2))];

        return new FunctionExpression(
            $this->getConfig('distanceFunction'),
            [
                new FunctionExpression('ST_GeomFromText', $point1),
                new FunctionExpression('ST_GeomFromText', $point2),
            ],
            [],
            'float'
        );
    }

    /**
     * Parse coordinates.
     *
     * @param mixed $point Coordinates.
     * @return float[]
     * @throws \BEdita\Core\Exception\BadFilterException Throws an exception if value could not be parsed into coords.
     */
    public static function parseCoordinates($point)
    {
        if (empty($point)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'missing or empty coordinates',
            ]);
        }
        if (!is_array($point)) {
            $point = preg_split('/[\s,]/', (string)$point, 2);
        }
        $point = filter_var_array(array_values($point), FILTER_VALIDATE_FLOAT);
        if (count($point) !== 2 || in_array(false, $point, true) || abs($point[0]) > 180 || abs($point[1]) > 90) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'bad geo data format: ' . implode(' ', $point),
            ]);
        }

        return $point;
    }

    /**
     * Check if current DB supports geo operations.
     *
     * @return void
     * @throws \BEdita\Core\Exception\BadFilterException Throws an exception if GIS SQL functions are not available.
     */
    public function checkGeoSupport()
    {
        $connection = $this->getTable()->getConnection();
        if (!isset($this->hasGeoSupport)) {
            try {
                $query = new DatabaseQuery($connection);
                $query = $query->select([
                    'dist' => $this->getDistanceExpression([0, 0], [1, 1]),
                ]);
                $query->execute();

                $this->hasGeoSupport = true;
            } catch (\PDOException $e) {
                $this->hasGeoSupport = false;
            }
        }

        if ($this->hasGeoSupport !== true) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'operation not supported on current database',
            ]);
        }
    }

    /**
     * Find objects by geo coordinates.
     *
     * Create a query to filter objects using geo data: location objects are
     * ordered by distance, from the nearest to the farthest using a center geo point.
     *
     * Accepted options are:
     *   - 'center' with point coordinates, latitude and longitude
     *   - 'from' with point coordinates, latitude and longitude
     *   - 'radius' with positive decimal number
     *
     * ### Examples
     *
     * ```php
     * // Find location objects near a given center, either a string with comma separated values or an array.
     * $table->find('geo', ['center' => '44.4944183,11.3464055']);
     * $table->find('geo', ['center' => [44.4944183, 11.3464055]]);
     *
     * // Find location objects within a radius of 10 kilometers from the given range.
     * $table->find('geo', ['center' => [44.4944183, 11.3464055], 'radius' => 10]);
     *
     * // Find location objects that are close to a center, but compute distances from another center.
     * $table->find('geo', ['center' => [44.4944183, 11.3464055], 'from' => [11.3464055, 44.4944183]]);
     * ```
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array of acceptable geo localization conditions.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    public function findGeo(Query $query, array $options)
    {
        $center = static::parseCoordinates(Hash::get($options, 'center'));
        $distanceCenter = static::parseCoordinates(Hash::get($options, 'from', $center));
        $radius = filter_var(Hash::get($options, 'radius'), FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
        $field = $this->getTable()->aliasField($this->getConfig('field'));

        $this->checkGeoSupport();

        return $query
            ->select(['distance' => $this->getDistanceExpression($field, $distanceCenter)])
            ->enableAutoFields(true)
            ->where(function (QueryExpression $exp) use ($center, $field, $radius) {
                if ($radius !== false) {
                    $exp = $exp->lte($this->getDistanceExpression($field, $center), $radius);
                }

                return $exp->isNotNull($field);
            })
            ->orderAsc($this->getDistanceExpression($field, $center));
    }
}
