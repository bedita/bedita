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
namespace BEdita\API\Controller;

use BEdita\API\Policy\EndpointPolicy;
use BEdita\Core\Model\Action\ActionTrait;

/**
 * Controller for `/signup` endpoint.
 *
 * @since 4.0.0
 */
class SignupController extends AppController
{
    use ActionTrait;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->request = $this->request->withAttribute(EndpointPolicy::DEFAULT_AUTHORIZED, true);

        if (isset($this->JsonApi)) {
            $this->JsonApi->setConfig('parseJson', false);
        }
    }

    /**
     * Indentity not required in `/signup`
     *
     * @return bool
     */
    protected function isIdentityRequired(): bool
    {
        return false;
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

        $action = $this->createAction('SignupUserAction');
        $user = $action(compact('data'));

        $this->response = $this->response->withStatus(202);

        $this->set('data', $user);
        $this->setSerialize(['data']);
    }

    /**
     * Signup activation action.
     *
     * @return \Cake\Http\Response
     */
    public function activation()
    {
        $this->request->allowMethod('post');

        $action = $this->createAction('SignupUserActivationAction');
        $action(['uuid' => $this->request->getData('uuid')]);

        return $this->response->withStatus(204);
    }
}
