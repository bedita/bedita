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
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeMailComponent extends Object {

	var $components = array("Email");
	
	/**
	 * startup component
	 * set smtp options if it's in cofiguration (bedita.sys.php)	
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
	 * @param bool $html
	 */
	public function sendMailById($msg_id, $to, $html=true) {
		if (empty($msg_id) || empty($to))
			throw new BeditaException(__("Missing message id or recipient", true));
		
		$mailMsgModel = ClassRegistry::init("MailMessage");
		$mailMsgModel->containLevel("default");
		if (!$res = $mailMsgModel->findById($msg_id))
			throw new BeditaException(__("Error finding mail message " . $msg_id, true));
		
		$data["to"] = $to;
		$data["from"] = $res["sender"];
		$data["subject"] = $res["subject"];
		$data["replyTo"] = $res["reply_to"];
		$data["mailType"] = ($html)? "html" : "txt";
		
		$data["body"] = $this->prepareMailBody($res, $html);

		$this->sendMail($data);
	}
	
	/**
	 * Prepare mail body using template
	 *
	 * @param array $message, mail_message array from a find on MailMessage 
	 * @param bool $html, mail type
	 * @return body (html or txt) of the message
	 */
	private function prepareMailBody($message, $html=true) {
		
		if (!empty($message["RelatedObject"]) && $message["RelatedObject"][0]["switch"] == "template") {

			$mailTemplate = ClassRegistry::init("MailTemplate");
			$template = $mailTemplate->find("first", array(
					"conditions" => array("MailTemplate.id" => $message["RelatedObject"][0]["object_id"]),
					"contain" => array("Content")
				)
			);
			
			if ($html) {
				// get css
				$treeModel = ClassRegistry::init("Tree");
				$pub_id = $treeModel->getParent($template["id"]);
				$areaModel = ClassRegistry::init("Area");
				$publicationUrl = $areaModel->field("public_url", array("id" => $pub_id));
				
				$css = (!empty($publicationUrl))? $publicationUrl . "/css/" . Configure::read("newsletterCss") : "";
				$htmlMsg = "<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"" . $css . "\" /></head><body>%s</body></html>";
				$htmlBody = str_replace("[\$newsletterTitle]", $message["title"], $template["body"]);
				$htmlBody = preg_replace("/<!--bedita content block-->[\s\S]*<!--bedita content block-->/", $message["body"], $htmlBody);
				$htmlBody = str_replace("[\$signature]", $message["signature"], $htmlBody);
				$htmlBody = str_replace("[\$privacydisclaimer]", $message["privacy_disclaimer"], $htmlBody);
				$body = sprintf($htmlMsg, $htmlBody);
			} else {
				$txtBody = str_replace("[\$newsletterTitle]",  strip_tags($message["title"]), $template["abstract"]);
				$txtBody = preg_replace("/<!--bedita content block-->[\s\S]*<!--bedita content block-->/", $message["abstract"], $txtBody);
				$txtBody = str_replace("[\$signature]", $message["signature"], $txtBody);
				$body = str_replace("[\$privacydisclaimer]", $message["privacy_disclaimer"], $txtBody);
			}
			
			return $body;
			
		}
		
		return ($html)? $message["body"] : $message["abstract"];
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
		
		$msgToSend = $mailMsgModel->find("all", array(
									"conditions" => array(
										"MailMessage.mail_status" => "pending",
										"MailMessage.id" => $msgIds
										),
									"contain" => array("BEObject" => array("RelatedObject"), "Content", "MailGroup")
									)
								);

		$groupCardModel = ClassRegistry::init("MailGroupCard");
		$groupCardModel->contain("Card");
		
		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("default");

		$data["status"] = "unsent";
		
		foreach ($msgToSend as $message) {
					
			$data["mail_message_id"] = $message["id"];
			
			if (!empty($message["MailGroup"])) {
				foreach ($message["MailGroup"] as $group) {
				
					$res = $groupCardModel->find("all", array(
						"conditions" => array(
							"mail_group_id" => $group["id"],
							"Card.mail_status" => "valid")
						)
					);

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
							
							// prepare mail body using template
							$data["mail_body"] = $this->prepareMailBody($message, $groupCard["Card"]["mail_html"]);
							
							$jobModel->create();
							if (!$jobModel->save($data))
								throw new BeditaException(__("Error creating jobs"),true);
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
				$data["replyTo"] = $job["MailMessage"]["reply_to"];
				$data["subject"] = $job["MailMessage"]["Content"]["subject"];
				$data["mailType"] = (!empty($job["Card"]["mail_html"]))? "html" : "txt";
				$data["body"] = $job["MailJob"]["mail_body"];
				
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
	
	public function notify() {

		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("minimum");
		$conditions = array("mail_message_id is NULL" , 
			"status" => "unsent");

		$jobsToSend = $jobModel->find('all', array("conditions" => $conditions));
		
		foreach ($jobsToSend as $job) {
			$jobModel->id = $job['MailJob']['id'];
			$jobModel->saveField("status", "pending");
		}
		
		$data = array();
		foreach ($jobsToSend as $job) {
			$mailParams = unserialize($job['MailJob']['mail_params']);
			$data["to"] = $job["MailJob"]["recipient"];
			$data["from"] = $mailParams["sender"];
			$data["replyTo"] = $mailParams["reply_to"];
			$data["subject"] = $mailParams["subject"];
			$data["mailType"] = "txt";
			$data["body"] = $job["MailJob"]["mail_body"];
			if(!empty($mailParams["signature"])) {
				$data["body"] .= "\n\n--\n" . $mailParams["signature"];
			}
			$jobModel->id = $job['MailJob']['id'];
			if (!$this->send($data)) {
				$this->log(__("Notification mail delivery failed", true) . "-" . $this->Email->smtpError);
				$jobModel->saveField("status", "failed");
			} else {
				$jobModel->saveField("status", "sent");
				$jobModel->saveField("sending_date", date("Y-m-d H:i:s"));
			}
		}
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
		$this->Email->replyTo = (!empty($data["replyTo"]))? $data["replyTo"] : "";
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