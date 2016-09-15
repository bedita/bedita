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
        });
    }

    /**
     * Upload a file using local filesystem or delegating to Model::apiUpload() method if exists.
     *
     * After the file is moved to the right location
     * a row in hash_jobs is added with status "pending" and the hash string is returned.
     * That hash string must be used from client to associate a new object to the file uploaded
     *
     * Event triggered:
     *
     * - Api.beforeCheckUpload: pass to listener a list of objects uploadable and the authenticated user
     *
     *   The listener should be calculate the size occupied (in bytes) from all files linked to a specific object type
     *   and the total number of files.
     *   It should be return an array with key object types and the above information.
     *   For example:
     *
     *   ```
     *   array(
     *       'image' => array(
     *           'size' => 12345678,
     *           'number' => 256
     *        ),
     *        'video' => array(
     *           'size' => 123456789,
     *           'number' => 15
     *        ),
     *   )
     *   ```
     *
     *   The listener should be merge its results with `$event->result` to avoid to delete other results
     *   calculated from another listener.
     *
     *   The final result will be used to check if the authenticated user is exceeding his upload quota defined in conf.
     *
     *
     * @param string $originalFileName The target file name
     * @param string $objectType The object type to which the upload file refers
     * @return string
     * @throws BeditaInternalErrorException
     * @throws BeditaConflictException When file already exists
     */
    public function upload($originalFileName, $objectType) {
        $source = $this->source();
        $fileSize = $source->size();
        $originalFileName = rawurldecode($originalFileName);
        $safeFileName = $this->BeFileHandler->buildNameFromFile($originalFileName);
        $mimeType = ClassRegistry::init('Stream')->getMimeType($source->pwd(), $safeFileName);
        $hashFile = $this->BeFileHandler->getHashFile($source->pwd());

        $user = $this->controller->ApiAuth->identify();
        $eventData = array(
            'uploadableObjects' => $this->controller->ApiValidator->uploadableObjects(),
            'user' => $user
        );
        $event = BeLib::eventManager()->trigger('Api.beforeCheckUpload', $eventData);

        if ($event->result === false || $event->stopped) {
            throw new BeditaInternalErrorException('Error uploading file, some check failed.');
        }
        $objectTypesData = is_array($event->result) ? $event->result : [];

        $this->controller->ApiValidator->checkUploadable(
            $objectType,
            compact('fileSize', 'mimeType', 'originalFileName', 'objectTypesData')
        );

        $objectTypeClass = Configure::read('objectTypes.' . $objectType . '.model');
        $model = ClassRegistry::init($objectTypeClass);
        if (method_exists($model, 'apiUpload')) {
            $targetPath = $model->apiUpload($source, array(
                'fileName' => $safeFileName,
                'hashFile' => $hashFile,
                'user' => $user
            ));
        } else {
            $streamId = $this->BeFileHandler->hashFileExists($hashFile);
            if ($streamId !== false) {
                throw new BeditaConflictException('The file already exists in /objects/' . $streamId);
            }
            $targetPath = $this->put($source, $safeFileName);
        }

        if (empty($targetPath)) {
            throw new BeditaInternalErrorException('Error uploading file');
        }

        return $this->generateToken(array(
            'uri' => $targetPath,
            'name' => pathinfo($targetPath, PATHINFO_BASENAME),
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'original_name' => $originalFileName,
            'hash_file' => $hashFile,
            'object_type' => $objectType
        ));
    }

    /**
     * Read from php://input, put the content in a temporary file and return the File instance
     *
     * @return File
     * @throws BeditaBadRequestException, BeditaInternalErrorException
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
     * @param File $source The source File instance
     * @param string $targetName The file target name
     * @return string
     * @throws BeditaInternalErrorException
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
        $user = $this->controller->ApiAuth->identify();
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

    /**
     * Given an upload token it returns the related data
     *
     * @param string $uploadToken The upload token
     * @param string $objectType The object type related to the upload operation
     * @return array
     */
    public function uploadedFileData($uploadToken, $objectType) {
        $uploadData = $this->validateToken($uploadToken);

        if ($objectType != $uploadData['object_type']) {
            throw new BeditaBadRequestException('upload_token refers to different object type');
        }

        $uploadData = array_intersect_key($uploadData, array_flip(
            array('uri', 'name', 'mime_type', 'file_size', 'original_name', 'hash_file')
        ));

        $objectTypeClass = Configure::read('objectTypes.' . $objectType . '.model');
        $model = ClassRegistry::init($objectTypeClass);
        if (method_exists($model, 'apiUpload')) {
            return $model->apiUploadTransformData($uploadData);
        }

        if ($objectType == 'image') {
            $this->BeFileHandler->setImageData($uploadData);
        }

        return $uploadData;
    }

    /**
     * Validate an `$uploadToken` and return data related
     *
     * @param string $uploadToken The upload token to validate
     * @return array
     * @throws BeditaBadRequestException When the token doesn't exists or is not valid anymore
     */
    public function validateToken($uploadToken) {
        $user = $this->controller->ApiAuth->identify();
        $hashJob = ClassRegistry::init('HashJob');
        $hashRow = $hashJob->find('first', array(
            'conditions' => array(
                'service_type' => 'api_upload',
                'hash' => $uploadToken,
                'user_id' => $user['id']
            )
        ));

        if (empty($hashRow)) {
            throw new BeditaBadRequestException('upload_token ' . $uploadToken . ' not exists');
        }

        if ($hashRow['HashJob']['status'] != 'pending') {
            throw new BeditaBadRequestException('Invalid upload_token. Its status is ' . $hashRow['HashJob']['status']);
        }

        return $hashRow['HashJob'];
    }

    /**
     * Remove `$uploadToken`
     *
     * @param string $uploadToken The upload token to remove
     * @return bool
     */
    public function removeToken($uploadToken) {
        if (empty($uploadToken)) {
            return false;
        }

        $user = $this->controller->ApiAuth->identify();
        $hashJob = ClassRegistry::init('HashJob');
        $hashId = $hashJob->field('id', array(
            'service_type' => 'api_upload',
            'hash' => $uploadToken,
            'user_id' => $user['id']
        ));

        if (!$hashId) {
            return false;
        }

        return $hashJob->delete($hashId);
    }
}
