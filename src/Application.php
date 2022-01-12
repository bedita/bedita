<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace BEdita\App;

use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
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
        'ignoreMissing' => true
    ];

    /**
     * {@inheritDoc}
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }

        // Load more plugins here
        $this->addPlugin('BEdita/Core', ['bootstrap' => true]);
        $this->addPlugin('BEdita/API', ['bootstrap' => true, 'routes' => true]);

        $this->addConfigPlugins();
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
    public function middleware($middlewareQueue): \Cake\Http\MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(ErrorHandlerMiddleware::class)

            // Add routing middleware.
            ->add(new RoutingMiddleware($this, '_bedita_core_'))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware());

        return $middlewareQueue;
    }
}
