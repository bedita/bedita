<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
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
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * This behavior adds finders for object's status filtering.
 *
 * {@inheritDoc}
 */
class StatusBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'implementedMethods' => [
            'checkStatus' => 'checkStatus',
        ],
        'implementedFinders' => [
            'statusLevel' => 'findStatusLevel',
        ],
        'field' => 'status',
    ];

    /**
     * Check that `status` is consistent with `level` configuration.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity being saved.
     * @return void
     * @throws \Cake\Http\Exception\BadRequestException
     */
    public function checkStatus(EntityInterface $entity): void
    {
        if ($entity->isNew() || !Configure::check('Status.level') || !$entity->isDirty('status')) {
            return;
        }
        $level = Configure::read('Status.level');
        $status = $entity->get('status');
        if (($level === 'on' && $status !== 'on') || ($level === 'draft' && $status === 'off')) {
            throw new BadRequestException(__d(
                'bedita',
                'Status "{0}" is not consistent with configured Status.level "{1}"',
                $status,
                $level
            ));
        }
    }

    /**
     * Finder for objects based on status level.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Object status level.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException Throws an exception if an invalid set of options is passed to
     *      the finder.
     */
    public function findStatusLevel(Query $query, array $options)
    {
        if (empty($options[0])) {
            throw new BadFilterException(__d('bedita', 'Invalid options for finder "{0}"', 'status'));
        }

        $level = $options[0];
        $field = $this->getConfigOrFail('field');
        switch ($level) {
            case 'on':
                return $query->where([
                    $this->table()->aliasField($field) => 'on',
                ]);

            case 'draft':
                return $query->where(function (QueryExpression $exp) use ($field) {
                    return $exp->in($this->table()->aliasField($field), ['on', 'draft']);
                });

            case 'off':
            case 'all':
                return $query;

            default:
                throw new BadFilterException(__d('bedita', 'Invalid options for finder "{0}"', 'status'));
        }
    }
}
