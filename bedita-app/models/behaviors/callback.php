<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2014 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * Migration behavior to translate basic Cake callbacks to BeCallbackManager events.
 *
 * When this behavior is attached to a model `MyModel`, all enabled behaviors with a name like
 * `MyModelSomethingCallbackBehavior` (where `Something` can be replaced with anything you want)
 * will be automatically attached to `MyModel`'s events.
 *
 * You can also attach a callback behavior to *all* object type using the `BEObject` reserved name:
 * for instance, you might create a `BEObjectSomethingCallbackBehavior` to attach callback operations
 * to every core and plugin object type at once.
 *
 * If this behavior is attached with configuration key `callbackManager` explicitely set to false,
 * callbacks behaviors will be attached using CakePHP's built in associations.
 * Otherwise, public callback behaviors' methods will be treated as listeners for corresponding events,
 * thus allowing finer control on event propagation and details, as well as detachment of
 * single methods within a behavior.
 *
 * @see BeCallbackManager
 */
class CallbackBehavior extends ModelBehavior {
    /**
     * Standard configuration.
     *
     * @var array
     * @access private
     */
    private $stdConfig = array(
        'callbackManager' => true,
        '_behaviors' => array(),
        '_listeners' => array(),
    );

    /**
     * Configuration.
     *
     * @var array
     * @access protected
     */
    protected $config = array();

    /**
     * Callback Manager.
     *
     * @var BeCallbackManager
     * @access private
     */
    private $manager = null;

    /**
     * Behaviors list.
     *
     * @var array
     * @access private
     */
    private $behaviors = null;

    /**
     * Initialize and return callback manager.
     *
     * @return BeCallbackManager
     * @access private
     */
    private function manager() {
        if (is_null($this->manager)) {
            $this->manager = BeLib::getObject('BeCallbackManager');
        }
        return $this->manager;
    }

    /**
     * Load and return behaviors list.
     *
     * @return array
     * @access private
     */
    private function behaviors() {
        if (is_null($this->behaviors)) {
            $this->behaviors = App::objects('behavior');
        }
        return $this->behaviors;
    }

    /**
     * Setup callback behavior.
     *
     * @param Model $model Model.
     * @param array $config Configuration.
     * @access public
     */
    public function setup(Model &$model, array $config = array()) {
        if (array_key_exists($model->alias, $this->config)) {
            // Already configured.
            return;
        }

        $objectTypes = Set::classicExtract(Configure::read('objectTypes'), '{n}.model') ?: array();
        if (!in_array($model->alias, $objectTypes)) {
            // Not a BE Object.
            return;
        }

        // Setup config.
        $this->config[$model->alias] = array_merge($this->stdConfig, $config);

        // Filter behaviors by their name, and attach them to model.
        // #723 - Use `BEObject` as a reserved name for wildcard callbacks.
        $behaviors = preg_grep("/^(?:BEObject|{$model->alias})\w+Callback$/", $this->behaviors());
        foreach ($behaviors as $beh) {
            if ($this->config[$model->alias]['callbackManager']) {
                // Attach listeners using callback manager.

                // Import.
                $import = App::import('Behavior', $beh);
                $class = $beh . 'Behavior';
                if (!$import || !class_exists($class)) {
                    // Failed.
                    continue;
                }

                // Initialize behavior.
                $behavior = null;
                if (ClassRegistry::isKeySet($class)) {
                    $behavior = ClassRegistry::getObject($class);
                } else {
                    $behavior = new $class();
                    ClassRegistry::addObject($class, $behavior);
                }
                $behavior->setup($model);
                // Bind listeners.
                $methods = array_diff(get_class_methods($behavior), array(
                    'cakeError', 'log', 'requestAction', 'toString',
                    'Object', 'dispatchMethod', 'onError', 'setup', 'cleanup',
                ));
                foreach ($methods as $meth) {
                    if ($meth[0] == '_') {
                        continue;
                    }

                    $eventName = $model->alias . '.' . Inflector::camelize($meth);
                    $listener = array($class, $meth);
                    $this->manager()->bind($eventName, $listener);

                    if (!array_key_exists($eventName, $this->config[$model->alias]['_listeners'])) {
                        $this->config[$model->alias]['_listeners'][$eventName] = array();
                    }
                    array_push($this->config[$model->alias]['_listeners'][$eventName], $listener);
                }
                unset($behavior);
            } else {
                // Simply attach behavior.
                $model->Behaviors->attach($beh);
                array_push($this->config[$model->alias]['_behaviors'], $beh);
            }
        }
    }

