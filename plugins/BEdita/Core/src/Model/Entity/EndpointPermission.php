<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Entity;

use Cake\Log\Log;
use Cake\ORM\Entity;

/**
 * EndpointPermission Entity
 *
 * @property int $id
 * @property int $endpoint_id
 * @property int $application_id
 * @property int $role_id
 * @property int $permission
 * @property bool|string $read
 * @property bool|string $write
 *
 * @property \BEdita\Core\Model\Entity\Endpoint|null $endpoint
 * @property \BEdita\Core\Model\Entity\Application|null $application
 * @property \BEdita\Core\Model\Entity\Role|null $role
 *
 * @since 4.0.0
 */
class EndpointPermission extends Entity
{

    /**
     * Bits to shift for read permissions.
     *
     * @var int
     */
    const PERM_READ = 0;

    /**
     * Bits to shift for write permissions.
     *
     * @var int
     */
    const PERM_WRITE = 2;

    /**
     * Do not grant permissions.
     *
     * @var int
     */
    const PERM_NO = 0;

    /**
     * Grant permissions only on my contents.
     *
     * @var int
     */
    const PERM_MINE = 1;

    /**
     * Grant permissions.
     *
     * @var int
     */
    const PERM_YES = 3;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'permission' => false,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'permission',
    ];

    /**
     * {@inheritDoc}
     */
    protected $_virtual = [
        'read',
        'write',
    ];

    /**
     * Decode a permission value.
     *
     * @param int $value Integer representing a permission value.
     * @param \Cake\ORM\Entity|null $entity Entity to be used as context in logs in case of an invalid value.
     * @return bool|string
     */
    public static function decode($value, Entity $entity = null)
    {
        switch ($value) {
            case static::PERM_NO:
                return false;
            case static::PERM_MINE:
                return 'mine';
            case static::PERM_YES:
                return true;
            default:
                Log::alert(sprintf('Invalid permission value "%d"', $value), compact('entity'));

                return false;
        }
    }

    /**
     * Encode a permission value.
     *
     * @param mixed $value Value to be encoded. Can be either a boolean, or the string `mine`.
     * @return int
     */
    public static function encode($value)
    {
        if (is_string($value)) {
            switch (strtolower(trim($value))) {
                case 'mine':
                    return static::PERM_MINE;
            }
        }

        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            return static::PERM_YES;
        }

        return static::PERM_NO;
    }

    /**
     * Setter for permission value.
     *
     * @param int|array $value Permission value. Can be either an integer, or an array with `read` and `write` keys.
     * @return int
     */
    protected function _setPermission($value) {
        if (is_array($value)) {
            $read = static::encode(array_key_exists('read', $value) ? $value['read'] : $this->read);
            $write = static::encode(array_key_exists('write', $value) ? $value['write'] : $this->write);

            return ($read << static::PERM_READ) | ($write << static::PERM_WRITE);
        }

        if (!is_numeric($value)) {
            return 0;
        }

        $value = max(0, min((int)$value, (static::PERM_YES << static::PERM_READ) | (static::PERM_YES << static::PERM_WRITE)));

        return $value;
    }

    /**
     * Human-readable getter for read permission.
     *
     * @return bool|string
     */
    protected function _getRead()
    {
        return static::decode($this->permission >> static::PERM_READ & static::PERM_YES, $this);
    }

    /**
     * Setter for read permission.
     *
     * @param mixed $read Value to be set for read permission.
     * @return bool|string
     */
    protected function _setRead($read)
    {
        $this->permission = compact('read');

        return $this->read;
    }

    /**
     * Human-readable getter for write permission.
     *
     * @return bool|string
     */
    protected function _getWrite()
    {
        return static::decode($this->permission >> static::PERM_WRITE & static::PERM_YES, $this);
    }

    /**
     * Setter for write permission.
     *
     * @param mixed $write Value to be set for write permission.
     * @return bool|string
     */
    protected function _setWrite($write)
    {
        $this->permission = compact('write');

        return $this->write;
    }
}
