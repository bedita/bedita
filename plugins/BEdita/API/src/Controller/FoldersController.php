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
namespace BEdita\API\Controller;

use BEdita\Core\Model\Action\ListRelatedFoldersAction;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Association;
use Cake\Utility\Hash;

/**
 * Controller for `/folders` endpoint.
 *
 * The main aim is to bridge `parent` API relationship to `parents` BTM association.
 *
 * @since 4.0.0
 */
class FoldersController extends ObjectsController
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Folders';

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'parent' => ['folders'],
            'children' => [],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * `parent` relationship is valid and will return `parents` association.
     * `parents` relationship is not allowed.
     */
    protected function findAssociation($relationship)
    {
        if ($relationship === 'parents') {
            throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
        }

        if ($relationship === 'parent') {
            return $this->Table->getAssociation('Parents');
        }

        return parent::findAssociation($relationship);
    }

    /**
     * {@inheritDoc}
     */
    protected function getAvailableTypes($relationship)
    {
        if ($relationship === 'parent') {
            return ['folders'];
        }
        if ($relationship === 'children') {
            return ['objects'];
        }

        return parent::getAvailableTypes($relationship);
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Model\Action\ListRelatedFoldersAction
     */
    protected function getAssociatedAction(Association $association)
    {
        return new ListRelatedFoldersAction(compact('association'));
    }

    /**
     * {@inheritDoc}
     *
     * Folder with Parents association allows GET and PATCH
     */
    protected function setRelationshipsAllowedMethods(Association $association)
    {
        parent::setRelationshipsAllowedMethods($association);

        if ($association->getName() === 'Parents') {
            $allowedMethods = ['get', 'patch'];
            $this->request->allowMethod($allowedMethods);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function relationships()
    {
        if ($this->request->getParam('relationship') === 'children' && in_array($this->request->getMethod(), ['POST', 'PATCH'])) {
            $this->request = $this->request->withParsedBody($this->getDataSortedByPosition());
        }

        return parent::relationships();
    }

    /**
     * Sort request data using `meta.relation.position`.
     *
     *  The order will be:
     * - null as first
     * - positive desc (i.e. 5, 3, 1)
     * - negative desc (i.e. -1, -3, -5)
     *
     * @return array
     */
    protected function getDataSortedByPosition()
    {
        $data = $this->request->getData();

        usort($data, function ($a, $b) {
            $positionA = Hash::get($a, '_meta.relation.position');
            $positionB = Hash::get($b, '_meta.relation.position');
            if ($positionA === null) {
                return -1;
            }

            if ($positionB === null) {
                return 1;
            }

            $positionA = $this->positionToInt($positionA);
            $positionB = $this->positionToInt($positionB);

            // if they have the same sign then sort desc
            if ($positionA * $positionB > 0) {
                return $positionB - $positionA;
            }

            if ($positionA > 0) {
                return -1;
            }

            return 1;
        });

        return $data;
    }

    /**
     * Given a position as string return its int value.
     *
     * @param string $position The position to parse as integer.
     * @return int
     */
    protected function positionToInt($position)
    {
        if ($position === 'first') {
            return 1;
        }

        if ($position === 'last') {
            return -1;
        }

        return (int)$position;
    }
}
