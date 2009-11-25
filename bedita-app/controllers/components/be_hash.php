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
 * General BEdita hash component
 * 
 * @version			
 * @modifiedby 		
 * @lastmodified	
 * 
 * $Id: be_newsletter.php
 */
class BeHashComponent extends Object {

	private $controller;
	private $notifyMsg = null;
	public $components = array("BeMail");
	
	/**
	 * startup component
	 * @param Controller $controller
	 */
	function startup(&$controller=null) {
		$this->controller = &$controller;
		$this->BeMail->startup($this->controller);
		$this->loadMessages();
	}
	
	public function getHashRow($hash) {
		$hashModel = ClassRegistry::init("HashJob");
		$hashRow = $hashModel->find("first", array(
				"conditions" => array("hash" => $hash)
			)
		);
		// no hash found or no service_type defined or hash expired
		if ( empty($hashRow["HashJob"]) || empty($hashRow["HashJob"]["service_type"]) || $hashRow["HashJob"]["status"] == "expired") {
			$this->controller->Session->setFlash(__("Hash not valid or expired.",true), NULL, NULL, 'info');
			return false;
		}
	
		return $hashRow["HashJob"];	
	}
	
	/**
	 * subscribe to a newsletter
	 */
	public function newsletterSubscribe($data) {
		if(empty($data)) {
			throw new BeditaException(__("Error on subscribing: no data",true));
		}
		if(empty($data['newsletter_email'])) {
			throw new BeditaException(__("Error on subscribing: empty 'newsletter_email'",true));
		}
		if(empty($data['joinGroup'])) {
			throw new BeditaException(__("Error on subscribing: no mail_group indicated",true));
		}
		
		$newsletter_email = $data['newsletter_email'];
		$newsletters = $data['joinGroup'];
		$card = ClassRegistry::init('Card');
		$c = $card->find('first', array('conditions' => array("Card.newsletter_email" => $newsletter_email),'contain'=>array()));
		if(empty($c)) {
			$data['email'] = $data['newsletter_email'];
			$data['title'] = (!empty($data['name'])) ? $data['name'] : $data['newsletter_email'];
			$data['status'] = "on";
			unset($data['joinGroup']);
			$card->create();
			if(!($card->save($data))) {
				throw new BeditaException(__("Error saving card for " . $newsletter_email,true));
			}
			$data['joinGroup'] = $newsletters;
			$c = $card->find('first', array('conditions' => array("Card.newsletter_email" => $newsletter_email),'contain'=>array()));
		}
		$mail_group = ClassRegistry::init('MailGroup');
		$mail_group_card = ClassRegistry::init('MailGroupCard');
		$hash_job = ClassRegistry::init("HashJob");
		
		foreach($newsletters as $n) {
			$m = $mail_group_card->find(
				'first',
				array('conditions' => array(
					"MailGroupCard.mail_group_id"=>$n['mail_group_id'],
					"MailGroupCard.card_id" => $c['id']
					),
					'contain'=>array()
				)
			);
			if(!empty($m)) {
				$this->log("Email $newsletter_email already subscribed to " . $m['MailGroupCard']['mail_group_id']);
			} else {
				$mailGroup = $mail_group->find("first", array(
						"conditions" => array("id" => $n['mail_group_id']),
						"contain" => array()
					)
				);
				if (empty($mailGroup)) {
					throw new BeditaException(__("Error loading mail group detail: id=" . $n['mail_group_id'],true));
				}
				$mailGroup = $mailGroup["MailGroup"];
				
				$params = array(
					"user" => $newsletter_email,
					"title" => $mailGroup["group_name"]
				);
				
				if ($mailGroup["security"] == "all") {
					$data["HashJob"]["user_id"] = $c["id"];
					$data["HashJob"]["command"] = 'confirm';
					$data["HashJob"]['mail_group_id'] = $n['mail_group_id'];
					$data["HashJob"]["hash"] = $hash_job->generateHash();
					$data["HashJob"]["expired"] = date("Y-m-d H:i:s", time() + Configure::read("hashExpiredTime"));
					$hash_job->create();
					if (!$hash_job->save($data["HashJob"])) {
						throw new BeditaException(__("Error generating hash confirmation for " . $newsletter_email,true));
					}
					$joindata['status'] = 'pending';
					$body = $this->getNotifyText("newsletterConfirmSubscribe", "mail_body");
					$subject = $this->getNotifyText("newsletterConfirmSubscribe", "subject");
					$params["url"] = Router::url('/hashjob/exec/' . $data["HashJob"]["hash"] . "/confirm", true);
					$viewsMsg = $this->getNotifyText("newsletterConfirmSubscribe", "viewsMsg");
				} else {
					$joindata['status'] = 'confirmed';
					$body = (!empty($mailGroup["confirmation_in_message"]))? $mailGroup["confirmation_in_message"] : $this->getNotifyText("newsletterSubscribed", "mail_body");
					$subject = $this->getNotifyText("newsletterSubscribed", "subject");
					$viewsMsg = $this->getNotifyText("newsletterSubscribed", "viewsMsg");
				}
				
				$joindata['card_id'] = $c["id"];
				$joindata['mail_group_id'] = $n['mail_group_id'];
				$mail_group_card->create();
				if (!$mail_group_card->save($joindata)) {
					throw new BeditaException(__("Error subscribing " . $newsletter_email,true));
				}
				
				$this->sendNotificationMail(array(
						"body" => $body,
						"subject" => $subject,
						"viewsMsg" => $viewsMsg,
						"newsletter_email" => $newsletter_email,
						"params" => $params
					)
				);				
			}
		}
	}

