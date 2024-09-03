<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller\Model;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Table;

/**
 * Controller for `/model/relations` endpoint.
 *
 * @since 4.0.0
 * @property \BEdita\Core\Model\Table\RelationsTable $Relations
 */
class RelationsController extends ModelController
{
    /**
     * @inheritDoc
     */
    public $defaultTable = 'Relations';

    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [
            'left_object_types' => ['object_types'],
            'right_object_types' => ['object_types'],
        ],
    ];

    /**
     * @inheritDoc
     */
    protected function prepareInclude($include, ?Table $table = null): array
    {
        $contain = parent::prepareInclude($include, $table);
        if ($this->request->getParam('action') !== 'related') {
            return $contain;
        }

        return array_merge($contain, ['LeftRelations', 'RightRelations']);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceId($id): string
    {
        try {
            $id = $this->Relations->getId($id);
        } catch (RecordNotFoundException $ex) {
            /** \BEdita\Core\Model\Behavior\ResourceNameBehavior $behavior */
            $behavior = $this->Relations->behaviors()->get('ResourceName');
            $behavior->setConfig('field', 'inverse_name');
            $id = $this->Relations->getId($id);
        }

        return (string)$id;
    }
}
