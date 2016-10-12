<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Shell;

use BEdita\Core\Utility\Database;
use Cake\Cache\Cache;
use Cake\Console\Shell;
use Cake\Core\Configure\Engine\JsonConfig;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Basic shell commands:
 *  - setup a new instance
 *  - check instance
 *  - internal cache cleanup
 *
 * @since 4.0.0
 */
class BeditaShell extends Shell
{

    /**
     * User input data array
     *
     * @var array
     */
    protected $userInputData = null;

    /**
     * Is configuration modified from startup default?
     *
     * @var boolean
     */
    protected $configModified = false;

    /**
     * Configuration file path
     *
     * @var string
     */
    public $configPath = null;

    /**
     * Default initial user name
     *
     * @var string
     */
    public $defaultUsername = 'bedita';

    /**
     * Default initial user id
     *
     * @var int
     */
    public $defaultUserId = 1;

    /**
     * Temporary configuration name used in initial setup
     */
    const TEMP_SETUP_CFG = '__temp_setup__';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('setup', [
            'help' => 'Setup new instance or check current setup.',
            'parser' => [
                'description' => [
                    'Use this interactive shell command to setup a new instance ',
                    'or to check current instance configuration/status.',
                ],
                'options' => [
                    'yes' => [
                        'help' => 'Respond yes to all questions',
                        'short' => 'y',
                        'required' => false,
                        'default' => true,
                    ],
                ],
            ],
        ]);

