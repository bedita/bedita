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
use Cake\Core\Plugin;
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
     * {@inheritDoc}
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
     * @return void
     */
    public function setup()
    {
        $info = Database::basicInfo();
        $this->info('Current database configuration');
        $this->info(' * Host: ' . $info['host']);
        $this->info(' * Database: ' . $info['database']);
        $this->info(' * Vendor: ' . $info['vendor']);
        $this->info('Checking database connection....');
        $res = Database::connectionTest();
        if (!$res['success']) {
            $this->warn('Unable to connect');
            $this->log('connection test message: ' . $res['error'], 'debug');
            $this->warn('==> Please check your database configuration in config/app.php file');
            $this->warn("==> See 'DataSources' => 'default' array");

            return;
        }
        $this->info('Database connection is OK');
        $this->hr();

        $this->info('Checking database schema....');
        if (!$this->initSchema()) {
            return;
        }
        $this->hr();

        $this->info('Checking filesystem permissions....');
        $this->checkFs();
        $this->hr();

        $this->info('Set admin user....');
        $this->adminUser();
        $this->hr();
    }

    /**
     * Initialize and check DB schema
     *
     * @return bool True on success, false on user stop
     */
    protected function initSchema()
    {
        if (!Cache::clear(false, '_cake_model_')) {
            $this->abort('Unable to remove internal cache before schema check');
        }
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
                $this->abort('Unable to proceed with setup, please check your database');
            }
            $this->info('Database schema is OK');
        } else {
            $this->out('Database is empty');
            $res = $this->in('Proceed with database creation?', ['y', 'n'], 'n');
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
     * Initialize and check DB schema
     *
     * @return void
     */
    protected function adminUser()
    {
        $usersTable = TableRegistry::get('Users');
        $adminUser = $usersTable->get(1);
        if (empty($adminUser)) {
            $this->abort('Unable to find default admin user (with id = 1)');
        }

        if ($adminUser->username !== 'bedita') {
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
