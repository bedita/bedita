<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * This file contains the BEdita error codes.
 *
 * Every error is identified by a string code (the key in the array)
 * and an array that describe the error and can contain other useful information. 
 *
 * Every Frontend app can add its custom error codes creating a file `app/config/error.codes.php`.
 * The file MUST returns an array as shown below. A good practice is to prefix the app error codes
 * with a significant word as `MYAPP_`.
 *
 * To trigger an error with a specific code you can pass the code string to an exception.
 * For example:
 *
 * ```
 * throw new BeditaForbiddenException('Error during upload', ['errorCode' => 'UPLOAD_QUOTA_EXCEEDED']);
 * ```
 *
 */

return array(
    'UPLOAD_QUOTA_EXCEEDED' => array(
        'description' => 'Upload quota available exceeded',
    ),
    'UPLOAD_FILES_LIMIT_EXCEEDED' => array(
        'description' => 'Maximum number of files allowed exceeded',
    )
);
