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

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * User Entity.
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $password_hash
 * @property bool $blocked
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $last_login
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $last_login_err
 * @property int $num_login_err
 * @property \BEdita\Core\Model\Entity\ExternalAuth[] $external_auth
 * @property \Cake\I18n\Time|\Cake\I18n\FrozenTime $verified
 *
 * @since 4.0.0
 */
class User extends Profile
{
    use LocatorAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);

        $this->setHidden(['password_hash', 'external_auth'], true);
        $this->setAccess(['blocked', 'last_login', 'last_login_err', 'num_login_err', 'verified'], false);
    }

    /**
     * {@inheritDoc}
     *
     * Add `external_auth` info to user meta.
     */
    protected function getMeta()
    {
        $meta = parent::getMeta();
        $meta['external_auth'] = $this->getExternalAuthMeta();

        return $meta;
    }

    /**
     * Get external auth info as
     *
     * ```
     * [
     *     [
     *         'provider' => 'the provider name',
     *         'username' => 'username used for that provider',
     *     ],
     * ]
     * ```
     *
     * @return array|null
     */
    protected function getExternalAuthMeta(): ?array
    {
        if (empty($this->id)) {
            return null;
        }

        $result = $this->getTableLocator()
            ->get('ExternalAuth')
            ->find('user', ['user' => $this->id]);

        if (!$result) {
            return null;
        }

        $extAuth = $result->map(function (ExternalAuth $item) {
            return [
                'provider' => $item->auth_provider->name,
                'username' => $item->provider_username,
            ];
        });

        return $extAuth->toArray();
    }

    /**
     * Password setter. This is an alias for `password_hash`.
     *
     * @param string $password Password to be hashed.
     * @return null
     */
    protected function _setPassword($password)
    {
        $this->password_hash = $password;

        return null;
    }

    /**
     * Password setter.
     *
     * @param string $password Password to be hashed.
     * @return string
     */
    protected function _setPasswordHash($password)
    {
        return (new DefaultPasswordHasher())->hash($password);
    }
}
