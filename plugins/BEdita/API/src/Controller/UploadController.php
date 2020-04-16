<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller;

use Cake\Http\Exception\ForbiddenException;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Controller to create objects via file upload.
 *
 * @since 4.2.0
 *
 * @property \BEdita\API\Controller\Component\UploadComponent $Upload
 */
class UploadController extends ObjectsController
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $this->loadComponent('BEdita/API.Upload');
        parent::initialize();
    }

    /**
     * Create new media uploading new stream.
     *
     * @param string $fileName Original file name.
     * @return void
     */
    public function upload($fileName)
    {
        $associations = (array)Hash::get($this->objectType, 'associations');
        if (!in_array('Streams', $associations)) {
            throw new ForbiddenException(__d(
                'bedita',
                'You are not allowed to upload streams on "{0}"',
                $this->objectType->get('name')
            ));
        };

        $this->Table->getConnection()->transactional(function () use ($fileName) {
            $this->request = $this->request
                ->withData('title', $fileName)
                ->withData('type', Inflector::underscore($this->Table->getAlias()));
            // create media object from POST request
            $this->index();

            $objectId = (int)Hash::get($this->viewVars, 'data.id');
            $this->Upload->upload($fileName, $objectId);
        });
    }
}
