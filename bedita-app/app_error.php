<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2011-2015 ChannelWeb Srl, Chialab Srl
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
 */
class AppError extends ErrorHandler {

	/**
	 * CakePHP errors that will be treated as 404 http errors
	 * All other CakePHP errors will be treated as 500 http errors
	 *
	 * @var array
	 */
	protected $error404 = array('missingController', 'missingAction');

	/**
	 * The exception error trace
	 *
	 * @var string
	 */
	protected $errorTrace = '';

	/**
	 * Current app debug level
	 * @var int
	 */
	protected $debugLevel;

	/**
	 * The Exception that has been thrown
	 *
	 * @var Exception
	 */
	protected $exception = null;

	/**
	 * Error data set for View
	 * It is set also as '_serialize' view var so that ResponseHandlerComponent can use it to send to the client as payload
	 * In json/xml requests
	 *
	 * Options are
	 *
	 * ```
	 * 'status' => null, // the http status code
	 * 'code' => null, // the error code
	 * 'message' => null, // the error message
	 * 'details' => null, // the error details
	 * 'moreInfo' => null, // the url to look for more information
	 * 'url' => null // the url that has caused the error
	 * ```
	 *
	 * @var array
	 */
	protected $error = array(
		'status' => null,
		'code' => null,
		'message' => null,
		'details' => null,
		'moreInfo' => null,
		'url' => null
	);

	/**
	 * The layout to use (default is 'error')
	 *
	 * @var string
	 */
	protected $layout = 'error';

	function __construct($method, $messages, Exception $exception = null) {
		$this->setLayout();
		try {
			$this->debugLevel = Configure::read('debug');
			Configure::write('debug', 1);
			if (!empty($exception)) {
				App::import('Core', 'Sanitize');
				$options = array('escape' => false);;
				$this->error['message'] = Sanitize::clean($exception->getMessage(), $options);
				if ($exception instanceof BeditaException) {
					$this->error['status'] = $exception->getHttpCode();
			        $this->error['details'] = Sanitize::clean($exception->getDetails(), $options);
			        $messages['result'] = $exception->result;
			        $this->errorTrace = $exception->errorTrace();
			    } else {
			        $messages['result'] = 'ERROR';
			        $this->errorTrace = get_class($exception) . ' - ' . $exception->getMessage()
			            . " \nFile: " . $exception->getFile() . ' - line: ' . $exception->getLine()
			            . " \nTrace:\n" . $exception->getTraceAsString();
			    }
			    $this->exception = $exception;
			}
			$this->error['url'] = $this->getUrl();
			parent::__construct($method, $messages);
		} catch (Exception $e) { // error 500 if another exception is thrown here
			$this->controller->view = 'Smarty';
			$this->controller->output = '';
			$this->setError(array(
				'message' => $e->getMessage(),
				'details' => null,
				'status' => 500,
				'moreInfo' => null
			));
			// log error
			$this->errorTrace = get_class($e). ": ". $e->getMessage()."\nFile: ".$e->getFile().
				" - line: ".$e->getLine()."\nTrace:\n". $e->getTraceAsString();
            $this->log($e->getMessage() .  ' url: ' . $this->getUrl());
            $this->log($this->errorTrace, 'exception');
			$this->sendMail($this->errorTrace);
			$this->controller->set($messages);
			$this->controller->render(null, $this->layout, $this->getViewFile());
			echo $this->controller->output;
			$this->_stop();
		}
	}

	public function restoreDebugLevel() {
		if(isset($this->debugLevel)) {
			Configure::write('debug', $this->debugLevel); // restore level
		}
	}