        return $parser;
    }

    /**
     * Setup or check BE4 instance
     *
     * @return bool True on setup terminated with success, false on error or user stop
     */
    public function setup()
    {
        $res = $this->databaseConnection();
        if (!$res) {
            $this->warn('Setup stopped');
            if (isset($this->userInputData)) {
                $this->warn('==> Please check your database connection parameters');
            }

            return false;
        }

        if (empty($this->userInputData)) {
            $info = Database::basicInfo();
            $this->info('Current database configuration');
            $this->info(' * Host: ' . $info['host']);
            $this->info(' * Database: ' . $info['database']);
            $this->info(' * Vendor: ' . $info['vendor']);
        }

        $this->info('Database connection is OK');
        $this->hr();


        $this->info('Checking database schema....');
        if (!$this->initSchema()) {
            return false;
        }
        $this->hr();
        if (!empty($this->userInputData)) {
            if (!$this->saveConnectionData()) {
                return false;
            }
        }

        $this->info('Checking filesystem permissions....');
        $this->checkFs();
        $this->hr();

        $this->info('Set admin user....');
        $this->adminUser();
        $this->hr();

        return true;
    }

    /**
     * Initialize and check DB schema
     *
     * @return bool True on success, false on user stop
     */
    protected function initSchema()
    {
        $currentSchema = Database::currentSchema();
        if (!empty($currentSchema)) {
            $be4Schema = (new JsonConfig())->read('BEdita/Core.schema/be4-schema');
            $schemaDiff = Database::schemaCompare($be4Schema, $currentSchema);
            if (!empty($schemaDiff)) {
                $this->err('Schema differs from BEdita4 schema!');
                $this->warn('Details:');
                foreach ($schemaDiff as $key => $data) {
                    foreach ($data as $type => $value) {
                        foreach ($value as $v) {
                            $this->warn($key . ' ' . Inflector::singularize($type) . ': ' . $v);
                        }
                    }
                }
                $this->err('Unable to proceed with setup, please check your database');

                return false;
            }
            $this->info('Database schema is OK');
        } else {
            $this->out('Database is empty');
            $res = $this->in('Proceed with database schema and data initialization?', ['y', 'n'], 'n');
            if ($res != 'y') {
                $this->info('Database creation aborted. Bye.');

                return false;
            }
            $dbInitTask = $this->Tasks->load('BEdita/Core.DbInit');
            $dbInitTask->main();
        }

        return true;
    }

    /**
     * Initialize and check DB schema
     *
     * @return void
     */
    protected function checkFs()
    {
        $httpdUser = exec("ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1");
        if (!empty($httpdUser)) {
            $this->info('HTTPD (webserver) user seems to be: ' . $httpdUser);
        } else {
            $this->info('Could not determine HTTPD (webserver) user');
        }
        $this->out('tmp/ and logs/ folder should be writable by the webserver');
        $this->checkDir(TMP, 'tmp/');
        $this->checkDir(LOGS, 'logs/');
    }

    /**
     * Check directory owners & permissions
     *
     * @param string $dirPath Folder path to check
     * @param string $label Folder label/name
     *
     * @return void
     */
    protected function checkDir($dirPath, $label)
    {
        $file = new File($dirPath);
        $this->out('Checking ' . $label . ' (' . $dirPath . ')');
        if (!is_dir($dirPath)) {
            $this->abort('Folder ' . $dirPath . ' not found, please check your installation!');
        }
        $owner = posix_getpwuid(fileowner($dirPath));
        $this->out(' owner: ' . $owner['name']);
        $group = posix_getgrgid(filegroup($dirPath));
        $this->out(' group: ' . $group['name']);
        $perms = substr(decoct(fileperms($dirPath)), 2);
        $this->out(' perms: ' . $perms);
    }

    /**
     * Check if database connection is working
     *
     * @return bool True on success, false on error
     */
    protected function checkDbConnection()
    {
        $res = Database::connectionTest();
        if (!$res['success']) {
            $this->warn('Unable to connect');
            $this->err('Error message: ' . $res['error']);
            if ($this->configModified) {
                $this->warn('==> Please check your database configuration in config/app.php file');
                $this->warn("==> See 'DataSources' => 'default' array");
            }

            return false;
        }

        return true;
    }

    /**
     * Check database connection parameters
     * On a fresh install placeholders should be found, if placeholders are not found
     * a manual edit is required
     *
     * @return bool True on success, false otherwise
     */
    protected function databaseConnection()
    {
        $this->configModified = false;
        $dbParams = Database::basicInfo();
        $fields = ['host', 'database', 'username', 'password'];
        foreach ($fields as $name) {
            $expected = '__BE4_DB_' . strtoupper($name) . '__';
            if (empty($dbParams[$name]) || $dbParams[$name] !== $expected) {
                $this->configModified = true;
            }
        }

        if ($this->configModified) {
            $this->warn('Configuration file has been modified!');

            return $this->checkDbConnection();
        }

        $this->info('A working database connection is needed in order to continue');
        $this->info('Parameter needed are: host, database, username, password');
        $res = $this->in('Proceed with setup?', ['y', 'n'], 'n');
        if ($res != 'y') {
            $this->info('Database setup stopped');

            return false;
        }

        $this->dbConnectionUserInput();
        $dbParams = array_merge($dbParams, $this->userInputData);
        $dbParams['className'] = 'Cake\Database\Connection';
        ConnectionManager::config(self::TEMP_SETUP_CFG, $dbParams);
        ConnectionManager::alias(self::TEMP_SETUP_CFG, 'default');

        return $this->checkDbConnection();
    }

    /**
     * Save database connection data in config/app.php file
     *
     * @return bool True on success, false otherwise
     */
    protected function saveConnectionData()
    {
        if (empty($this->configPath)) {
            $this->configPath = CONFIG . 'app.php';
        }
        if (!is_writable($this->configPath)) {
            $this->warn('Unable to update configuration file');
            $this->warn('==> Please check write permission on config/app.php file');

            return false;
        }

        $content = file_get_contents($this->configPath);
        $fields = ['host', 'database', 'username', 'password'];
        foreach ($fields as $name) {
            $placeHolder = '__BE4_DB_' . strtoupper($name) . '__';
            $content = str_replace($placeHolder, $this->userInputData[$name], $content);
        }

        // TODO: better php check for $content?
        $eval = eval(str_replace('<?php', '', $content));
        if (empty($eval) || !is_array($eval)) {
            $this->err('Error updating configuration parameters');

            return false;
        }

        file_put_contents($this->configPath, $content);
        $this->configModified = true;

        $this->info('Configuration updated in ' . $this->configPath);

        return true;
    }

    /**
     * Get database connection parameters from user
     *
     * @return void
     */
    protected function dbConnectionUserInput()
    {
        $this->userInputData = [];
        $this->userInputData['host'] = $this->in('Host?', null, 'localhost');
        $this->userInputData['database'] = $this->in('Database?');
        $this->userInputData['username'] = $this->in('Username?');
        $this->userInputData['password'] = $this->in('Password?');
    }

    /**
     * Initialize and check DB schema
     *
     * @return void
     */
    protected function adminUser()
    {
        $usersTable = TableRegistry::get('Users');
        $adminUser = $usersTable->get($this->defaultUserId);

        if ($adminUser->username !== $this->defaultUsername) {
            $this->info('An admin user has already been set: ' . $adminUser->username);
            $res = $this->in('Overwrite current admin user?', ['y', 'n'], 'n');
            if ($res != 'y') {
                $this->info('Admin user ' . $adminUser->username . ' unchanged.');

                return;
            }
        }

        $this->info('A new admin user will be created');
        $data = [];
        $data['username'] = $this->in('username: ');
        $data['password_hash'] = $this->in('password: ');
        $adminUser = $usersTable->patchEntity($adminUser, $data);
        $adminUser->blocked = false;
        if (!$usersTable->save($adminUser)) {
             $this->abort('Error saving admin user data');
        }

        $this->info('Admin user login data updated');
    }
}
