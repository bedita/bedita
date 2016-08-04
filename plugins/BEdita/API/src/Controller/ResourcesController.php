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
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

/**
 * Base controller for CRUD actions on generic resources.
 *
 * @since 4.0.0
 */
abstract class ResourcesController extends AppController
{

    /**
     * View and manage relationships.
     *
     * @return void
     */
    public function relationships()
    {
        $this->request->allowMethod(['get', 'post', 'patch', 'delete']);

        $id = $this->request->param('id');
        $relationship = $this->request->param('relationship');

        $Table = TableRegistry::get($this->modelClass);

        $Association = null;
        foreach ($Table->associations() as $assoc) {
            if ($assoc->property() == $relationship) {
                $Association = $assoc;
                break;
            }
        }
        if ($Association === null) {
            throw new NotFoundException(__('Relationship {0} does not exist', [$relationship]));
        }

        $coreAction = null;
        switch ($this->request->method()) {
            case 'PATCH':
                $coreAction = new SetAssociated($Association);
                break;

            case 'POST':
                $coreAction = new AddAssociated($Association);
                break;

            case 'DELETE':
                $coreAction = new RemoveAssociated($Association);
        }

        if ($coreAction !== null) {
            $action = new UpdateAssociated($coreAction, $this->request);

            if (!$action($id)) {
                throw new InternalErrorException(__('Could not update relationship {0}', [$relationship]));
            }
        }

        $action = new ListAssociated($Association);
        $associatedEntities = $action($id);

        $this->set([
            $relationship => $associatedEntities,
            '_type' => $relationship,
            '_serialize' => [$relationship],
        ]);
    }
}
