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
        $this->Auth->getAuthorize('BEdita/API.Endpoint')->setConfig('defaultAuthorized', true);
        $this->JsonApi->setConfig('resourceTypes', ['users']);
    }

    /**
     * Signup action.
     *
     * @return void
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
        $urlOptions += ['activation_url' => Router::url(['_name' => 'api:signup'], true)];

        $action = new SignupUserAction();
        $user = $action->execute([
            'data' => $data,
            'urlOptions' => $urlOptions
        ]);

        $this->response = $this->response->withStatus(202);

        $this->set('data', $user);
        $this->set('_serialize', ['data']);
    }
}
