<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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

App::import('Core', 'Error');

/**
 * BEdita/cake error handler (backends+frontends)
 *  
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class AppError extends ErrorHandler {

	protected $error404 = array('missingController', 'missingAction');
	protected $errorTrace = "";
	protected $debugLevel;
	
	function __construct($method, $messages, $trace="") {
		$this->debugLevel = Configure::read('debug');
		Configure::write('debug', 1);
		$this->errorTrace = $trace;
		parent::__construct($method, $messages);
		$this->restoreDebugLevel();
	}

	public function restoreDebugLevel() {
		if(isset($this->debugLevel)) {
			Configure::write('debug', $this->debugLevel); // restore level
		}
	}
	
	public function handleException(array $messages) {

		$this->restoreDebugLevel();
		$url = AppController::usedUrl();
		$current = AppController::currentController();
		if (isset($current)) {
			try {
				$this->controller = $current;
				$this->controller->handleError($messages['details'], $messages['msg'], $this->errorTrace);
				$this->controller->setResult($messages['result']);
				$this->controller->render($this->controller->action);
				$this->controller->afterFilter();
			} catch (Exception $e) { // error 500 if another exception is thrown here
				header('HTTP/1.1 500 Internal Server Error');
				// log error
				$this->errorTrace = get_class($e). ": ". $e->getMessage()."\nFile: ".$e->getFile().
					" - line: ".$e->getLine()."\nTrace:\n". $e->getTraceAsString();
                $this->log($e->getMessage() . $url);
                $this->log($this->errorTrace, 'exception');
				$this->sendMail($this->errorTrace);
				$this->controller->set($messages);
				App::import('View', "Smarty");
				$viewObj = new SmartyView($this->controller);
				$this->controller->output = $viewObj->render(null, "error", VIEWS."errors/error500.tpl");				
			}
		} else {
			header('HTTP/1.1 404 Not Found');
            $this->log($messages['details'] . $url);
            $this->log($this->errorTrace, 'exception');
			$this->controller->render(null, "error", VIEWS."errors/error404.tpl");
			$this->sendMail($this->errorTrace);
		}
		echo $this->controller->output;		
	}
	
	public function handleSmartyException(array $messages) {
		$messages['msg'] = 'Smarty error';
		$this->handleException($messages);
	}

	/**
	 * handle ajax exception
	 * 
	 * @param array $messages 
	 *				'msg' => the exception message
	 *				'details' => the error detail
	 *				'output' => the output type. It can be:
	 *							'html' (default) BEdita html standard error message from elements/message.tpl
	 *							'json' json object is built in view as:
	 *									{
	 *										errorMsg: exception message, 
	 *										htmlMsg: BEdita html standard error message from see elements/message.tpl
	 *									}
	 *							'beditaMsg' output the BEdita html standard error message and trigger it
	 *							'reload' javascript: location.reload(); used for example when the session expired in a ajax call
	 *       		'headers' => server headers (usually header error): if it's not set or === null no headers error will be sent
	 *         															if it's set and empty use "HTTP/1.1 500 Internal Server Error"
	 */
	public function handleAjaxException(array $messages) {
		if (empty($messages['output'])) {
			$messages['output'] = "html";
		}

		if (isset($messages['headers']) && $messages['headers'] !== null) {
			if (empty($messages['headers'])) {
				$messages['headers'] = array("HTTP/1.1 500 Internal Server Error");
			} elseif (is_string($messages['headers'])) {
				$messages['headers'] = array($messages['headers']);
			}
			// output headers
			foreach ($messages['headers'] as $header) {
				header($header);
			}
		}

		$this->controller->set("output", $messages['output']);
		// html (default fallback)
		$usrMsgParams = array("layout" => "", "params" => array());
		if ($messages['output'] == "beditaMsg" || $messages['output'] == "reload") {
			$usrMsgParams = array();
		} elseif ($messages['output'] == "json") {
			header("Content-Type: application/json");
			$this->controller->set("errorMsg",  $messages['msg']);
			$usrMsgParams = array();
		}
		$this->restoreDebugLevel();
		$this->controller->handleError($messages['details'], $messages['msg'], $this->errorTrace, $usrMsgParams);
		App::import('View', "Smarty");
		$viewObj = new SmartyView($this->controller);
		echo $viewObj->render(null, "ajax", VIEWS."errors/error_ajax.tpl");
	}
	
	public function handleExceptionRuntime(array $messages) {
		$current = AppController::currentController();
		if(isset($current)) {
			$this->controller = $current;
			$this->controller->handleError($messages['details'], $messages['msg'], $this->errorTrace);
		}
		header('HTTP/1.1 500 Internal Server Error');
		$this->controller->set($messages);
		$this->restoreDebugLevel();
		App::import('View', "Smarty");
		$viewObj = new SmartyView($this->controller);
		echo $viewObj->render(null, "error", VIEWS."errors/error500.tpl");				
	}
	
	public function handleExceptionFrontend(array $messages) {
		$current = AppController::currentController();
		if(isset($current)) {
			$this->controller = $current;
			$this->controller->handleError($messages['details'], $messages['msg'], $this->errorTrace);
		}
		header('HTTP/1.1 404 Not Found');
		$this->controller->set($messages);
		$this->restoreDebugLevel();
		App::import('View', "Smarty");
		$viewObj = new SmartyView($this->controller);
		echo $viewObj->render(null, "error", VIEWS."errors/error404.tpl");				
	}
	
	public function handleExceptionFrontAccess(array $messages) {
		if (isset($messages['headers']) && $messages['headers'] !== null) {
			if (empty($messages['headers'])) {
				$messages['headers'] = array("HTTP/1.1 401 Unauthorized");
			} elseif (is_string($messages['headers'])) {
				$messages['headers'] = array($messages['headers']);
			}
			// output headers
			foreach ($messages['headers'] as $header) {
				header($header);
			}
		}
		$currentController = AppController::currentController();
		$currentController->set($messages);
		$this->restoreDebugLevel();
		$currentController->handleError($messages['details'], $messages['msg'], $this->errorTrace);
		if ($messages["errorType"] == "unlogged") {
			$viewName = "login";
		} else {
			$viewName = $messages["errorType"];
		}
		$viewFile = (file_exists(VIEWS."pages".DS.$viewName.".tpl"))? VIEWS."pages".DS.$viewName.".tpl" : VIEWS."pages".DS.$viewName.".ctp";
		echo $currentController->render(null,null,$viewFile);
	}
	
	private function sendMail($mailMsg) {
		$mailSupport = Configure::read('mailSupport');
		if (!empty($mailSupport['to'])) {
			$jobModel = ClassRegistry::init("MailJob");
			$jobModel->containLevel("default");
			$data = array();
			$data["status"] = "unsent";
			$data["mail_params"] = serialize(array(
						"sender" => $mailSupport['from'], 
						"subject" => $mailSupport['subject'],
			));
			$data["mail_body"] = $mailMsg;
			$dest = explode(',', $mailSupport['to']);
			foreach ($dest as $d) {
				$data["recipient"] = $d;
				$jobModel->create();
				if (!$jobModel->save($data)) {
					$this->log(__("Error creating mail job") . "-" . print_r($data,true),true);
				}
			}

		}
	}

	// use cake output only in debug mode
	function _outputMessage($template) {
		$this->__outputMessage($template);
	}
	
	function __outputMessage($template) {
		$tpl = "";
		if(empty($this->controller->viewVars["conf"])) {
			$this->controller->set('conf', Configure::getInstance());
		}
		$vars = array();
		if(!empty($_GET["url"])) {
			$vars["url"] = $_GET["url"];
		}
		if(in_array($template, $this->error404)) {
			if(!empty($this->controller->viewVars["BEAuthUser"])) {
				// remove unwanted data
				$vars["userid"] = $this->controller->viewVars["BEAuthUser"]["userid"];
			}
			if(!empty($this->controller->viewVars["controller"])) {
				$vars["controller"] = $this->controller->viewVars["controller"];
			}
			header('HTTP/1.1 404 Not Found');
			$tpl = "error404.tpl";
			$this->log(" 404 Not Found - $template: " . var_export($vars, TRUE));
		} else {
			$vars = array_merge($vars, $this->controller->viewVars);
			if(!empty($vars["conf"])) {
				unset($vars["conf"]);
			}
			if(!empty($_POST)) {
				$vars["post"] = $_POST;
			}
			header('HTTP/1.1 500 Internal Server Error');
			$tpl = "error500.tpl";
			$errMsg = " 500 Internal Error - $template: " . var_export($vars, TRUE);
			$this->log($errMsg);
			$this->sendMail($errMsg);
		}
		if(empty($this->controller->viewVars["errorType"])) {
			$this->controller->set("errorType", $template);
		}
		$this->restoreDebugLevel();
		App::import('View', "Smarty");
		$viewObj = new SmartyView($this->controller);
		echo $viewObj->render(null, "error", VIEWS."errors/" . $tpl);				
	}
}
?>