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

namespace BEdita\Core\Model\Validation;

/**
 * Validator for locations.
 *
 * @since 4.0.0
 */
class LocationsValidator extends ObjectsValidator
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();

        $this->setProvider('locations', self::class);

        $this
            ->add('coords', 'valid', [
                'rule' => 'checkWkt',
                'provider' => 'locations',
            ])
            ->allowEmpty('coords')

            ->allowEmpty('address')

            ->allowEmpty('locality')

            ->allowEmpty('postal_code')

            ->allowEmpty('country_name')

            ->allowEmpty('region');
    }

    /**
     * Check that a value is a valid WKT string.
     *
     * @param mixed $value WKT to be validated.
     * @return string|true
     */
    public static function checkWkt($value)
    {
        static $regex = '/^POINT\((?P<lng>\-?[0-9\.]+) (?P<lat>\-?[0-9\.]+)\)$/i';

        if (!is_string($value) || !preg_match($regex, $value, $matches)) {
            return 'invalid Well-Known Text';
        }

        return static::checkCoordinates([$matches['lat'], $matches['lng']]);
    }

    /**
     * Check that coordinates are valid.
     *
     * @param mixed $value Coordinates to be validated.
     * @return string|true
     */
    public static function checkCoordinates($value)
    {
        if (!is_array($value) || count($value) !== 2 || !isset($value[0]) || !isset($value[1])) {
            return 'coordinates must be a pair of values';
        }

        $lat = filter_var($value[0], FILTER_VALIDATE_FLOAT);
        if ($lat === false || abs($lat) > 90) {
            return 'invalid latitude';
        }

        $lng = filter_var($value[1], FILTER_VALIDATE_FLOAT);
        if ($lat === false || $lng > 180 || $lng <= -180) {
            return 'invalid longitude';
        }

        return true;
    }
}
