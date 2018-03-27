<?php
return [
    /**
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /**
     * Configure basic information about the application.
     *
     * - namespace - The namespace to find app classes under.
     * - defaultLocale - The default locale for translation, formatting currencies and numbers, date and time.
     * - encoding - The encoding used for HTML + database connections.
     * - base - The base directory the app resides in. If false this
     *   will be auto detected.
     * - dir - Name of app directory.
     * - webroot - The webroot directory.
     * - wwwRoot - The file path to webroot.
     * - baseUrl - To configure CakePHP to *not* use mod_rewrite and to
     *   use CakePHP pretty URLs, remove these .htaccess
     *   files:
     *      /.htaccess
     *      /webroot/.htaccess
     *   And uncomment the baseUrl key below.
     * - fullBaseUrl - A base URL to use for absolute links.
     * - imageBaseUrl - Web path to the public images directory under webroot.
     * - cssBaseUrl - Web path to the public css directory under webroot.
     * - jsBaseUrl - Web path to the public js directory under webroot.
     * - paths - Configure paths for non class based resources. Supports the
     *   `plugins`, `templates`, `locales` subkeys, which allow the definition of
     *   paths for plugins, view templates and locale files respectively.
     */
    'App' => [
        'namespace' => 'BEdita\App',
        'encoding' => env('APP_ENCODING', 'UTF-8'),
        'defaultLocale' => env('APP_DEFAULT_LOCALE', 'en_US'),
        'base' => env('BEDITA_BASE_URL', false),
        'dir' => 'src',
        'webroot' => 'webroot',
        'wwwRoot' => WWW_ROOT,
        // 'baseUrl' => env('SCRIPT_NAME'),
        'fullBaseUrl' => false,
        'imageBaseUrl' => 'img/',
        'cssBaseUrl' => 'css/',
        'jsBaseUrl' => 'js/',
        'paths' => [
            'plugins' => [ROOT . DS . 'plugins' . DS],
            'templates' => [APP . 'Template' . DS],
            'locales' => [APP . 'Locale' . DS],
        ],
    ],

    /**
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     * - jwt - Duration and algorithm for JSON Web Tokens.
     *   By default, `duration` is `'+20 minutes'`, and `algorithm` is `'HS256'`.
     * - blockAnonymousApps - Are anonymous applications (i.e. requests without an api key) forbidden?
     * - blockAnonymousUsers - Are unauthenticated users requests blocked by default?
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
        // 'jwt' => [
        //     'duration' => '+20 minutes',
        //     'algorithm' => 'HS256',
        // ],
        // 'blockAnonymousApps' => true,
        'blockAnonymousUsers' => false,
    ],

    /**
     * Apply timestamps with the last modified time to static assets (js, css, images).
     * Will append a querystring parameter containing the time the file was modified.
     * This is useful for busting browser caches.
     *
     * Set to true to apply timestamps when debug is true. Set to 'force' to always
     * enable timestamping regardless of debug value.
     */
    'Asset' => [
        // 'timestamp' => true,
    ],

    /**
     * Configure the cache adapters.
     */
    'Cache' => [
        'default' => [
            'className' => 'File',
            'path' => CACHE,
            'url' => env('CACHE_DEFAULT_URL', null),
        ],

        /**
         * Configure the cache used for object types caching.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         */
        '_bedita_object_types_' => [
            'className' => 'File',
            'prefix' => 'bedita_object_types_',
            'path' => CACHE . 'object_types/',
            'serialize' => true,
            'duration' => '+1 year',
            'url' => env('CACHE_BEDITAOBJECTTYPES_URL', null),
        ],

        /**
         * Configure the cache used for general framework caching.
         * Translation cache files are stored with this configuration.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         * If you set 'className' => 'Null' core cache will be disabled.
         */
        '_cake_core_' => [
            'className' => 'File',
            'prefix' => 'myapp_cake_core_',
            'path' => CACHE . 'persistent/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKECORE_URL', null),
        ],

        /**
         * Configure the cache for model and datasource caches. This cache
         * configuration is used to store schema descriptions, and table listings
         * in connections.
         * Duration will be set to '+2 minutes' in bootstrap.php when debug = true
         */
        '_cake_model_' => [
            'className' => 'File',
            'prefix' => 'myapp_cake_model_',
            'path' => CACHE . 'models/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKEMODEL_URL', null),
        ],
    ],

    /**
     * Configure the Error and Exception handlers used by your application.
     *
     * By default errors are displayed using Debugger, when debug is true and logged
     * by Cake\Log\Log when debug is false.
     *
     * In CLI environments exceptions will be printed to stderr with a backtrace.
     * In web environments an HTML page will be displayed for the exception.
     * With debug true, framework errors like Missing Controller will be displayed.
     * When debug is false, framework errors will be coerced into generic HTTP errors.
     *
     * Options:
     *
     * - `errorLevel` - int - The level of errors you are interested in capturing.
     * - `trace` - boolean - Whether or not backtraces should be included in
     *   logged errors/exceptions.
     * - `log` - boolean - Whether or not you want exceptions logged.
     * - `exceptionRenderer` - string - The class responsible for rendering
     *   uncaught exceptions. If you choose a custom class you should place
     *   the file for that class in src/Error. This class needs to implement a
     *   render method.
     * - `skipLog` - array - List of exceptions to skip for logging. Exceptions that
     *   extend one of the listed exceptions will also be skipped for logging.
     *   E.g.:
     *   `'skipLog' => ['Cake\Network\Exception\NotFoundException', 'Cake\Network\Exception\UnauthorizedException']`
     * - `extraFatalErrorMemory` - int - The number of megabytes to increase
     *   the memory limit by when a fatal error is encountered. This allows
     *   breathing room to complete logging or error handling.
     */
    'Error' => [
        'errorLevel' => E_ALL,
        'exceptionRenderer' => 'BEdita\API\Error\ExceptionRenderer',
        'skipLog' => [],
        'log' => true,
        'trace' => true,
    ],

    /**
     * Email configuration.
     *
     * By defining transports separately from delivery profiles you can easily
     * re-use transport configuration across multiple profiles.
     *
     * You can specify multiple configurations for production, development and
     * testing.
     *
     * Each transport needs a `className`. Valid options are as follows:
     *
     *  Mail   - Send using PHP mail function
     *  Smtp   - Send using SMTP
     *  Debug  - Do not send the email, just return the result
     *
     * You can add custom transports (or override existing transports) by adding the
     * appropriate file to src/Mailer/Transport. Transports should be named
     * 'YourTransport.php', where 'Your' is the name of the transport.
     */
    'EmailTransport' => [
        'default' => [
            'className' => 'Mail',
            // The following keys are used in SMTP transports
            'host' => 'localhost',
            'port' => 25,
            'timeout' => 30,
            'username' => null,
            'password' => null,
            'client' => null,
            'tls' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],

    /**
     * Email delivery profiles
     *
     * Delivery profiles allow you to predefine various properties about email
     * messages from your application and give the settings a name. This saves
     * duplication across your application and makes maintenance and development
     * easier. Each profile accepts a number of keys. See `Cake\Mailer\Email`
     * for more information.
     */
    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => 'you@localhost',
            //'charset' => 'utf-8',
            //'headerCharset' => 'utf-8',
        ],
    ],

    /**
     * Connection information used by the ORM to connect
     * to your application's datastores.
     * Do not use periods in database name - it may lead to error.
     * See https://github.com/cakephp/cakephp/issues/6471 for details.
     * Drivers include Mysql Postgres Sqlite Sqlserver
     * See vendor\cakephp\cakephp\src\Database\Driver for complete list
     */
    'Datasources' => [
        'default' => [
            'className' => 'Cake\Database\Connection',

            /**
             * Possible values for 'driver' are: Mysql, Postgres, Sqlite
             * Simply replace Mysql with Posgres or Sqlite in 'driver' value
             */
            'driver' => 'Cake\Database\Driver\Mysql',
            'host' => '__BE4_DB_HOST__',

            /**
             * CakePHP will use the default DB port based on the driver selected
             * MySQL on MAMP uses port 8889, MAMP users will want to uncomment
             * the following line and set the port accordingly
             */
            'port' => '__BE4_DB_PORT__',
            'username' => '__BE4_DB_USERNAME__',
            'password' => '__BE4_DB_PASSWORD__',
            'database' => '__BE4_DB_DATABASE__',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'persistent' => false,
            'flags' => [],
            'cacheMetadata' => true,
            'log' => false,

            /**
             * Set identifier quoting to true if you are using reserved words or
             * special characters in your table or column names. Enabling this
             * setting will result in queries built using the Query Builder having
             * identifiers quoted when creating SQL. It should be noted that this
             * decreases performance because each query needs to be traversed and
             * manipulated before being executed.
             */
            'quoteIdentifiers' => false,

            /**
             * During development, if using MySQL < 5.6, uncommenting the
             * following line could boost the speed at which schema metadata is
             * fetched from the database. It can also be set directly with the
             * mysql configuration directive 'innodb_stats_on_metadata = 0'
             * which is the recommended value in production environments
             */
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],

            'url' => env('DATABASE_URL', null),
        ],

        /**
         * The test connection is used during the test suite.
         */
        'test' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => 'localhost',
            //'port' => 'non_standard_port_number',
            'username' => 'bedita',
            'password' => 'bedita',
            'database' => 'bedita_test',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            'log' => false,
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
            'url' => env('DATABASE_TEST_URL', null),
        ],
    ],

    /**
     * Configures logging options
     */
    'Log' => [
        'debug' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'debug',
            'url' => env('LOG_DEBUG_URL', null),
            'scopes' => false,
            'levels' => ['notice', 'info', 'debug'],
        ],
        'error' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'error',
            'url' => env('LOG_ERROR_URL', null),
            'scopes' => false,
            'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        ],
        // To enable this dedicated query log, you need set your datasource's log flag to true
        'queries' => [
            'className' => 'Cake\Log\Engine\FileLog',
            'path' => LOGS,
            'file' => 'queries',
            'url' => env('LOG_QUERIES_URL', null),
            'scopes' => ['queriesLog'],
        ],
    ],

    /**
     * Session configuration.
     *
     * Contains an array of settings to use for session configuration. The
     * `defaults` key is used to define a default preset to use for sessions, any
     * settings declared here will override the settings of the default config.
     *
     * ## Options
     *
     * - `cookie` - The name of the cookie to use. Defaults to 'CAKEPHP'. Avoid using `.` in cookie names,
     *   as PHP will drop sessions from cookies with `.` in the name.
     * - `cookiePath` - The url path for which session cookie is set. Maps to the
     *   `session.cookie_path` php.ini config. Defaults to base path of app.
     * - `timeout` - The time in minutes the session should be valid for.
     *    Pass 0 to disable checking timeout.
     *    Please note that php.ini's session.gc_maxlifetime must be equal to or greater
     *    than the largest Session['timeout'] in all served websites for it to have the
     *    desired effect.
     * - `defaults` - The default configuration set to use as a basis for your session.
     *    There are four built-in options: php, cake, cache, database.
     * - `handler` - Can be used to enable a custom session handler. Expects an
     *    array with at least the `engine` key, being the name of the Session engine
     *    class to use for managing the session. CakePHP bundles the `CacheSession`
     *    and `DatabaseSession` engines.
     * - `ini` - An associative array of additional ini values to set.
     *
     * The built-in `defaults` options are:
     *
     * - 'php' - Uses settings defined in your php.ini.
     * - 'cake' - Saves session files in CakePHP's /tmp directory.
     * - 'database' - Uses CakePHP's database sessions.
     * - 'cache' - Use the Cache class to save sessions.
     *
     * To define a custom session handler, save it at src/Network/Session/<name>.php.
     * Make sure the class implements PHP's `SessionHandlerInterface` and set
     * Session.handler to <name>
     *
     * To use database sessions, load the SQL file located at config/Schema/sessions.sql
     */
    'Session' => [
        'defaults' => 'php',
    ],

    /**
     * Additional plugins to load with this format: 'PluginName' => load options array
     * Where options array may contain
     *
     * - `debugOnly` - boolean - (default: false) Whether or not you want to load the plugin when in 'debug' mode only
     * - `bootstrap` - boolean - (default: false) Whether or not you want the $plugin/config/bootstrap.php file loaded.
     * - `routes` - boolean - (default: false) Whether or not you want to load the $plugin/config/routes.php file.
     * - `ignoreMissing` - boolean - (default: false) Set to true to ignore missing bootstrap/routes files.
     * - `autoload` - boolean - (default: false) Whether or not you want an autoloader registered
     */
    'Plugins' => [
        'BEdita/DevTools' => ['debugOnly' => true, 'bootstrap' => true],
//      'MyPlugin' => ['autoload' => true, 'bootstrap' => true, 'routes' => true],
    ],

    /**
     * Default pagination settings.
     *
     * - `limit` - Default number of items per page (page_size). Defaults to 20.
     * - `maxLimit` - The maximum numer of items retrievable using a `page_size` request per call. Defaults to 100.
     *   This value cannot exceed a superlimit (@see \BEdita\API\Controller\Component\PaginatorComponent::MAX_LIMIT))
     */
    'Pagination' => [
        'limit' => 20,
        'maxLimit' => 100,
    ],

    /**
     * Project information.
     *
     * - `name` public name of the project, short expression recommended like `MyProject`, `Nope v1`
     */
    'Project' => [
        'name' => env('PROJECT_NAME', 'BEdita 4'),
    ],

    /**
     * Signup settings.
     *
     * - `requireActivation` - boolean (default: true) - Are new users required to verify their contact method
     *      before being "activated"?
     * - 'roles' - allowed role names on user signup (this config should be set normally at application level),
     *      requested user roles MUST be included in this array
     */
    'Signup' => [
        // 'requireActivation' => true,
        'roles' => [],
    ],

    /**
     * Auth default settings.
     *
     * - 'passwordPolicy.rule' - Regexp, callback or validation class to use as validation rule (only regexp supported for now)
     * - 'passwordPolicy.message' -  Error message for password validation failure
     */
    'Auth' => [
        'passwordPolicy' => [
            'rule' => '',
            'message' => '',
        ]
    ],

    /**
     * Filesystem configuration.
     *
     * Several filesystem configurations allow you to manage your files
     * separately, and each configuration will behave as an isolate mount-point.
     */
    'Filesystem' => [
        'default' => [
            'className' => 'BEdita/Core.Local',
            'path' => WWW_ROOT . '_files',
            'url' => env('FILESYSTEM_DEFAULT_URL', null),
        ],
        'thumbnails' => [
            'className' => 'BEdita/Core.Local',
            'path' => WWW_ROOT . '_files' . DS . 'thumbs',
            'url' => env('FILESYSTEM_THUMBNAILS_URL', null),
        ],
    ],

    /**
     * Thumbnails configuration.
     *
     * - `allowAny`: set this to `true` to allow clients to pass thumbnail options in request.
     *      Should be set to `false` on production systems, where only presets are allowed.
     * - `presets`: list of named presets. Presets are set of options that allow clients to
     *      generate thumbnails of different formats without giving them full power over thumbnail
     *      options, which might lead to DoS attacks.
     * - `generators`: configured generators. Different generators will generate thumbnails using
     *      different systems. For instance, you may have a default generator that generates the
     *      thumbnails using GD or Imagik, an asynchronous generator that enqueues thumbnail jobs,
     *      an external generator that invokes a remote API, ...
     */
    'Thumbnails' => [
        'allowAny' => filter_var(env('THUMBNAILS_ALLOW_ANY', false), FILTER_VALIDATE_BOOLEAN),
        'presets' => [
            'default' => [
                // 'generator' => 'async',
                'w' => 768,
                'h' => 576,
            ],
        ],
        'generators' => [
            'default' => [
                'className' => 'BEdita/Core.Glide',
                // 'cache' => 'thumbnails',
                'url' => env('THUMBNAILS_DEFAULT_URL', null),
            ],
            'async' => [
                'className' => 'BEdita/Core.Async',
                // 'baseGenerator' => 'default',
                'url' => env('THUMBNAILS_ASYNC_URL', null),
            ],
        ],
    ],
];
