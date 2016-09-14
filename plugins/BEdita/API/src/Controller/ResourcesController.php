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

use BEdita\API\Model\Action\UpdateAssociated;
use BEdita\Core\Model\Action\AddAssociated;
use BEdita\Core\Model\Action\ListAssociated;
use BEdita\Core\Model\Action\RemoveAssociated;
use BEdita\Core\Model\Action\SetAssociated;
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

        if (isset($this->JsonApi) && $this->request->param('action') == 'relationships') {
            $this->JsonApi->config(
                'resourceTypes',
                $this->config(sprintf('allowedAssociations.%s', $this->request->param('relationship')))
            );
            $this->JsonApi->config('clientGeneratedIds', true);
        }
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
        if (array_key_exists($relationship, $this->config('allowedAssociations'))) {
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

        $id = $this->request->param('id');
        $relationship = $this->request->param('relationship');

        $Association = $this->findAssociation($relationship);

        switch ($this->request->method()) {
            case 'PATCH':
                $action = new SetAssociated($Association);
                break;

            case 'POST':
                $action = new AddAssociated($Association);
                break;

            case 'DELETE':
                $action = new RemoveAssociated($Association);
                break;

            case 'GET':
            default:
                $action = new ListAssociated($Association);
                $data = $action($id);

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

        $action = new UpdateAssociated($action, $this->request);
        $count = $action($id);

        if ($count === false) {
            throw new InternalErrorException(__('Could not update relationship "{0}"', $relationship));
        }

        if ($count === 0) {
            $this->response->header('Content-Type: ' . $this->request->contentType());
            $this->response->statusCode(204);

            return $this->response;
        }

        $this->set(['_serialize' => []]);

        return null;
    }
}
