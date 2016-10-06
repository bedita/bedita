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

namespace BEdita\Core\Utility;

use BEdita\Core\Utility\Database;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

/**
 * Retrieve system information on service availability and general status
 *
 * Provides static methods to get information in array format
 */
class System
{

    /**
     * Get status information
     *
     * @return array Information on environment and datasource/cache connections
     */
    public static function status()
    {
        $env = 'ok';
        $errors = [];
        $check = self::checkPHP();
        if (!$check['success']) {
            $env = 'error';
            $errors['php'] = $check['messages'];
        }

        $check = self::checkFS();
        if (!$check['success']) {
            $env = 'error';
            $errors['fs'] = $check['messages'];
        }

        $check = Database::connectionTest();
        if (!$check['success']) {
            $env = 'error';
            $errors['db'] = $check['error'];
        }

        $res = ['environment' => $env];
        if (Configure::read('debug')) {
            $res['errors'] = $errors;
            $res['debug'] = true;
            $res['plugins'] = Plugin::loaded();
            $res['versions']['BEdita'] = Configure::read('BEdita.version');
        }

        return $res;
    }

    /**
     * Check PHP version and extensions
     *
     * @return array Details of performed check with keys
     *   - 'success' true if check ok, false otherwise
     *   - 'message' array of error messages
     */
    public static function checkPHP()
    {
        $success = true;
        $messages = [];
        self::loadRequirements();
        $required = Configure::read('Requirements');
        if (!version_compare(PHP_VERSION, $required['phpMin'], '>=')) {
            $success = false;
            $messages[] = 'PHP version ' . PHP_VERSION . ' too low, min required ' . $required['phpMin'];
        }

        foreach ($required['extensions'] as $ext) {
            if (!extension_loaded($ext)) {
                $success = false;
                $messages[] = 'Missing PHP extension ' . $ext;
            }
        }

        return compact('success', 'messages');
    }

    /**
     * Check Filesystem
     *
     * @return array Details of performed check with keys
     *   - 'success' true if check ok, false otherwise
     *   - 'message' array of error messages
     */
    public static function checkFS()
    {
        $success = true;
        $messages = [];
        self::loadRequirements();

        foreach (Configure::read('Requirements.writable') as $wr) {
            if (!is_writable($wr)) {
                $success = false;
                $messages[] = 'Directory ' . $wr . ' MUST be writable';
            }
        }

        return compact('success', 'messages');
    }

    /**
     * Load system requirements in Configuration 'Requirements'
     *
     * @return void
     */
    protected static function loadRequirements()
    {
        if (!Configure::read('Requirements')) {
            Configure::load('BEdita/Core.requirements');
        }
    }
}
