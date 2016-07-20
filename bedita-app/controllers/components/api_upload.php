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

App::import('Core', 'File');

/**
 * ApiUploadComponent class
 *
 * Used to handle upload via REST API
 *
 */
class ApiUploadComponent extends Object {

    /**
     * Controller instance
     *
     * @var Controller
     */
    public $controller = null;

    /**
     * List of components used
     *
     * @var array
     */
    public $components = array('BeFileHandler');

    /**
     * Initialize component (called before Controller::beforeFilter())
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller, array $settings = array()) {
        $this->controller = &$controller;
        $this->_set($settings);
    }

    /**
     * Upload a file using local filesystem or delegating to Model::apiUpload() method if exists.
     * 
     * After the file is moved to the right location
     * a row in hash_jobs is added with status "pending" and the hash string is returned.
     * That hash string must be used from client to associate a new object to the file uploaded
     * 
     *
     * @throws BeditaInternalErrorException
     * @param string $targetName The target file name
     * @param string $objectType The object type to which the upload file refers
     * @return string
     */
    public function upload($targetName, $objectType) {
        $source = $this->source();
        $fileName = $this->BeFileHandler->buildNameFromFile($targetName);
        $mimeType = ClassRegistry::init('Stream')->getMimeType($source->pwd(), $fileName);

        $objectTypeClass = Configure::read('objectTypes.' . $objectType . '.model');
        $model = ClassRegistry::init($objectTypeClass);
        if (method_exists($model, 'apiUpload')) {
            $targetPath = $model->apiUpload($source, array(
                'fileName' => $fileName,
                'user' => $this->controller->ApiAuth->getUser()
            ));
        } else {
            $targetPath = $this->put($source, $fileName);
        }

        if (empty($targetPath)) {
            throw new BeditaInternalErrorException('Error uploading file');
        }

        return $this->generateToken(array(
            'uri' => $targetPath,
            'name' => pathinfo($targetPath, PATHINFO_BASENAME),
            'mime_type' => $mimeType,
            'file_size' => $source->size(),
            'original_name' => $targetName,
            'object_type' => $objectType
        ));
    }

    /**
     * Read from php://input, put the content in a temporary file and return the File instance
     *
     * @throws BeditaBadRequestException, BeditaInternalErrorException
     * @return File
     */
    public function source() {
        $contentLength = env('CONTENT_LENGTH');
        if (empty($contentLength)) {
            throw new BeditaBadRequestException('Missing or invalid Content-Length in request headers');
        }

        $inputData = file_get_contents('php://input');
        if (empty($inputData)) {
            throw new BeditaBadRequestException('Missing file to upload');
        }

        $tmpFileName = tempnam(sys_get_temp_dir(), 'bedita-api-upload-');
        if ($tmpFileName === false) {
            throw new BeditaInternalErrorException('Error creating temporary file');
        }

        $source = new File($tmpFileName);
        if ($source->write($inputData) === false) {
            $source->delete();
            throw new BeditaInternalErrorException('Error writing input data in temporary file');
        }
        clearstatcache();

        if ($source->size() != $contentLength) {
            throw new BeditaBadRequestException('Content-Length header does not match file size');
        }

        return $source;
    }

    /**
     * Copy the source file in a target path obtained starting from `$targetName`
     * and returning the target path.
     * Once the file is successfully copied it is removed.
     *
     * @throws BeditaInternalErrorException
     * @param File $source The source File instance
     * @param string $targetName The file target name
     * @return string
     */
    public function put(File $source, $targetName) {
        $targetPath = $this->BeFileHandler->getPathTargetFile($targetName);

        if ($this->BeFileHandler->putFile($source->pwd(), $targetPath) === false) {
            throw new BeditaInternalErrorException('Error during upload operation');
        }
        $source->delete();

        return $targetPath;
    }

    /**
     * Save an upload token in hash_jobs table and return it.
     *
     * @throws BeditaInternalErrorException
     * @param array $params An array of parameters to save in `hash_jobs.params`
     * @return string
     */
    protected function generateToken(array $params = array()) {
        $user = $this->controller->ApiAuth->getUser();
        $hashJob = ClassRegistry::init('HashJob');
        $uploadToken = $hashJob->generateHash(true);
        $data = array(
            'service_type' => 'api_upload',
            'user_id' => $user['id'],
            'hash' => $uploadToken,
            'expired' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
        );
        $data += $params;
        if (!$hashJob->save($data)) {
            throw new BeditaInternalErrorException('Error during upload operation');
        }

        return $uploadToken;
    } 

}
