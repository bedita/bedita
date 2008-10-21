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

/**
 * General BEdita mail component
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: $
 * @modifiedby 		$LastChangedBy: $
 * @lastmodified	$LastChangedDate: $
 * 
 * $Id: $
 */

class BeMailComponent extends Object {

	var $components = array("Email");
	
	/**
	 * startup component
	 * set smtp options if it's in cofiguration (bedita.cfg.php)	
	 * @param unknown_type $controller
	 */
	function startup(&$controller=null) {
		if ($controller === null) {
			foreach ($this->components as $comp) {
				App::import('Component', $comp);
				$componentName = $comp . "Component";
				$this->{$comp} = new $componentName() ;
			}
		} else {
			$this->controller = &$controller;	
		}
		
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
	public function sendMailById($msg_id, $to=null) {
		if (empty($msg_id))
			throw new BeditaException(__("Missing message id", true));
		
		$mailMsgModel = ClassRegistry::init("MailMessage");
		$mailMsgModel->containLevel("default");
		if (!$res = $mailMsgModel->findById($msg_id))
			throw new BeditaException(__("Error finding mail message " . $msg_id, true));
		
		$data["to"] = $to;
		$data["from"] = $res["sender"];
		$data["subject"] = $res["subject"];
		$data["replayTo"] = $res["replay_to"];
		$data["mailType"] = "html";
		$data["body"] = $res["body"];

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
	 * set to pending messages with status=unsent and start_sending <= now
	 *
	 */
	public function lockMessages() {
		
		$msgIds = array();
		$mailMsgModel = ClassRegistry::init("MailMessage");
		$mailMsgModel->containLevel("mailgroup");
		
		$msgToLock = $mailMsgModel->find("all", array(
									"conditions" => array(
											"MailMessage.mail_status" => "unsent",
											"MailMessage.start_sending <= '" . date("Y-m-d H:i:s",time()) . "'",
											)
									)
								);
		
		if (!empty($msgToLock)) {
			
			foreach ($msgToLock as $key => $message) {
				if (!empty($message["MailGroup"])) {
					$mailMsgModel->id = $message["id"];
					if (!$mailMsgModel->saveField("mail_status", "pending")) {
						throw new BeditaException(__("Mail message lock failed: id " . $message["id"]), true);
					}
					
					$msgIds[] = $message["id"];
				}
			}
			
		}
		
		return $msgIds;
								
	}
	
	/**
	 * create jobs from message with status pending
	 */
	public function createJobs(array $msgIds) {
		
		if (empty($msgIds))
			return ;
			
		$mailMsgModel = ClassRegistry::init("MailMessage");
		$mailMsgModel->containLevel("mailgroup");
		
		$msgToSend = $mailMsgModel->find("all", array(
									"conditions" => array(
										"MailMessage.mail_status" => "pending",
										"MailMessage.id" => $msgIds
										)
									)
								);

		$groupCardModel = ClassRegistry::init("MailGroupCard");
		$groupCardModel->recursive = -1;
	
		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("default");

		$data["status"] = "unsent";
		
		foreach ($msgToSend as $message) {
					
			$data["mail_message_id"] = $message["id"];
			
			if (!empty($message["MailGroup"])) {
				foreach ($message["MailGroup"] as $group) {
				
					$res = $groupCardModel->find("all", array("conditions" => array("mail_group_id" => $group["id"])));

					foreach ($res as $groupCard) {
						// create job only if it dosen't exist
						if ($jobModel->find("count", array(
														"conditions" => array(
																"card_id" => $groupCard["MailGroupCard"]["card_id"],
																"mail_message_id" => $message["id"]
																)
															)
											) == 0) {
							$data["card_id"] = $groupCard["MailGroupCard"]["card_id"];
							$jobModel->create();
							if (!$jobModel->save($data))
								throw new BeditaException(__("Error create jobs"),true);
						}
					}
					
				}
			}
		}	
	}
	
	
	/**
	 * execute active jobs
	 *
	 */
	public function sendQueuedJobs(array $msgIds) {
		
		if (empty($msgIds))
			return ;
		
		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("detailed");
		$jobsToDo = $jobModel->find("all", array(
								"conditions" => array(
									"MailJob.status" => "unsent",
									"MailJob.mail_message_id" => $msgIds
								)
							)
						);
						
		$messagesSent = array();
		
		foreach ($jobsToDo as $job) {
			
			if (!in_array($job["MailMessage"]["id"], $messagesSent)) {
				$messagesSent[] = $job["MailMessage"]["id"];
			}
			
			if ($job["Card"]["mail_status"] == "valid") {
				$data["to"] = $job["Card"]["newsletter_email"];
				$data["from"] = $job["MailMessage"]["sender"];
				$data["replayTo"] = $job["MailMessage"]["replay_to"];
				$data["subject"] = $job["MailMessage"]["Content"]["subject"];
				$data["mailType"] = (!empty($job["Card"]["mail_html"]))? "html" : "txt";
				$data["body"] = $job["MailMessage"]["Content"]["body"];
				
				unset($job["MailMessage"]);
				unset($job["Card"]);
					
				try {
					$this->sendMail($data);
					$job["MailJob"]["sending_date"] = date("Y-m-d H:i:s",time());
					$job["MailJob"]["status"] = "sent";
					$jobModel->save($job);
				} catch(BeditaMailException $ex) {
					$job["MailJob"]["status"] = "failed";
					$jobModel->save($job);
					$this->log($ex->errorTrace());
				}
			}
		}

		// set messages mail_status to sent
		$mailMsgModel = ClassRegistry::init("MailMessage");
		$mailMsgModel->Behaviors->disable('ForeignDependenceSave'); 		
		foreach ($messagesSent as $id) {
			$dataMsg["id"] = $id;
			$dataMsg["mail_status"] = "sent";
			$dataMsg["end_sending"] = date("Y-m-d H:i:s");
			$mailMsgModel->save($dataMsg, false);
		}
		$mailMsgModel->Behaviors->disable('ForeignDependenceSave'); 
		
	}
	
	
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
			
		if (empty($data["subject"]))
			throw new BeditaException(__("Missing subject field", true));
		
		$this->Email->to = $data["to"];
		$this->Email->from = $data["from"]; 
		$this->Email->subject = $data["subject"];
		$this->Email->replyTo = (!empty($data["replayTo"]))? $data["replayTo"] : "";
		$this->Email->sendAs = (!empty($data["mailType"]))? $data["mailType"] : "txt";
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