	/**
	 * confirm subscription to a newsletter
	 */
	public function newsletterSubscribeConfirm($data) {
		if ($data["HashJob"]["status"] != "pending")
			throw new BeditaException(__("Hash isn't valid.",true));
		
		if (empty($data["HashJob"]["mail_group_id"]))
			throw new BeditaException(__("Missing mail group id",true));
			
		$mail_group_card = ClassRegistry::init('MailGroupCard');
		$m = $mail_group_card->find(
			'first',
			array('conditions' => array(
				"MailGroupCard.mail_group_id" => $data["HashJob"]['mail_group_id'],
				"MailGroupCard.card_id" => $data["HashJob"]['user_id'],
				"MailGroupCard.status='pending'"
				),
				'contain'=>array()
			)
		);
		
		if (empty($m))
			throw new BeditaException(__("User not associated to mailgroup or he isn't in pending status",true));
		
		
		$mail_group_card->id = $m['MailGroupCard']['id'];
		if(!$mail_group_card->saveField("status", "confirmed")) {
			throw new BeditaException(__("Error saving mail group card",true));
		}
		
		$hashModel = ClassRegistry::init("HashJob");
		$hashModel->id = $data["HashJob"]["id"];
		if(!$hashModel->saveField("status", "closed")) {
			throw new BeditaException(__("Error saving hash status",true));
		}
		
		$card = ClassRegistry::init('Card');
		
		$newsletter_email = $card->field('newsletter_email',array('id'=>$data["HashJob"]['user_id']));
		$mail_group = ClassRegistry::init('MailGroup');
		$mailGroup = $mail_group->find('first',array(
			"conditions" => array('id'=>$data["HashJob"]['mail_group_id']),
			"contain" => array()
			)
		);
		
		$params = array(
			"user" => $newsletter_email,
			"title" => $mailGroup["MailGroup"]["group_name"]
		);

		$this->sendNotificationMail(array(
				"body" => ( (!empty($mailGroup["confirmation_in_message"]))? $mailGroup["confirmation_in_message"] : $this->getNotifyText("newsletterSubscribed", "mail_body") ),
				"subject" => $this->getNotifyText("newsletterSubscribed", "subject"),
				"viewsMsg" => $this->getNotifyText("newsletterSubscribed", "viewsMsg"),
				"newsletter_email" => $newsletter_email,
				"params" => $params
			)
		);
		
	}

	/**
	 * unsubscribe from a newsletter
	 */
	public function newsletterUnsubscribe($data) {
		if (empty($data['mail_group_id']))
			throw new BeditaException(__("missing mail group id", true));
		if (empty($data['card_id']))
			throw new BeditaException(__("missing card id", true));
		
		// check if there are other unsubscribe hashes for mail_group_id
		$hashjobModel = ClassRegistry::init("HashJob");
		$hashjobs = $hashjobModel->find("all", array(
				"conditions" => array(
					"user_id" => $data["card_id"],
					"service_type" => "newsletter_unsubscribe"
				)
			)
		);
		
		if (!empty($hashjobs)) {
			foreach ($hashjobs as $hashRow) {
				if (!empty($hashRow["HashJob"]["mail_group_id"]) 
						&& $hashRow["HashJob"]["mail_group_id"] == $data['mail_group_id']
						&& $hashRow["HashJob"]['status'] == "pending") {
					throw new BeditaException(__("hashjob for unsubscribe already in use", true));	
				}
			}
		}
		
		$mail_group_card = ClassRegistry::init('MailGroupCard');
		$m = $mail_group_card->find(
			'first',
			array('conditions' => array(
				"MailGroupCard.mail_group_id"=>$data['mail_group_id'],
				"MailGroupCard.card_id" => $data['card_id'],
				"MailGroupCard.status='confirmed'"
				),
				'contain'=>array()
			)
		);
		if(empty($m)) {
			throw new BeditaException(__("User is not subscribed to mail group", true));
		}		
		
		$data["HashJob"]["user_id"] = $data["card_id"];
		$data["HashJob"]["command"] = 'confirm';
		$data["HashJob"]['mail_group_id'] = $data['mail_group_id'];
		$data["HashJob"]["hash"] = $hashjobModel->generateHash();
		$data["HashJob"]["expired"] = date("Y-m-d H:i:s", time() + Configure::read("hashExpiredTime"));
		if (!$hashjobModel->save($data["HashJob"])) {
			throw new BeditaException(__("Error generating hash confirmation for " . $newsletter_email,true));
		}

		$mail_group_name = ClassRegistry::init('MailGroup')->field("group_name", array("id" => $data["mail_group_id"]));
		$newsletter_email = ClassRegistry::init('Card')->field("newsletter_email", array("id" => $data["card_id"]));

		$params = array(
			"user" => $newsletter_email,
			"title" => $mail_group_name,
			"url" => Router::url('/hashjob/exec/' . $data["HashJob"]["hash"] . "/confirm", true)
		);
		
		$this->sendNotificationMail(array(
				"body" => $this->getNotifyText("newsletterConfirmUnsubscribe", "mail_body"),
				"subject" => $this->getNotifyText("newsletterConfirmUnsubscribe", "subject"),
				"viewsMsg" => $this->getNotifyText("newsletterConfirmUnsubscribe", "viewsMsg"),
				"newsletter_email" => $newsletter_email,
				"params" => $params
			)
		);
		
	}

