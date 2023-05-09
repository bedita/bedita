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

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Authenticator\JwtAuthenticator;
use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceInterface;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Policy\MapResolver;
use Authorization\Policy\ResolverCollection;
use BEdita\API\Identifier\JwtSubjectIdentifier;
use BEdita\API\Middleware\ApplicationMiddleware;
use BEdita\API\Middleware\BodyParserMiddleware;
use BEdita\API\Middleware\LoggedUserMiddleware;
use BEdita\API\Policy\EndpointPolicy;
use BEdita\API\Policy\ObjectsResolver;
use BEdita\Core\Model\Entity\AuthProvider;
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication as CakeBaseApplication;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application base class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your BEdita application.
 */
abstract class BaseApplication extends CakeBaseApplication implements AuthenticationServiceProviderInterface, AuthorizationServiceProviderInterface
{
    use LocatorAwareTrait;

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

        $this->addPlugin('Authentication');
        $this->addPlugin('Authorization');
        if (Configure::check('Queue')) {
            $this->addPlugin('Cake/Queue');
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
            ->add(new BodyParserMiddleware())

            // Add the AuthenticationMiddleware.
            // It should be after routing and body parser.
            ->add(new AuthenticationMiddleware($this))

            // Setup current BEdita application.
            // It should be after AuthenticationMiddleware.
            ->add(new ApplicationMiddleware([
                'blockAnonymousApps' => Configure::read('Security.blockAnonymousApps', true),
            ]))

            // Setup current logged user.
            // It should be after AuthenticationMiddleware.
            ->add(new LoggedUserMiddleware())

            // Add the AuthorizationMiddleware *after* routing, body parser
            // and authentication middleware.
            ->add(new AuthorizationMiddleware($this));

        return $middlewareQueue;
    }

    /**
     * Returns an authentication service instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request data.
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        if (in_array($request->getUri()->getPath(), ['/auth', '/auth/optout'])) {
            return $this->loginAuthentication($request);
        }

        $service = new AuthenticationService();
        $service->loadAuthenticator('Authentication.Jwt', [
            'algorithm' => Configure::read('Security.jwt.algorithm', 'HS256'),
            'subjectKey' => 'id',
        ]);

        return $service;
    }

    /**
     * Setup authentication service for login and opt-out use cases.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request data.
     * @return \Authentication\AuthenticationService
     */
    protected function loginAuthentication(ServerRequestInterface $request): AuthenticationService
    {
        $service = new AuthenticationService();
        // Load authenticators and identifiers based on `grant_type`
        $body = (array)$request->getParsedBody();
        $grantType = (string)Hash::get($body, 'grant_type');
        $method = sprintf('%sGrantType', Inflector::variable($grantType));
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$service]);
        }
        // Use `auth_provider` if no matching grant type was found
        $provider = (string)Hash::get($body, 'auth_provider');

        return $this->loadAuthProviders($service, $provider);
    }

    /**
     * Returns authorization service instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authorization\AuthorizationServiceInterface
     */
    public function getAuthorizationService(ServerRequestInterface $request): AuthorizationServiceInterface
    {
        $mapResolver = new MapResolver([
            ServerRequest::class => EndpointPolicy::class,
        ]);
        $objectsResolver = new ObjectsResolver();
        $resolver = new ResolverCollection([$mapResolver, $objectsResolver]);

        return new AuthorizationService($resolver);
    }

    /**
     * Handle `password` grant type
     *
     * @param \Authentication\AuthenticationService $service The authentication service
     * @return \Authentication\AuthenticationService
     */
    protected function passwordGrantType(AuthenticationService $service): AuthenticationService
    {
        $service->loadIdentifier('Authentication.Password', [
            'fields' => [
                'username' => 'username',
                'password' => 'password_hash',
            ],
            'resolver' => [
                'className' => 'Authentication.Orm',
                'finder' => 'loginRoles',
            ],
            'passwordHasher' => [
                'className' => 'Authentication.Fallback',
                'hashers' => [
                    'Authentication.Default',
                    [
                        'className' => 'Authentication.Legacy',
                        'hashType' => 'md5',
                    ],
                ],
            ],
        ]);

        // Load authenticators
        $service->loadAuthenticator('Authentication.Form', [
            'loginUrl' => [
                ['_name' => 'api:login'],
                ['_name' => 'api:login:optout'],
            ],
            'urlChecker' => 'Authentication.CakeRouter',
        ]);

        return $service;
    }

    /**
     * Handle `refresh_token` grant type
     *
     * @param \Authentication\AuthenticationService $service The authentication service
     * @return \Authentication\AuthenticationService
     */
    protected function refreshTokenGrantType(AuthenticationService $service): AuthenticationService
    {
        $service->loadIdentifier('Authentication.JwtSubject', [
            'tokenField' => 'id',
            'resolver' => [
                'className' => 'Authentication.Orm',
                'finder' => 'loginRoles',
            ],
        ]);

        $service->loadIdentifier('RenewClientCredentialsJwtSubject', [
            'className' => JwtSubjectIdentifier::class,
            'dataField' => 'app.id',
            'resolver' => [
                'className' => 'Authentication.Orm',
                'userModel' => 'Applications',
                'finder' => 'enabled',
            ],
        ]);

        $service->loadAuthenticator('Authentication.Jwt', [
            'algorithm' => Configure::read('Security.jwt.algorithm') ?: 'HS256',
            'returnPayload' => false,
        ]);

        $service->loadAuthenticator('RenewClientCredentials', [
            'className' => JwtAuthenticator::class,
            'algorithm' => Configure::read('Security.jwt.algorithm') ?: 'HS256',
            'subjectKey' => 'app',
            'returnPayload' => false,
        ]);

        return $service;
    }

    /**
     * Handle `client_credentials` grant type
     *
     * @param \Authentication\AuthenticationService $service The authentication service
     * @return \Authentication\AuthenticationService
     */
    protected function clientCredentialsGrantType(AuthenticationService $service): AuthenticationService
    {
        $service->loadIdentifier('BEdita/API.Application');

        $service->loadAuthenticator('BEdita/API.Application', [
            'loginUrl' => ['_name' => 'api:login'],
            'urlChecker' => 'Authentication.CakeRouter',
            'fields' => [
                'username' => 'client_id',
                'password' => 'client_secret',
            ],
        ]);

        return $service;
    }

    /**
     * Load enabled `auth_providers` from the database
     *
     * @param \Authentication\AuthenticationService $service The authentication service
     * @param string $name Auth Provider name
     * @return \Authentication\AuthenticationService
     */
    protected function loadAuthProviders(AuthenticationService $service, string $name): AuthenticationService
    {
        $this->fetchTable('AuthProviders')
            ->find('enabled')
            ->all()
            ->each(function (AuthProvider $authProvider) use ($service, $name): void {
                if ($authProvider->name === $name) {
                    $service->loadAuthenticator(
                        $authProvider->auth_class,
                        compact('authProvider'),
                    );
                    $service->loadIdentifier(
                        $authProvider->auth_class,
                        compact('authProvider'),
                    );
                }
            });
        if ($service->authenticators()->isEmpty()) {
            throw new UnauthorizedException();
        }

        return $service;
    }
}
