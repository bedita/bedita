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

use BEdita\Core\Model\Action\BaseAction;
use BEdita\Core\Model\Action\SignupUserAction;
use BEdita\Core\Model\Action\SignupUserActivationAction;
use Cake\Core\Configure;
use Cake\Utility\Inflector;

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

        if ($this->request->contentType() === 'application/json') {
            $this->RequestHandler->setConfig('inputTypeMap.json', ['json_decode', true], false);
        }
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

        $action = $this->createAction('SignupUserAction', SignupUserAction::class);
        $user = $action(compact('data'));

        $this->response = $this->response->withStatus(202);

        $this->set('data', $user);
        $this->set('_serialize', ['data']);
    }

    /**
     * Create requested action class, looking in configuration for custom class.
     * You can set a custom class in configuration like:
     *
     * ```
     * Configure::write('Signup.signupUserAction', '\MyPlugin\Model\Action\MySignupAction')
     * ```
     * Custom class must extend BaseAction.
     *
     * @param string $name Configuration name to look for
     * @param string $default Default action class to use
     * @return \BEdita\Core\Model\Action\BaseAction
     */
    protected function createAction(string $name, string $default): BaseAction
    {
        $config = sprintf('Signup.%s', Inflector::variable($name));
        $actionName = Configure::read($config, $default);

        return new $actionName();
    }

    /**
     * Signup activation action.
     *
     * @return \Cake\Http\Response
     */
    public function activation()
    {
        $this->request->allowMethod('post');

        $action = $this->createAction(
            'SignupUserActivationAction',
            SignupUserActivationAction::class
        );
        $action(['uuid' => $this->request->getData('uuid')]);

        return $this->response->withStatus(204);
    }
}
