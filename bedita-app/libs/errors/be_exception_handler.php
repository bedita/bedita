<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2015 ChannelWeb Srl, Chialab Srl
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
 * Default Exception handler class
 */
class BeExceptionHandler extends Object {

    /**
     * Default method to handle Exceptions
     *
     * @param Exception $exception
     * @return void
     */
    public static function handleExceptions(Exception $exception) {
        $options = array();
        $name = get_class($exception);
        if ($exception instanceof SmartyException) {
            $name = 'SmartyException';
        }
        $method = 'handle' . $name;
        self::appError($method, $options, $exception);
    }

    /**
     * display a new AppError
     *
     * @param string $method The method the AppError class has to use
     * @param array $options Options to pass to AppError class
     * @param Exception|null $exception The Exception that was been thrown
     * @return void
     */
    protected static function appError($method, array $options, Exception $exception = null) {
        if (!class_exists('Controller')) {
            App::import('Core', 'Controller');
        }
        if (!class_exists('Router')) {
            App::import('Core', 'Router');
        }
        include_once (APP . 'app_error.php');
        if (!method_exists('AppError', $method)) {
            $method = 'handleException';
        }
        new AppError($method, $options, $exception);
    }

}
