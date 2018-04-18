<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;

/**
 * Load plugin routes.
 * First loaded plugins then 'BEdita/API'
 */
$plugins = Plugin::loaded();
foreach ($plugins as $plugin) {
    if (!in_array($plugin, ['BEdita/API', 'BEdita/Core'])) {
        static::routes($plugin);
    }
}

// Load 'BEdita/API' as last route
if (Plugin::loaded('BEdita/API')) {
    Plugin::routes('BEdita/API');
}
