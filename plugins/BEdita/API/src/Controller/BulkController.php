<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller;

use BEdita\API\Policy\EndpointPolicy;
use BEdita\Core\Model\Table\RolesTable;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Throwable;

/**
 * Controller for `/bulk` endpoint.
 *
 * @since 5.43.0
 */
class BulkController extends JsonBaseController
{
    /**
     * Allowed operations
     *
     * @var array<string>
     */
    protected array $allowedOperations = ['edit'];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function checkAcceptable(): void
    {
    }

    /**
     * Perform bulk operations
     *
     * @param string|null $operation Operation to perform
     * @return \Cake\Http\Response
     */
    public function index(?string $operation): Response
    {
        $this->request->allowMethod(['post']);
        if (!in_array($operation, $this->allowedOperations, true)) {
            throw new MethodNotAllowedException(sprintf('Operation %s not allowed', $operation));
        }

        return $this->{$operation}();
    }

    /**
     * Edit multiple resources
     *
     * @return \Cake\Http\Response
     */
    protected function edit(): Response
    {
        $ids = (array)$this->request->getData('ids');
        $payload = (array)$this->request->getData('data');
        $objectsTable = $this->fetchTable('Objects');
        $saved = [];
        $errors = [];
        foreach ($ids as $id) {
            try {
                $connection = $objectsTable->getConnection();
                $connection->transactional(function () use ($id, $payload, $objectsTable, &$saved) {
                    $entity = $objectsTable->get($id);
                    $type = $entity->get('type');
                    if (!$this->canSave($type)) {
                        throw new MethodNotAllowedException(sprintf('User cannot save type %s', $type));
                    }
                    $typesTable = $this->fetchTable(Inflector::camelize($type));
                    $entity = $typesTable->get($id);
                    $entity = $typesTable->patchEntity($entity, $payload);
                    $typesTable->saveOrFail($entity);
                    $saved[] = (int)$id;
                });
            } catch (Throwable $e) {
                $errors[] = [
                    'id' => (int)$id,
                    'message' => $e->getMessage(),
                ];
            }
        }
        $data = compact('errors', 'saved');
        $this->set($data);
        $this->setSerialize(['data']);

        return $this->response->withStringBody(json_encode($data));
    }

    /**
     * Check if current user can save entities of given type.
     *
     * @param string $type Object type
     * @return bool
     */
    protected function canSave(string $type): bool
    {
        $roles = (array)$this->Authentication->getIdentityData('roles');
        if (in_array(RolesTable::ADMIN_ROLE, (array)Hash::extract($roles, '{n}.id'))) {
            return true;
        }
        $endpointId = $this->fetchTable('Endpoints')->fetchId(sprintf('/%s', $type));
        if ($endpointId === null) {
            return true;
        }
        $user = compact('roles');
        $permissions = $this->fetchTable('EndpointPermissions')->fetchPermissions($endpointId, $user, false);
        $policy = new EndpointPolicy();

        return $policy->checkPermissions($permissions, false);
    }
}
