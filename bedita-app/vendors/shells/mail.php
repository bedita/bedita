<?php

App::import('Core', 'Controller');
App::import('Controller', 'App'); // BeditaException
App::import('Component', 'BeMail');

class MailShell extends Shell {
	
	var $BeMail;

	/**
	 * startup method for MailShell, initialize BeMail Component
	 * override startup method in Shell
	 * 
	 * @return 
	 */
	function startup() {
		if (!empty($this->Dispatch->shellCommand) && $this->Dispatch->shellCommand != "main") 
			$this->_welcome();
		
		$this->BeMail = new BeMailComponent();
		$this->BeMail->startup(); 
	}

	
	function main() {

		try {
			$msgIds = $this->BeMail->lockMessages();
			$this->BeMail->createJobs($msgIds);
			$this->BeMail->sendQueuedJobs($msgIds);
				
		} catch (BeditaException $ex) {
			$this->log("Error: " . $ex->errorTrace());
		}
	
	}
	
	
	function help() {
		$this->out("Shell script to send newsletters");
	}
	
}
?>
