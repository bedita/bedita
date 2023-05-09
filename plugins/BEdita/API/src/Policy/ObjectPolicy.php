<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Policy;

use Authorization\IdentityInterface;
use Authorization\Policy\BeforePolicyInterface;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Table\RolesTable;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

/**
 * Object policy.
 *
 * @since 5.10.0
 */
class ObjectPolicy implements BeforePolicyInterface
{
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    public function before(?IdentityInterface $identity, $resource, $action)
    {
        if ($identity === null) {
            return null;
        }

        $roleIds = (array)Hash::extract($identity->getOriginalData(), 'roles.{n}.id');
        if (in_array(RolesTable::ADMIN_ROLE, $roleIds)) {
            return true;
        }

        return null;
    }

    /**
     * Check if $identity can update an object.
     *
     * @param \Authorization\IdentityInterface $identity The identity.
     * @param \BEdita\Core\Model\Entity\ObjectEntity $object The object entity
     * @return bool
     */
    public function canUpdate(IdentityInterface $identity, ObjectEntity $object): bool
    {
        $permsRoles = Hash::extract((array)$object->perms, 'roles');
        if (empty($permsRoles)) { // no permission set
            return true;
        }

        return !empty(array_intersect($permsRoles, $this->extractRolesNames($identity)));
    }

    /**
     * Extract roles'names from identity.
     *
     * @param \Authorization\IdentityInterface $identity The identity.
     * @return array
     */
    protected function extractRolesNames(IdentityInterface $identity): array
    {
        $userRolesNames = (array)Hash::extract($identity->getOriginalData(), 'roles.{n}.name');
        if (!empty($userRolesNames)) {
            return $userRolesNames;
        }

        $userRolesIds = (array)Hash::extract($identity->getOriginalData(), 'roles.{n}.id');
        if (empty($userRolesIds)) {
            return [];
        }

        return $this->fetchTable('Roles')
            ->find('list')
            ->where(['id IN' => $userRolesIds])
            ->toArray();
    }
}
