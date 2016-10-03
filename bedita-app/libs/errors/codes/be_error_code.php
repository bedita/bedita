<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
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
 * Default Error Code object.
 *
 * It is responsible for:
 *
 * - check if the error code passed is valid (fallback on GENERIC_ERROR)
 * - handle error code returning the code and info about error
 *
 * You can create custom error code objects but custom class should always extend this one.
 * The name of the custom class MUST be the camelized version of the error code lower cased.
 * For example to handle `UPLOAD_QUOTA_EXCEEDED` error code you need to create the class
 *
 * ```
 * class UploadQuotaExceeded extends BeErrorCode {}
 * ```
 */
class BeErrorCode {

    /**
     * The valid codes
     *
     * @var array
     */
    protected $validCodes = array();

    /**
     * The error code
     *
     * @var string
     */
    protected $code = 'GENERIC_ERROR';

    /**
     * An array of info about the error
     *
     * @var array
     */
    protected $info = array();

    /**
     * Constructor. Set error code and info if it is valid. 
     *
     * @param string $errorCode The error code name
     * @param array $info Additional error information
     * @return void
     */
    public function __construct($errorCode = '', array $info = array()) {
        $validCodes = $this->validCodes();
        if (!array_key_exists($errorCode, $validCodes)) {
            return;
        }

        $this->code = $errorCode;
        $this->info = $info + $validCodes[$errorCode] + $this->info;
    }

    /**
     * Initialize the array of valid codes `self::validCodes` and return it.
     * Valid codes are taken from `bedita-app/config/error.codes.php`
     * combined with `app/config/error.codes.php` if the app is a frontend 
     *
     * @return array
     */
    public function validCodes() {
        if (!empty($this->validCodes)) {
            return $this->validCodes;
        }

        $this->loadErrorCodes(BEDITA_CORE_PATH . DS . 'config' . DS . 'error.codes.php');

        if (BACKEND_APP) {
            return $this->validCodes;
        }

        $this->loadErrorCodes(APP . DS . 'config' . DS . 'error.codes.php');

        return $this->validCodes;
    }

    /**
     * Load a file containing error codes adding them to those already presents
     *
     * @param string $filePath The file path to load
     * @return array
     */
    protected function loadErrorCodes($filePath) {
		if (!file_exists($filePath )) {
            return;
        }

        $errorCodes = include $filePath;
        if (empty($errorCodes) || !is_array($errorCodes)) {
            return;
        }
        
        $this->validCodes = $this->validCodes + $errorCodes;
    }

    /**
     * Return the error code 
     *
     * @return string
     */
    public function code() {
        return $this->code;
    }

    /**
     * Return the error info 
     *
     * @return array
     */
    public function info() {
        return $this->info;
    }
}