	/**
	 * Check if Controller has basic initialization
	 *
	 * * Check if params are defined
	 * * Check if RequestHandler and ResponseHandler components are initialized
	 * * Add BeFront helper in frontend apps
	 * * Add $conf to viewVars if missing
	 *
	 * @return void
	 */
	private function checkController() {
		if (empty($this->controller->params)) {
			$this->controller->params = Router::getParams();
		}
		$components = array('RequestHandler', 'ResponseHandler');
		foreach ($components as $name) {
			if (empty($this->controller->$name)) {
				if (App::import('Component', $name)) {
					$completeName = $name . 'Component';
					$this->controller->$name = new $completeName();
					if ($name == 'ResponseHandler') {
						$this->controller->$name->RequestHandler = $this->controller->RequestHandler;
					}
					$this->controller->$name->initialize($this->controller);
					$this->controller->$name->startup($this->controller);
				} else {
					$this->log('AppError: Fail to load ' . $name . ' component.');
				}
			}
		}

		// assure that ResponseHandler is enabled
		$this->controller->ResponseHandler->enabled = true;

		if (BACKEND_APP) {
			// check backend helpers
			$checkHelpers = array('Beurl', 'BeForm', 'Session', 'SessionFilter');
			// assure to use Smarty view
			$this->controller->view = 'Smarty';
		} else {
			// check frontend helpers
			$checkHelpers = array('BeFront');
		}

		foreach ($checkHelpers as $helperName) {
			if (!in_array($helperName, $this->controller->helpers)) {
				$this->controller->helpers[] = $helperName;
			}
		}

		// assure to have $conf in viewVars
		if (empty($this->controller->viewVars['conf'])) {
			$this->controller->set('conf', Configure::getInstance());
		}
	}

