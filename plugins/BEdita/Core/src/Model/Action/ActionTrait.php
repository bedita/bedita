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

use Cake\Core\App;
use Cake\Core\Configure;

/**
 * Trait to create actions allowing custom actions load svia configuration.
 *
 * @since 4.2.0
 */
trait ActionTrait
{
    /**
     * Create action class with options, load custom actions via configuration.
     *
     * Action creation rules:
     *
     *  - if passed class is in namespaced format like '\MyNamespace\MyClass' class is created
     *  - a custom class is searched in in `Actions.{$class}` configuration, if set this class is loaded
     *      in namespaced or plugin syntax format
     *  - otherwise class is searched using plugin syntax with 'BEdita/Core` as default in `Model\Action` namespace
     *
     * Examples:
     * ```
     *  // `SignupUserAction` from `\BEdita\Core\Model\Action\` is created, with no other check
     *  $this->createAction('\BEdita\Core\Model\Action\SignupUserAction');
     *
     *  // First we look in `Actions.ListObjectsAction` config for a custom class,
     *  // if nothing is found `BEdita/Core.ListObjectsAction` is used, looking in `Model\Action` namespace
     *  // ['table' => $this->Table] is passed to constructor
     *  $this->createAction('ListObjectsAction', ['table' => $this->Table]);
     *
     *  // Same as above, but if nothing is found in `Actions.SignupUserAction`
     *  // `MyPLugin.SignupUserAction` is used, looking om `Model\Action` namespace
     *  $this->createAction('SignupUserAction', [], 'MyPlugin');
     * ```
     *
     * Configuration examples of default action override
     *
     * ```
     *  'Actions ' => [
     *   'SignupUserAction' => '\MyPlugin\Model\Action\MySignupAction',
     *   'ListObjectsAction' => 'MyPlugin.MyListAction',
     *  ],
     * ```
     *
     * Custom class must extend BaseAction.
     *
     * @param string $class Action class to create
     * @param array $options Action options
     * @param string $prefix Prefix to use, defaults to 'BEdita/Core'
     * @return \BEdita\Core\Model\Action\BaseAction
     */
    protected function createAction(string $class, array $options = [], $prefix = 'BEdita/Core'): BaseAction
    {
        // instantiate class in namespaced format like '\MyNamespace\MyClass'
        $className = App::className($class);
        if ($className !== null) {
            return new $className($options);
        }

        // look in `Actions.{$class}` config or use prefix
        $defaultClass = sprintf('%s.%s', $prefix, $class);
        $class = Configure::read(sprintf('Actions.%s', $class), $defaultClass);
        $className = App::className($class, 'Model/Action');
        if ($className === null) {
            throw new \RuntimeException(__d('bedita', 'Unable to find class "{0}"', $class));
        }

        return new $className($options);
    }
}
