<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\App;

use BEdita\API\Middleware\BodyParserMiddleware;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication as CakeBaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application base class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your BEdita application.
 */
abstract class BaseApplication extends CakeBaseApplication
{
    /**
     * Default plugin options
     *
     * @var array
     */
    protected $pluginDefaults = [
        'debugOnly' => false,
        'autoload' => false,
        'bootstrap' => true,
        'routes' => true,
        'ignoreMissing' => true,
    ];

    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }
    }

    /**
     * @return void
     */
    protected function bootstrapCli(): void
    {
        $this->addPlugin('Migrations');
        $this->addOptionalPlugin('Bake');
    }

    /**
     * Add plugins from 'Plugins' configuration
     *
     * @return void
     */
    public function addConfigPlugins(): void
    {
        $plugins = (array)Configure::read('Plugins');
        if (empty($plugins)) {
            return;
        }

        foreach ($plugins as $plugin => $options) {
            if (!is_string($plugin) && is_string($options)) {
                // plugin listed not as assoc array 'PluginName' => [....]
                // but as numeric array like 0 => 'PluginName'
                $plugin = $options;
                $options = [];
            }
            $this->addConfigPlugin($plugin, $options);
        }
    }

    /**
     * Load configured plugin, using defaults and checking `debugOnly`
     *
     * @param string $plugin Plugin name.
     * @param array $options Plugin options.
     * @return void
     */
    protected function addConfigPlugin(string $plugin, array $options): void
    {
        $options = array_merge($this->pluginDefaults, $options);
        if (!$options['debugOnly'] || ($options['debugOnly'] && Configure::read('debug'))) {
            $this->addPlugin($plugin, $options);
        }
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware($middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Add routing middleware.
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware());

        return $middlewareQueue;
    }
}
