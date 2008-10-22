<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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

App::import('Core', 'Error');

/**
 * BEdita/cake error handler (backends+frontends)
 *  
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class AppError extends ErrorHandler {

	protected $error404 = array('missingController', 'missingAction');
	protected $errorTrace = "";
	
	function __construct($method, $messages, $trace="") {
		$debugLevel = Configure::read('debug');
		Configure::write('debug', 1);
		$this->errorTrace = $trace;
		parent::__construct($method, $messages);
		Configure::write('debug', $debugLevel); // restore level
	}

	public function handleException(array $messages) {

		$current = AppController::currentController();
		if(isset($current)) {
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
				$this->controller->log($this->errorTrace);
				$this->sendMail($this->errorTrace);
				$this->controller->set($messages);
				App::import('View', "Smarty");
				$viewObj = new SmartyView($this->controller);
				$this->controller->output = $viewObj->render(null, "error", VIEWS."errors/error500.tpl");				
			}
		} else {
			header('HTTP/1.1 404 Not Found');
			$this->log($this->errorTrace);
			$this->controller->render(null, "error", VIEWS."errors/error404.tpl");
			$this->sendMail($this->errorTrace);
		}
		echo $this->controller->output;		
	}

	public function handleExceptionFrontend(array $messages) {
		$current = AppController::currentController();
		if(isset($current)) {
			$this->controller = $current;
			$this->controller->handleError($messages['details'], $messages['msg'], $this->errorTrace);
		}
		header('HTTP/1.1 404 Not Found');
		$this->controller->set($messages);
		App::import('View', "Smarty");
		$viewObj = new SmartyView($this->controller);
		echo $viewObj->render(null, "error", VIEWS."errors/error404.tpl");				
	}
	
	private function sendMail($mailMsg) {
		$mailSupport = Configure::read('mailSupport');
		if(!empty($mailSupport)) {
			App::import('Core', 'Email');
			$email = new EmailComponent;
			$email->from = $mailSupport['from'];
			$email->to = $mailSupport['to'];
			$email->subject = $mailSupport['subject'];
			$smtpOptions = Configure::read("smtpOptions");
			if (!empty($smtpOptions) && is_array($smtpOptions)) {
				$email->smtpOptions = $smtpOptions;
				$email->delivery = 'smtp';
			}
			if(!$email->send($mailMsg)) {
				$this->log("mail send failed");
			}
		}
	}
	
	function __outputMessage($template) {
		$tpl = "";
		if(in_array($template, $this->error404)) {
			header('HTTP/1.1 404 Not Found');
			$tpl = "error404.tpl";
			$this->log(" 404 Not Found - $template: " . var_export($this->controller->viewVars, TRUE));
		} else {
			header('HTTP/1.1 500 Internal Server Error');
			$tpl = "error500.tpl";
			$errMsg = " 500 Internal Error - $template: " . var_export($this->controller->viewVars, TRUE);
			$this->log($errMsg);
			$this->sendMail($errMsg);
		}
		App::import('View', "Smarty");
		$viewObj = new SmartyView($this->controller);
		echo $viewObj->render(null, "error", VIEWS."errors/" . $tpl);				
	}
}
?>