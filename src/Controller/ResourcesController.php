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

namespace BEdita\API\Controller;

use BEdita\API\Model\Action\UpdateAssociatedAction;
use BEdita\Core\Model\Action\AddAssociatedAction;
use BEdita\Core\Model\Action\ListAssociatedAction;
use BEdita\Core\Model\Action\RemoveAssociatedAction;
use BEdita\Core\Model\Action\SetAssociatedAction;
use Cake\Core\InstanceConfigTrait;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * Base controller for CRUD actions on generic resources.
 *
 * @since 4.0.0
 */
abstract class ResourcesController extends AppController
{

    use InstanceConfigTrait;

    /**
     * Configuration.
     *
     * Available configurations are:
     *  - `allowedAssociations`: an associative array of relationships names, and
     *      an array of allowed resource types for that relationship.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->set(['_allowedAssociations' => $this->getConfig('allowedAssociations')]);

        if (isset($this->JsonApi) && $this->request->getParam('action') == 'relationships') {
            $this->JsonApi->setConfig(
                'resourceTypes',
                $this->getConfig(sprintf('allowedAssociations.%s', $this->request->getParam('relationship')))
            );
            $this->JsonApi->setConfig('clientGeneratedIds', true);
        }
    }

    /**
     * Paginated list of resources.
     *
     * @return void
     */
    abstract public function index();

    /**
     * Paginated list of related resources.
     *
     * This method is an alias of {@see self::index()}. However, this is required because of how routes
     * are matched by Cake.
     *
     * @return void
     */
    public function related()
    {
        $this->index();
    }

    /**
     * Find the association corresponding to the relationship name.
     *
     * @param string $relationship Relationship name.
     * @return \Cake\ORM\Association
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if no association could be found.
     */
    protected function findAssociation($relationship)
    {
        if (array_key_exists($relationship, $this->getConfig('allowedAssociations'))) {
            $associations = TableRegistry::get($this->modelClass)->associations();
            foreach ($associations as $association) {
                if ($association->property() === $relationship) {
                    return $association;
                }
            }
        }

        throw new NotFoundException(__('Relationship "{0}" does not exist', $relationship));
    }

    /**
     * View and manage relationships.
     *
     * @return \Cake\Network\Response|null
     */
    public function relationships()
    {
        $this->request->allowMethod(['get', 'post', 'patch', 'delete']);

        $id = $this->request->getParam('id');
        $relationship = $this->request->getParam('relationship');

        $association = $this->findAssociation($relationship);
        // Try to guess reverse association and implicitly add it to displayed associations.
        $reverseAssociation = $association->getTarget()->association($this->modelClass);
        if ($reverseAssociation !== null) {
            $allowAssoc = $reverseAssociation->getProperty();
            $this->set(['_allowedAssociations' => [$allowAssoc => [$allowAssoc]]]);
        }

        switch ($this->request->getMethod()) {
            case 'PATCH':
                $action = new SetAssociatedAction(compact('association'));
                break;

            case 'POST':
                $action = new AddAssociatedAction(compact('association'));
                break;

            case 'DELETE':
                $action = new RemoveAssociatedAction(compact('association'));
                break;

            case 'GET':
            default:
                $action = new ListAssociatedAction(compact('association'));
                $data = $action(['primaryKey' => $id]);

                if ($data instanceof Query) {
                    $data = $this->paginate($data);
                }

                $this->set(compact('data'));
                $this->set([
                        '_type' => $relationship,
                        '_serialize' => ['data'],
                    ]);

                return null;
        }

        $action = new UpdateAssociatedAction(compact('action') + ['request' => $this->request]);
        $count = $action(['primaryKey' => $id]);

        if ($count === false) {
            throw new InternalErrorException(__d('bedita', 'Could not update relationship "{0}"', $relationship));
        }

        if ($count === 0) {
            return $this->response
                ->withHeader('Content-Type', $this->request->contentType())
                ->withStatus(204);
        }

        $this->set(['_serialize' => []]);

        return null;
    }
}
