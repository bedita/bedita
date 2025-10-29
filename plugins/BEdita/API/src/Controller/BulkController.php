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

use Authorization\IdentityInterface;
use BEdita\API\Policy\EndpointPolicy;
use BEdita\Core\Model\Action\ListObjectsAction;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
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
     * Perform bulk operations
     *
     * @param string|null $operation Operation to perform
     * @return void
     */
    public function index(?string $operation): void
    {
        $this->request->allowMethod(['post']);
        if (!in_array($operation, $this->allowedOperations, true)) {
            throw new MethodNotAllowedException(sprintf('Operation %s not allowed', $operation));
        }

        $this->{$operation}();
    }

    /**
     * Edit multiple resources
     * Payload is like:
     * ```json
     * {
     *     "data": {
     *         "attributes": {
     *             "title": "New title"
     *         },
     *         "objects": {
     *            "documents": [100],
     *            "events": [101]
     *         ]
     *     }
     * }
     * ```
     *
     * @return void
     */
    protected function edit(): void
    {
        /** @var \Authorization\IdentityInterface $user */
        $user = $this->Authentication->getIdentity();
        $data = $this->request->getData('data');
        $map = Hash::get($data, 'objects', []);
        $payload = (array)$data['attributes'];
        $saved = [];
        $errors = [];
        $types = array_keys($map);
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        foreach ($types as $type) {
            $objectType = $ObjectTypes->get($type);
            if ($objectType->get('is_abstract') || !$objectType->get('enabled')) {
                $errors = array_merge($errors, array_map(
                    fn($id) => [
                        'id' => (int)$id,
                        'message' => sprintf('Endpoint "%s" cannot be used for bulk edit: abstract or disabled', $type),
                    ],
                    $map[$type]
                ));
                unset($map[$type]);
                continue;
            }
            if (!$this->canAccessEndpoint($user, $type)) {
                $errors = array_merge($errors, array_map(
                    fn($id) => [
                        'id' => (int)$id,
                        'message' => sprintf('User cannot access "%s" endpoint', $type),
                    ],
                    $map[$type]
                ));
                unset($map[$type]);
                continue;
            }
            $typesTable = $this->fetchTable(Inflector::camelize($type));
            $action = new ListObjectsAction(['table' => $typesTable, 'objectType' => $objectType]);
            $query = $action();
            $entities = $query
                ->where([sprintf('%s.id IN', $typesTable->getAlias()) => $map[$type]])
                ->all()
                ->toArray();
            foreach ($entities as $entity) {
                try {
                    if (!$user->can('update', $entity)) {
                        throw new MethodNotAllowedException(sprintf('User cannot save "%s" %s', $type, $entity->get('id')));
                    }
                    $typesTable->getConnection()->transactional(function () use ($entity, $payload, $typesTable, &$saved) {
                        $entity = $typesTable->patchEntity($entity, $payload);
                        $typesTable->saveOrFail($entity);
                        $saved[] = $entity->get('id');
                    });
                } catch (Throwable $e) {
                    $errors[] = [
                        'id' => $entity->get('id'),
                        'message' => $e->getMessage(),
                    ];
                }
            }
        }
        $responseData = [
            'saved' => $saved,
            'errors' => $errors,
        ];
        $this->set('data', $responseData);
        $this->setSerialize(['data']);
    }

    /**
     * Check if endpoint is accessible.
     *
     * @param \Authorization\IdentityInterface|null $user The user identity.
     * @param string $type Object type
     * @return bool
     */
    protected function canAccessEndpoint(?IdentityInterface $user, string $type): bool
    {
        $policy = new EndpointPolicy();

        return $policy->canAccess($user, new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'PATCH',
                'REQUEST_URI' => '/' . $type,
            ],
            'url' => '/' . $type,
        ]));
    }
}