	/**
	 * Set the layout to use
	 * If Request is ajax set ajax layout
	 *
	 * @param string $layout
	 * @return void
	 */
	protected function setLayout($layout = 'error') {
		if (env('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
			$layout = 'ajax';
		}
        $this->layout = $layout;
	}

	/**
	 * Set self::error to View vars and add also it to '_serialize' for ResponseHandlerComponent
	 *
	 * @param array $error
	 */
	protected function setError(array $error = array()) {
		if (!empty($error)) {
			$this->error = $error + $this->error;
		}
		// default http status code to 500
		if (empty($this->error['status'])) {
			$this->error['status'] = 500;
		}
		$this->controller->ResponseHandler->sendStatus($this->error['status']);
		$this->controller->set(array(
			'error' => $this->error,
			'_serialize' => array('error')
		));
	}

	/**
	 * Return the error template to use
	 * The template depends from http status code triggered from Exception
	 *
	 * First it checks if exists 'views/errors/error' . self::exception::httpCode() . '.tpl' (or .ctp)
	 * If it not exists then it return the more general 'views/errors/error.tpl' (or .ctp)
	 *
	 * @return string
	 */
	protected function getViewFile() {
		$baseFileName = 'error';
		$httpCode = (method_exists($this->exception, 'getHttpCode')) ? $this->exception->getHttpCode() : '';
		$fileName = VIEWS . 'errors/' . $baseFileName . $httpCode . $this->controller->ext;
		if (file_exists($fileName)) {
			return $fileName;
		} else {
			return VIEWS . 'errors/' . $baseFileName . $this->controller->ext;
		}
	}

	public function handleException(array $messages) {
		$this->checkController();
		if ($this->controller->RequestHandler->isAjax() && BACKEND_APP) {
			$messages['output'] = ($this->controller->ResponseHandler->getType() == 'json') ? 'json' : 'beditaMsg';
			$messages['headers'] = array('HTTP/1.1 500 Internal Server Error');
			return $this->handleBeditaAjaxException($messages);
		}
		$this->restoreDebugLevel();
		$url = AppController::usedUrl();
		$current = AppController::currentController();
		if (isset($current)) {
			$this->controller = $current;
			$this->checkController();
			$this->controller->handleError($this->error['details'], $this->error['message'], $this->errorTrace);
			$this->controller->setResult($messages['result']);
			$this->setError();
			if (BACKEND_APP) {
				$this->controller->render($this->controller->action);
			} else {
				$this->controller->render(null, $this->layout, $this->getViewFile());
			}
			$this->controller->afterFilter();
		} else {
			$this->setError();
            $this->log($this->error['details'] . $url);
            $this->log($this->errorTrace, 'exception');
			$this->controller->render(null, $this->layout,  $this->getViewFile());
			$this->sendMail($this->errorTrace);
		}
		echo $this->controller->output;
	}
	
	public function handleSmartyException(array $messages) {
		$this->error['message'] = 'Smarty error';
		$this->error['details'] = $this->exception->getMessage();
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
	public function handleBeditaAjaxException(array $messages) {
		if (empty($messages['output'])) {
			$messages['output'] = $this->exception->getOutputType();
		}

		$headers = (!empty($messages['headers'])) ? $messages['headers'] : $this->exception->getHeaders();

		if ($headers !== null) {
			if (empty($headers)) {
				$headers = array('HTTP/1.1 500 Internal Server Error');
			} elseif (is_string($headers)) {
				$headers = array($headers);
			}
			// output headers
			foreach ($headers as $header) {
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
			$this->controller->set("errorMsg",  $this->error['message']);
			$usrMsgParams = array();
		}
		$this->restoreDebugLevel();
		$this->controller->handleError($this->error['details'], $this->error['message'], $this->errorTrace, $usrMsgParams);
		App::import('View', "Smarty");
		$viewObj = new SmartyView($this->controller);
		echo $viewObj->render(null, "ajax", VIEWS."errors/error_ajax.tpl");
	}
	
	/**
	 * @deprecated
	 * @param array $messages
	 * @return void
	 */
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
		echo $viewObj->render(null, $this->layout, VIEWS."errors/error500.tpl");				
	}
	
	/**
	 * @deprecated
	 * @param array $messages
	 * @return void
	 */
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
		echo $viewObj->render(null, $this->layout, VIEWS."errors/error404.tpl");				
	}
	
	/**
	 * @deprecated
	 * @param array $messages
	 * @return void
	 */
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
		$this->checkController();
		$tpl = "";
		$vars = array();
		$vars['url'] = $this->error['url'] = $this->getUrl();
		$this->error['message'] = $this->getMessage();
		if (in_array($template, $this->error404)) {
			$this->error['status'] = 404;
			if (!empty($this->controller->viewVars["BEAuthUser"])) {
				// remove unwanted data
				$vars["userid"] = $this->controller->viewVars["BEAuthUser"]["userid"];
			}
			if (!empty($this->controller->viewVars["controller"])) {
				$vars["controller"] = $this->controller->viewVars["controller"];
			}
			$tpl = "error404.tpl";
			$this->log(" 404 Not Found - $template: " . var_export($vars, TRUE));
		} else {
			$this->error['status'] = 500;
			$vars = array_merge($vars, $this->controller->viewVars);
			if (!empty($vars["conf"])) {
				unset($vars["conf"]);
			}
			if (!empty($_POST)) {
				$vars["post"] = $_POST;
			}
			$tpl = "error500.tpl";
			$errMsg = " 500 Internal Error - $template: " . var_export($vars, TRUE);
			$this->log($errMsg);
			$this->sendMail($errMsg);
		}
		if (empty($this->controller->viewVars["errorType"])) {
			$this->controller->set("errorType", $template);
		}
		$this->restoreDebugLevel();
		$this->setError();
		$this->controller->render(null, $this->layout, VIEWS."errors/" . $tpl);
		$this->controller->afterFilter();
		echo $this->controller->output;
	}

	/**
	 * Return the request url
	 *
	 * @return string|null
	 */
	public function getUrl() {
		$url = env('REQUEST_URI');
		if (empty($url)) {
			if (!empty($this->controller->params['url']['url'])) {
				$url = $this->controller->params['url']['url'];
			} elseif (!empty($_GET['url'])) {
				$url = $_GET['url'];
			} elseif (!empty($_POST['url'])) {
				$url = $_POST['url'];
			}
		}
		$url = Router::normalize($url);
		return Router::url($url, true);
	}

	/**
	 * Return the error message
	 * If an exception has created the error use its message
	 * else try to read some View vars set by CakePHP
	 *
	 * @return string
	 */
	public function getMessage() {
		$message = '';
		if ($this->exception) {
			$message = $this->exception->getMessage();
		} elseif (!empty($this->controller->viewVars['name'])) {
			$message = $this->controller->viewVars['name'];
		} elseif (!empty($this->controller->viewVars['message'])) {
			$message = $this->controller->viewVars['message'];
		} elseif (!empty($this->controller->viewVars['title'])) {
			$message = $this->controller->viewVars['title'];
		}
		return $message;
	}
}
