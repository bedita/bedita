<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2011 ChannelWeb Srl, Chialab Srl
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
 * Utility model class
 * Execute utility operations
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Utility extends AppModel {
    
	public $useTable = false;
	
	/**
	 * response of Utility::call()
	 * @var array, it contains 
	 *				'results' => the results of the operation, it can be an array and depends from operation,
	 *				'message' => message to display at user,
	 *				'log' => log message
	 */
	protected $response = array('results' => null, 'message' => null, 'log' => null);
	
	/**
	 * call a specific utility operation and eventually log errors
	 * 
	 * @param string $operation, the name of the operation. 
	 *				It is also the name of the method called and the name of log file written
	 * @param array $options, default 'log' => false, no file log written
	 *				other options can be seen in the relative utility operation
	 * @return array of response (Utility::response)
	 * @throws BeditaException 
	 */
	public function call($operation, $options = array()) {
		if (!method_exists($this, $operation)) {
			throw new BeditaException(__("Error: utility operation doesn't found"), array("operation" => $operation));
		}
		$options = array_merge(array('log' => false), $options);
		$this->clearResponse();
		$this->{$operation}($options);
		if (empty($this->response["message"])) {
			$this->response["message"] = $operation . " " . __("operation done");
		}
		if ($options['log'] && !empty($this->response["log"])) {
			if (is_array($this->response["log"])) {
				$this->response["log"] = implode(PHP_EOL, $this->response["log"]);
			}
			CakeLog::write($operation, $this->response['log']);
			$this->response['message'] .= __("Some errors occured. For more information look at") . " " . LOGS . $operation . ".log";
		}
		return $this->response;
	}
	
	/**
	 * utility operation
	 * update stream fields according to Stream::updateStreamFields
	 * 
	 * @param array $options 
	 */
	protected function updateStreamFields($options) {
		$this->response['results'] = ClassRegistry::init("Stream")->updateStreamFields();
	}
	
	/**
	 * utility operation
	 * rebuild index for search text according to SearchText::rebuildIndex()
	 * 
	 * @param array $options
	 *				'returnOnlyFailed' => true (default) to return only failed results
	 *				'log' => true to log errors
	 */
	protected function rebuildIndex($options) {
		$returnOnlyFailed = (isset($options['returnOnlyFailed']))? $options['returnOnlyFailed'] : true;
		$this->response['results'] = ClassRegistry::init("SearchText")->rebuildIndex($returnOnlyFailed);
		if (!empty($options['log'])) {
			$msg = "";
			$this->response['log'] = array();
			if (!empty($this->response['results']['failed'])) {
				$this->response['log'][] = "Failed rebuilding indexes for these objects";
				$this->response['log'] += $this->buildLogItems($this->response['results']['failed']);
				$msg .= count($this->response['results']['failed']) . " " . __("objects rebuilding failed.") . PHP_EOL;
			}
			if (!empty($this->response['results']['langTextFailed'])) {
				$this->response['log'][] = "Failed rebuilding translations indexes for these objects";
				$this->response['log'] += $this->buildLogItems($this->response['results']['langTextFailed']);
				$msg .= count($this->response['results']['langTextFailed']) . " " . __("translations rebuilding failed.") . PHP_EOL;
			}
			if (!empty($this->response['log'])) {
				$this->response['message'] = "rebuildIndex " . __("operation done") . PHP_EOL . $msg;
			}
		}
	}
	
	/**
	 * utility operation
	 * clear media cache, remove image thumbs according to Stream::clearMediaCache()
	 * 
	 * @param array $options 
	 *				'log' => true to log errors
	 */
	protected function clearMediaCache($options) {
		$streamModel = ClassRegistry::init("Stream");
		$this->response['results'] = $streamModel->clearMediaCache();
		if ($this->response['results'] === false) {
			$this->response['message'] = __("No streams found");
		} elseif (!empty($this->response['results']['failed'])) {
			if (!empty($options['log'])) {
				$this->response['log'] = $this->buildLogItems($this->response['results']['failed']);
			}
			$msg .= count($this->response['results']['failed']) . " " . __("errors cleaning media cache.") . "\n";
			$this->response['message'] = "clearMediaCache " . __("operation done") . ".\n" . $msg;
		}
	}
	
	/**
	 * utility operation
	 * delete log files according to BeSystem::emptyLogs()
	 * 
	 * @param array $options
	 *				'filename' => null (default delete all log files) the log file to delete,
	 *				'basePath' => LOGS (default) the log directory path
	 *				'log' => true to log errors
	 */
	protected function emptyLogs($options) {
		$options = array_merge(array('filename' => null, 'basePath' => LOGS), $options);
		$this->response['results'] = BeLib::getObject("BeSystem")->emptyLogs($options['filename'], $options['basePath']);
		if (!empty($this->response['results']['failed']) && !empty($options['log'])) {
			$this->response['log'] = $this->buildLogItems($this->response['results']['failed']);
		}
	}
	
	/**
	 * utility operation
	 * cleanup cached files according to BeSystem::cleanupCache()
	 * 
	 * @param array $options 
	 *				'basePath' => TMP (default) the path on which search and clear cache
	 *				'frontendsToo' => true (default) to clean also frontends cache
	 */
	protected function cleanupCache($options) {
		$options = array_merge(array('basePath' => TMP, 'frontendsToo' => true), $options);
		$this->response['results'] = BeLib::getObject("BeSystem")->cleanupCache($options['basePath']);
		if (!empty($this->response['results']['failed']) && !empty($options['log'])) {
			$this->response['log'] = $this->buildLogItems($this->response['results']['failed']);
		}
	}


	
	/**
	 * build an array of log items
	 * loop the $data array and build an array of string built with all key => value pairs
	 * 
	 * @param array $data, multidimensional array as
	 *				0 => array('id' => 1, '.....'fieldname' => 'value_1', ...),
	 *				1 => array('id' => 2, '.....'fieldname' => 'value_2', ...)
	 * @return string 
	 */
	private function buildLogItems(array $data = array()) {
		$log = array();
		foreach ($data as $failItem) {
			$logstring = "";
			foreach ($failItem as $key => $val) {
				$logstring .= $key . " => " . $val . "; ";
			}
			$log[] = $logstring;
		}
		return $log;
	}
	
	/**
	 * clear Utility::response
	 */
	private function clearResponse() {
		$this->response = array('results' => null, 'message' => null, 'log' => null);
	}
}
?>
