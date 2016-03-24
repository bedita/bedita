<?php

namespace BEdita\Auth\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use PDO;

/**
 * JSON database type
 */
class JsonType extends Type
{

    /**
     * {@inheritDoc}
     */
    public function toPHP($value, Driver $driver)
    {
        if ($value === null) {
            return null;
        }
        return json_decode($value, true);
    }

    /**
     * {@inheritDoc}
     */
    public function marshal($value)
    {
        if (is_array($value) || $value === null) {
            return $value;
        }
        return json_decode($value, true);
    }

    /**
     * {@inheritDoc}
     */
    public function toDatabase($value, Driver $driver)
    {
        return json_encode($value);
    }

    /**
     * {@inheritDoc}
     */
    public function toStatement($value, Driver $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }
}
