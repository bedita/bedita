<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 *
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class FilesController extends AppController {

	var $helpers 	= array('Html');
	var $uses		= array('Stream','BEObject') ;
	var $components = array('Transaction', 'BeUploadToObj', 'RequestHandler', 'BeSecurity');

	function upload() {
		try {
			$this->Transaction->begin();
			$id = $this->BeUploadToObj->upload();
			$this->Transaction->commit();
			$response = array('id' => $id);
		} catch (BeditaException $ex) {
			throw new BeditaAjaxException($ex->getMessage(), array(
				'output' => 'json',
				'headers' => array('HTTP/1.1 409 Conflict')
			));
		}

		$this->layout = 'ajax';
		$this->RequestHandler->respondAs('json');
        $this->set('data', $response);
        $this->view = 'View';
        $this->render('/pages/json');
	}

	function uploadAjax($uploadSuffix = null) {
		$this->layout = "ajax";
		try {
			$this->Transaction->begin() ;
			$formUploadFields = 'streamUploaded';
			$formFileName = 'Filedata';
			if (!empty($uploadSuffix)) {
				$formUploadFields .= $uploadSuffix;
				unset($this->params["form"]["Filedata"]);
				$formFileName .= $uploadSuffix;
			}
			$this->params['form'][$formUploadFields]['lang'] = $this->data["lang"];
			$id = $this->BeUploadToObj->upload($this->params["form"][$formUploadFields],$formFileName) ;
			$this->Transaction->commit();
			$this->set("fileId", $id);
			$this->set("fileUploaded", true);
		} catch(BeditaException $ex) {
			$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
			$this->set("errorMsg", $ex->getMessage());
		}
	}

	function uploadAjaxMediaProvider() {
		$this->layout = "ajax";
		header("Content-Type: application/json");
		try {
			if (!isset($this->params['form']['uploadByUrl']['url']))
				throw new BEditaException(__("Error during upload: missing url",true)) ;

			$this->params['form']['uploadByUrl']['lang'] = $this->data["lang"];

			$this->Transaction->begin() ;
			$id = $this->BeUploadToObj->uploadFromURL($this->params['form']['uploadByUrl']) ;
			$this->Transaction->commit();
			$this->set("fileId", $id);

		} catch(BeditaException $ex) {
			$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
			$this->set("errorMsg", $ex->getMessage());
		}
	}

	/**
	 * Delete a Stream object (using _POST filename to find stream)
	 */
	function deleteFile() {
 		if(!isset($this->params['form']['filename'])) throw new BeditaException(sprintf(__("No data", true), $id));
	 	$this->Transaction->begin() ;
	 	// Get object id from filename
		if(!($id = $this->Stream->getIdFromFilename($this->params['form']['filename']))) throw new BeditaException(sprintf(__("Error getting id object: %s", true), $this->params['form']['filename']));
	 	// Delete data
	 	if(!$this->BeFileHandler->del($id)) throw new BeditaException(sprintf(__("Error deleting object: %d", true), $id));
	 	$this->Transaction->commit() ;
	 	$this->layout = "empty" ;
	}

	protected function beforeCheckLogin() {
		// multiple upload
		if ($this->RequestHandler->isFlash()) {
			$this->skipCheck = true;
		}
	}

	/**
	 * Override AppController handleError to not save message in session
	 */
	public function handleError($eventMsg, $userMsg, $errTrace) {
		$this->log($errTrace);
		// end transactions if necessary
		if($this->Transaction->started()) {
			$this->Transaction->rollback();
		}
	}

    protected function forward($action, $result) {
        $redirect = array(
            'uploadAjax' => array(
                'OK' => self::VIEW_FWD . 'upload_ajax_response',
                'ERROR' => self::VIEW_FWD . 'upload_ajax_response'
            ),
            'uploadAjaxMediaProvider' => array(
                'OK' => self::VIEW_FWD . 'upload_ajax_response',
                'ERROR' => self::VIEW_FWD . 'upload_ajax_response'
            )
        );
        if (isset($redirect[$action][$result])) {
            return $redirect[$action][$result];
        }
        return false;
    }

}
