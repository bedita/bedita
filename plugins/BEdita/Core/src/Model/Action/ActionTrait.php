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

namespace BEdita\Core\Model\Action;

use Cake\Core\Configure;
use Cake\Utility\Inflector;

/**
 * Trait to create actions with possible
 *
 * @since 4.2.0
 */
trait ActionTrait
{
    /**
     * Create action class with options, load custom actions via configuration.
     *
     * You can override an action via configuration like this:
     *
     *  - if `$config` is set a custom class is searched in `$config` key
     *  - otherwise a custom class is searched in in `Actions.{actionName}` where `{actionName}`
     *      is the variable inflected class name, after namespace removal
     *  - if no custom class is found `$action` class is created
     *
     * Examples:
     * ```
     *  // `SignupUserAction` loaded looking in `Actions.signupUserAction' for a custom class
     *  $this->createAction(SignupUserAction::class);
     *
     *  // `ListObjectsAction` loaded looking in `Actions.sistObjectsAction' for a custom class,
     *  // ['table' => $this->Table] passed to constructor
     *  $this->createAction(ListObjectsAction::class, ['table' => $this->Table]);
     *
     *  // `SignupUserAction` loaded looking in `Signup.signupUserAction' for a custom class
     *  $this->createAction(SignupUserAction::class, [], 'Signup.signupUserAction');
     * ```
     *
     * Configuration example of default action override
     *
     * ```
     *  'Actions ' => [
     *   'signupUserAction', '\MyPlugin\Model\Action\MySignupAction',
     *  ],
     * ```
     *
     * Custom class must extend BaseAction.
     *
     * @param string $action Action class to create
     * @param array $options Action options
     * @param string $config Configuration key to use
     * @return \BEdita\Core\Model\Action\BaseAction
     */
    protected function createAction(string $action, array $options = [], ?string $config = null): BaseAction
    {
        if (empty($config)) {
            $path = explode('\\', $action);
            $name = (string)end($path);
            $config = sprintf('Actions.%s', Inflector::variable($name));
        }
        $action = (string)Configure::read($config, $action);

        return new $action($options);
    }
}
