<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Entity;

use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Text;

/**
 * AuthProvider Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $auth_class
 * @property string $slug
 * @property string $url
 * @property string $params
 * @property bool $enabled
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \BEdita\Core\Model\Entity\ExternalAuth[] $external_auth
 *
 * @since 4.0.0
 */
class AuthProvider extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * Getter for slug.
     *
     * @return string
     */
    protected function _getSlug()
    {
        list(, $name) = pluginSplit($this->name);

        return mb_strtolower(Text::slug($name));
    }

    /**
     * Get list of roles to be associated to users logging in with this auth provider.
     *
     * @return \BEdita\Core\Model\Entity\Role[]
     */
    public function getRoles()
    {
        $roles = (array)Configure::read(sprintf('Roles.%s', $this->auth_class));
        if (empty($roles)) {
            return [];
        }

        $table = TableRegistry::get('Roles');

        return $table->find()
            ->where(function (QueryExpression $exp) use ($table, $roles) {
                return $exp->in($table->aliasField('name'), $roles);
            })
            ->toArray();
    }

    /**
     * Check auth providers credentials.
     * Provider username MUST match external auth provider response.
     *
     * @param array $providerResponse Provider response in array format
     * @param string $providerUsername Provider username to match
     * @return bool True on success, false on failure
     */
    public function checkAuthorization($providerResponse, $providerUsername)
    {
        $fieldPath = Hash::get($this->get('params'), 'provider_username_field', 'id');
        $userName = Hash::get($providerResponse, (string)$fieldPath);

        return ($userName === $providerUsername);
    }
}
