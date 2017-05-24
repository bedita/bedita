<?php
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
namespace BEdita\API\Controller;

use BEdita\Core\Model\Action\SignupUserAction;
use BEdita\Core\Model\Action\SignupUserActivationAction;
use Cake\Routing\Router;

/**
 * Controller for `/signup` endpoint.
 *
 * @since 4.0.0
 *
 */
class SignupController extends AppController
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize()
    {
        parent::initialize();

        if ($this->request->getParam('action') === 'signup' && $this->JsonApi) {
            $this->JsonApi->setConfig('resourceTypes', ['users']);
        }

        if ($this->request->getParam('action') === 'activation' && $this->request->contentType() === 'application/json') {
            $this->RequestHandler->setConfig('inputTypeMap.json', ['json_decode', true], false);
        }
    }

    /**
     * Signup action.
     *
     * @return \Cake\Http\Response
     */
    public function signup()
    {
        $this->request->allowMethod('post');

        $data = $this->request->getData();
        if (!empty($data['password'])) {
            $data['password_hash'] = $data['password'];
            unset($data['password']);
        }

        $urlOptions = $this->request->getData('_meta') ?: [];

        $action = new SignupUserAction();
        $action([
            'data' => $data,
            'urlOptions' => $urlOptions
        ]);

        return $this->response->withStatus(202);
    }

    /**
     * Signup activation action.
     *
     * @return \Cake\Http\Response
     */
    public function activation()
    {
        $this->request->allowMethod('post');

        $action = new SignupUserActivationAction();
        $action(['uuid' => $this->request->getData('uuid')]);

        return $this->response->withStatus(204);
    }
}
