<?php

/*
 * Local configuration file to provide any overrides to your app.php configuration.
 * Copy and save this file as app_local.php and make changes as required.
 * Note: It is not recommended to commit files with credentials such as app_local.php
 * into source code version control.
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => [

            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',

            'host' => '__BE4_DB_HOST__',
            'port' => '__BE4_DB_PORT__',
            'username' => '__BE4_DB_USERNAME__',
            'password' => '__BE4_DB_PASSWORD__',
            'database' => '__BE4_DB_DATABASE__',
            // For MariaDB/MySQL use `utf8mb4`
            'encoding' => 'utf8mb4',

            'timezone' => env('BEDITA_DEFAULT_TIMEZONE', 'UTC'),
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

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'url' => env('DATABASE_TEST_URL', 'sqlite:///tmp/be5_tests.sqlite'),
        ],
    ],

    /*
     * Debugger configuration
     *
     * Define development error values for Cake\Error\Debugger
     *
     * - `editor` Set the editor URL format you want to use.
     *   By default atom, emacs, macvim, phpstorm, sublime, textmate, and vscode are
     *   available. You can add additional editor link formats using
     *   `Debugger::addEditor()` during your application bootstrap.
     * - `outputMask` A mapping of `key` to `replacement` values that
     *   `Debugger` should replace in dumped data and logs generated by `Debugger`.
     */
    'Debugger' => [
        'editor' => 'vscode',
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'host' => 'localhost',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
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
        // 'BEdita/DevTools' => ['bootstrap' => true],
        // 'BEdita/AWS' => [],
//      'MyPlugin' => ['autoload' => true, 'bootstrap' => true, 'routes' => true],
    ],

    /**
     * Default pagination settings. Uncomment to change.
     *
     * - `limit` - Default number of items per page (page_size). Defaults to 20.
     * - `maxLimit` - The maximum numer of items retrievable using a `page_size` request per call. Defaults to 100.
     *   This value cannot exceed a superlimit (@see \BEdita\API\Datasource\JsonApiPaginator::MAX_LIMIT))
     */
    // 'Pagination' => [
    //     'limit' => 20,
    //     'maxLimit' => 100,
    // ],

    /**
     * Project information.
     *
     * - `name` public name of the project, short expression recommended like `MyProject`, `Nope v1`
     */
    'Project' => [
        'name' => env('PROJECT_NAME', 'BEdita 5'),
    ],

    /**
     * Uncomment to define custom actions to load
     * This way some action beahavior can be overridden
     */
    // 'Actions' => [
    //     'SignupUserAction' => '\MyPlugin\Model\Action\SignupUserAction',
    //     'SignupUserActivationAction' => '\MyPlugin\Model\Action\SignupUserActivationAction',
    // ],

    /**
     * Uncomment to define custom mailer classes to load
     */
    // 'Mailer' => [
    //     'User' => '\MyPlugin\Mailer\UserMailer',
    // ],

    /**
     * Signup settings.
     *
     * - `requireActivation` - boolean (default: true) - Are new users required to verify their contact method
     *      before being "activated"? If true upon creation user will have a `draft` status, otherwise `on`
     * - 'roles' - allowed role names on user signup (this config should be set normally at application level),
     *      requested user roles MUST be included in this array.
     *      Leave empty to allow signup only for users without roles.
     * - 'requireEmail' - require email upon signup (default: true)
     * - 'activationUrl' => default activation URL to use if not set by application
     * - 'requirePassword' - require password upon signup (default: true), can be false in some AUTH schemas like One Time Password
     * - 'defaultRoles` - roles to add upon signup as default if no roles are passed; they MUST be in allowed `roles` in order to be set
     */
    'Signup' => [
        // 'requireActivation' => true,
        // 'roles' => [],
        // 'requireEmail' => true,
        // 'requirePassword' => true,
        // 'activationUrl' => 'https://myapp.com/verify',
        // 'defaultRoles' => [],
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
        ],
    ],

    /**
     * Default values per object type
     * object type names as keys (lower case), default property names and values as value
     */
    // 'DefaultValues' => [
    //     'cats' => [
    //         'status' => 'off',
    //     ],
    //     'dogs' => [
    //         'status' => 'on', // GO dogs!
    //     ],
    // ],

    /**
     * Optional schema for the `params` attribute of the children relationship.
     */
    // 'ChildrenParams' => [
    //     'type' => 'object',
    //     'required' => ['name'],
    //     'properties' => [
    //         'name' => ['type' => 'string'],
    //         'hobby' => [
    //             'type' => 'string',
    //             'enum' => ['fishing', 'knitting', 'gaming'],
    //         ],
    //     ],
    // ],

    /**
     * I18n settings.
     * Language tags follow IETF RFC5646 https://tools.ietf.org/html/rfc5646
     * See also https://en.wikipedia.org/wiki/IETF_language_tag
     *
     * - 'languages' - array of language tags as keys with names as values; if empty any language tag may be used,
     *      if not only these language tags are valid
     * - 'default' - value assumed on empty lang attribute upon creation, can be null (default)
     */
    // 'I18n' => [
    //     // list of allowed project language tags
    //     'languages' => [
    //     //   'en' => 'English',
    //     //   'de' => 'German',
    //     ],

    //     // default lang tag - may be null
    //     'default' => null,
    // ],

    /**
     * Queue settings
     */
    // 'Queue' => [
    //     'default' => [
    //         // A DSN for your configured backend. default: null
    //         // Can contain protocol/port/username/password or be null if the backend defaults to localhost
    //         'url' => 'redis://127.0.01:6379',

    //         // The queue that will be used for sending messages. default: default
    //         // This can be overridden when queuing or processing messages
    //         'queue' => 'default',

    //         // Delay in seconds to use in `QueueManager::push` action, default: 1
    //         // 'pushDelay' => 1,

    //         // The name of a configured logger, default: null
    //         // 'logger' => 'stdout',

    //         // The name of an event listener class to associate with the worker
    //         // 'listener' => \App\Listener\WorkerListener::class,

    //         // The amount of time in milliseconds to sleep if no jobs are currently available. default: 10000
    //         'receiveTimeout' => 10000,
    //     ],
    // ],
];
