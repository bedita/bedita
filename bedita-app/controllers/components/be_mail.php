<?php

class BeMailComponent extends Object {

	var $components = array("Email");
	
	/**
	 * startup component
	 * set smtp options if it's in cofiguration (bedita.cfg.php)	
	 * @param unknown_type $controller
	 */
	function startup(&$controller) {
		$this->controller = &$controller;
		$smtpOptions = Configure::read("smtpOptions");
		if (!empty($smtpOptions) && is_array($smtpOptions)) {
			$this->Email->smtpOptions = $smtpOptions;
			$this->Email->delivery = 'smtp';
		}
	}
	
	/**
	 * send single mail from mail_messages table
	 *
	 * @param int $msg_id
	 * @param strinf $to, recipient email
	 */
	public function sendMailById($msg_id=null, $to=null) {
		if (empty($msg_id))
			throw new BeditaException(__("Missing message id", true));
		
		// TODO get and format data
		
		$this->sendMail($data);
	}
	
	/**
	 * send single mail from $data array
	 *
	 * @param array $data
	 */
	public function sendMail($data=array()) {
		if (!$this->send($data))
			throw new BeditaMailException(__("Mail delivery failed", true), $this->Email->smtpError);
	}
	
	/**
	 * execute active jobs
	 *
	 */
	public function sendQueuedJobs() {}
	
	/**
	 * prepare data for email sending
	 *
	 * @param array $data
	 */
	private function prepareData($data) {
		$this->Email->reset();
		
		// check required fields
		if (empty($data["to"]))
			throw new BeditaException(__("Missing recipient", true));
		
		if (empty($data["from"]))
			throw new BeditaException(__("Missing from field", true));
		
		$this->Email->to = $data["to"];
		$this->Email->from = $data["from"]; 
		$this->Email->subject = (!empty($data["subject"]))? $data["subject"] : __("no subject", true);
		$this->Email->replyTo = (!empty($data["replayTo"]))? $data["replayTo"] : "";
		$this->Email->sendAs = (!empty($data["mailType"]))? $data["mailType"] : "html";
		if (!empty($data["cc"]) && is_array($data["cc"]))
			$this->Email->cc = $data["cc"];
		if (!empty($data["bcc"]) && is_array($data["bcc"]))
			$this->Email->bcc = $data["bcc"];
	}
	
	private function send($data) {
		$this->prepareData($data);
		if (!$this->Email->send($data["body"]))
			return false;
			
		return true;
	}
	
}


/**
 * BeditaMailException
 *
 */
class BeditaMailException extends BeditaException
{
}

?>