    /**
     * Detach callback behavior.
     *
     * @param Model $model Model.
     * @access public
     */
    public function cleanup(Model &$model) {
        foreach ($this->config[$model->alias]['_behaviors'] as $beh) {
            // Detach previously attached behaviors.
            $model->Behaviors->detach($beh);
        }
        foreach ($this->config[$model->alias]['_listeners'] as $eventName => $callbacks) {
            // Unbind previously binded listeners.
            foreach ($callbacks as $callback) {
                $this->manager()->unbind($eventName, $callback);
            }
        }
        unset($this->config[$model->alias]);
    }

    /**
     * Migration helper for event `beforeFind`.
     *
     * @param Model $model Model.
     * @param array $query Query.
     * @return mixed Result.
     * @access public
     */
    public function beforeFind(Model &$model, array $query) {
        $evt = $this->manager()->trigger("{$model->alias}.BeforeFind", array(
            'model' => &$model,
            'query' => $query,
        ));
        return $evt->result;
    }

    /**
     * Migration helper for event `afterFind`.
     *
     * @param Model $model Model.
     * @param mixed $results Results.
     * @param bool $primary Primary.
     * @return mixed Result.
     * @access public
     */
    public function afterFind(Model &$model, $results, $primary) {
        $evt = $this->manager()->trigger("{$model->alias}.AfterFind", array(
            'model' => &$model,
            'results' => $results,
            'primary' => $primary,
        ));
        return $evt->result;
    }

    /**
     * Migration helper for event `beforeValidate`.
     *
     * @param Model $model Model.
     * @return mixed Result.
     * @access public
     */
    public function beforeValidate(Model &$model) {
        $evt = $this->manager()->trigger("{$model->alias}.BeforeValidate", array(
            'model' => &$model,
        ));
        return $evt->result;
    }

    /**
     * Migration helper for event `beforeSave`.
     *
     * @param Model $model Model.
     * @return mixed Result.
     * @access public
     */
    public function beforeSave(Model &$model) {
        $evt = $this->manager()->trigger("{$model->alias}.BeforeSave", array(
            'model' => &$model,
        ));
        return $evt->result;
    }

    /**
     * Migration helper for event `afterSave`.
     *
     * @param Model $model Model.
     * @param bool $created Created.
     * @return mixed Result.
     * @access public
     */
    public function afterSave(Model &$model, $created) {
        $evt = $this->manager()->trigger("{$model->alias}.AfterSave", array(
            'model' => &$model,
            'created' => $created,
        ));
        return $evt->result;
    }

    /**
     * Migration helper for event `beforeDelete`.
     *
     * @param Model $model Model.
     * @param bool $cascade Cascade.
     * @return mixed Result.
     * @access public
     */
    public function beforeDelete(Model &$model, $cascade = true) {
        $evt = $this->manager()->trigger("{$model->alias}.BeforeDelete", array(
            'model' => &$model,
            'cascade' => $cascade,
        ));
        return $evt->result;
    }

    /**
     * Migration helper for event `afterDelete`.
     *
     * @param Model $model Model.
     * @return mixed Result.
     * @access public
     */
    public function afterDelete(Model &$model) {
        $evt = $this->manager()->trigger("{$model->alias}.AfterDelete", array(
            'model' => &$model,
        ));
        return $evt->result;
    }
}