	public function newsletterUnsubscribeConfirm($data) {
		if ($data["HashJob"]["status"] != "pending")
			throw new BeditaException(__("Hash isn't valid.",true));
		
		if (empty($data["HashJob"]["mail_group_id"]))
			throw new BeditaException(__("Missing mail group id",true));
			
		$mail_group_card = ClassRegistry::init('MailGroupCard');
		$m = $mail_group_card->find(
			'first',
			array('conditions' => array(
				"MailGroupCard.mail_group_id" => $data["HashJob"]['mail_group_id'],
				"MailGroupCard.card_id" => $data["HashJob"]['user_id'],
				"MailGroupCard.status='confirmed'"
				),
				'contain'=>array()
			)
		);
		
		if (empty($m))
			throw new BeditaException(__("User not associated to mailgroup or he is in pending status",true));
		
		if(!$mail_group_card->delete($m['MailGroupCard']['id'])) {
			throw new BeditaException(__("Error removing association mail group card",true));
		}
				
		$hashModel = ClassRegistry::init("HashJob");
		$hashModel->id = $data["HashJob"]["id"];
		if(!$hashModel->saveField("status", "closed")) {
			throw new BeditaException(__("Error saving hash status",true));
		}

		$mail_group_name = ClassRegistry::init('MailGroup')->field("group_name", array("id" => $data["HashJob"]['mail_group_id']));
		$newsletter_email = ClassRegistry::init('Card')->field("newsletter_email", array("id" => $data["HashJob"]['user_id']));

		$params = array(
			"user" => $newsletter_email,
			"title" => $mail_group_name
		);

		$this->sendNotificationMail(array(
				"body" => $this->getNotifyText("newsletterUnsubscribed", "mail_body"),
				"subject" => $this->getNotifyText("newsletterUnsubscribed", "subject"),
				"viewsMsg" => $this->getNotifyText("newsletterUnsubscribed", "viewsMsg"),
				"newsletter_email" => $newsletter_email,
				"params" => $params
			)
		);		
	}

	private function sendNotificationMail(array $mailParams) {
		$mailOptions = Configure::read("mailOptions");
		$mail_message_data['from'] = $mailOptions["sender"];
		$mail_message_data['reply_to'] = $mailOptions["reply_to"];
		$mail_message_data['to'] = $mailParams["newsletter_email"];
		$mail_message_data['subject'] = $this->replacePlaceHolder($mailParams["subject"], $mailParams["params"]);
		$mail_message_data['body'] = $this->replacePlaceHolder($mailParams["body"],$mailParams["params"]) . "\n\n\n" . $mailOptions["signature"];
		$this->BeMail->sendMail($mail_message_data);
		
		if (!empty($mailParams["viewsMsg"]))
			$this->controller->Session->setFlash($this->replacePlaceHolder($mailParams["viewsMsg"], $mailParams["params"]), NULL, NULL, 'info');
	}
	
	protected function loadMessages() {
		// load local messages if present
		$appPath = (defined("BEDITA_CORE_PATH"))? BEDITA_CORE_PATH . DS : APP;
		$localMsg = $appPath."config".DS."notify".DS."local.msg.php";
		$notify = array();
		if (file_exists ($localMsg) ) {
			require($localMsg);
		} else {
			require($appPath."config".DS."notify".DS."default.msg.php");
		}
		$this->notifyMsg = &$notify;
	}
	
	protected function getNotifyText($msgType, $field) {
		if(isset($this->notifyMsg[$msgType][$this->controller->viewVars["currLang"]][$field])) {
			$text = $this->notifyMsg[$msgType][$this->controller->viewVars["currLang"]][$field];
		} else {
			$text = $this->notifyMsg[$msgType]["eng"][$field]; // default fallback
		}
		return $text;
	}
	
	protected function replacePlaceHolder($text, array &$params) {
		if (isset($params["user"])) 
			$text = str_replace("[\$user]", $params["user"], $text);
		if (isset($params["title"]))
			$text = str_replace("[\$title]", $params["title"], $text);
		if (isset($params["url"]))
			$text = str_replace("[\$url]", $params["url"], $text);
		//$text = str_replace("[\$beditaUrl]", $params["beditaUrl"], $text);
		return $text;		
	}
	
}
?>