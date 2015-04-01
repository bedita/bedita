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
 * General BEdita hash component
 * 
 * @version			
 * @modifiedby 		
 * @lastmodified	
 * 
 * $Id: be_newsletter.php
 */
class BeHashComponent extends Object {

	/**
	 *
	 * @var controller instance
	 */
	protected $controller;

	/**
	 * store notification messages
	 *
	 * @var array
	 */
	protected $notifyMsg = null;

	/**
	 * other components
	 *
	 * @var array
	 */
	public $components = array("BeMail");

	/**
	 * if an hash job has to be closed after at the end of handleHash method
	 *
	 * @var boolean
	 */
	public $closeHashJob = true;
	
	/**
	 * startup component
	 * @param Controller $controller
	 */
	function startup(&$controller=null) {
		$this->controller = &$controller;
		$this->BeMail->startup($this->controller);
		$this->loadMessages();
	}

	/**
	 * handle hash request like newsletter/frontend subscribe/unsubscribe
	 *
	 * @param string $service_type
	 * @param string $hash
	 * @return boolean
	 * @throws BeditaException
	 * @throws BeditaHashException
	 */
	public function handleHash($service_type, $hash=null) {
		if (empty($service_type)) {
			return false;
		}
		$this->closeHashJob = true;
		
		if (!empty($hash)) {

			if (!$hashRow = $this->getHashRow($hash)) {
				return false;
			}
			$service_type = $hashRow["service_type"];
			if (!empty($hashRow["command"])) {
				if (empty($this->controller->params["named"]["command"]) || $hashRow["command"] != $this->controller->params["named"]["command"]) {
					throw new BeditaException(__("Incorrect hash command", true));
				}
				$method = $hashRow["service_type"] . "_" . $hashRow["command"];
			} else {
				$method = $hashRow["service_type"];
			}
			$method = Inflector::camelize($method);
			$method{0} = strtolower($method{0});
			$this->controller->data["HashJob"] = $hashRow;

		// first hash operation
		} else {
			if (empty($service_type)) {
				throw new BeditaException(__("missing service type", true));
			}
			$method = Inflector::camelize($service_type);
			$method{0} = strtolower($method{0});
			$this->controller->data["HashJob"]["service_type"] = $service_type;
			$this->controller->data = array_merge($this->controller->data, $this->controller->params["named"]);
			$this->closeHashJob = false;
		}

		$mailParams = array();
		if (method_exists($this->controller, $method)) {
		    $mailParams = $this->controller->{$method}();
		} elseif (method_exists($this, $method)) {
		    $mailParams = $this->{$method}($this->controller->data);
		} else {
		    throw new BeditaException(__("missing method to manage hash case", true));
		}

		if ($this->closeHashJob && !empty($hashRow["status"]) && $hashRow["status"] == "pending") {
			$hashModel = ClassRegistry::init("HashJob");
			$hashModel->id = $hashRow["id"];
			if(!$hashModel->saveField("status", "closed")) {
				throw new BeditaException(__("Error saving hash status",true));
			}
		}
		if (!empty($mailParams)) {
			try {
				$this->sendNotificationMail($mailParams);
			} catch (BeditaException $e) {
				$this->log($e->errorTrace());
				throw new BeditaHashException(__("Error sending notification mail",true));
			}
		}
		return true;

	}

	/**
	 * get hash_jobs row
	 *
	 * @param string $hash
	 * @return array HashJob row
	 */
	public function getHashRow($hash) {
		$hashModel = ClassRegistry::init("HashJob");
		$hashRow = $hashModel->find("first", array(
				"conditions" => array(
					"hash" => $hash,
					"status" => "pending"
				)
			)
		);
		// no hash found or no service_type defined or hash expired
		if ( empty($hashRow["HashJob"]) || empty($hashRow["HashJob"]["service_type"]) || $hashRow["HashJob"]["status"] == "expired") {
			$this->controller->Session->setFlash(__("Hash not valid or expired.",true), "message", array("class" => "info"), 'info');
			return false;
		}
	
		return $hashRow["HashJob"];	
	}

	/**
	 * user sign up registration
	 *
	 * @param array $data
	 * @return array
	 * @throws BeditaHashException
	 * @throws BeditaException
	 */
	protected function userSignUp($data) {
		if (empty($data['User'])) {
			throw new BeditaHashException(__("Error on sign up: no data",true));
		}

		if (empty($data['User']['email'])) {
			throw new BeditaHashException(__("Error on sign up: an email is required",true));
		}

		if (!$this->controller->BeAuth->checkConfirmPassword($this->controller->params['form']['pwd'], $data['User']['passwd'])) {
			throw new BeditaHashException(__("Passwords mismatch",true));
		}

		if (!empty($data["groups"])) {
			// exclude backend groups for avoid malicious data
			$groups = ClassRegistry::init("Group")->getList(array("backend_auth" => 0, "name" => $data["groups"]));
			unset($data["groups"]);
		} else {
			$groups = Configure::read('authorizedGroups');
			if (empty($groups)) {
				$groups = ClassRegistry::init("Group")->getList(array("backend_auth" => 0));
			}
		}

		$data["User"]["valid"] = 0;

		if (empty($data['User']['realname'])) {
			$data['User']['realname'] = $data['User']['userid'];
		}

		try {
			$user_id = $this->controller->BeAuth->createUser($data, $groups, false);
		} catch (BeditaException $ex) {
			throw new BeditaHashException($ex->getMessage(), $ex->getDetails());
		}

		if (!empty($data["Card"])) {
			$data["Card"]["ObjectUser"]["card"][0]["user_id"] = $user_id;
			$data["Card"]["ObjectUser"]["card"][0]["switch"] = "card";
			if (empty($data["Card"]["email"])) {
				$data["Card"]["email"] = $data["User"]["email"];
			}
			if (empty($data["Card"]["title"])) {
				$data["Card"]["title"] = $data["User"]["realname"];
				if (!empty($data["Card"]["name"])) {
					$data["Card"]["title"] = $data["Card"]["name"] . " ";
					if (!empty($data["Card"]["surname"])) {
						$data["Card"]["title"] .= $data["Card"]["surname"];
					}
				} elseif (!empty($data["Card"]["surname"])) {
					$data["Card"]["title"] = $data["Card"]["surname"];
				}
			}
			if (!ClassRegistry::init("Card")->save($data["Card"])) {
				throw new BeditaHashException(__("Error saving data", true));
			}
		}


		// if user subscription is moderated
		$userModerateSignup = Configure::read("userModerateSignup");
		if ($userModerateSignup === true) { // true but not email, the user is created not valid and a mail is sent to the user

			$mailParams = array(
				"body" => $this->getNotifyText("userSignUpModerated", "mail_body"),
				"subject" => $this->getNotifyText("userSignUpModerated", "subject"),
				"viewsMsg" => $this->getNotifyText("userSignUpModerated", "viewsMsg"),
				"email" => $data["User"]["email"],
				"params" => array(
					"title" => $this->controller->viewVars["publication"]["public_name"],
					"user" => $data["User"]["userid"],
				)
			);

		} else {

			$hash_job = ClassRegistry::init("HashJob");
			$data["HashJob"]["user_id"] = $user_id;
			$data["HashJob"]["command"] = 'activation';
			$data["HashJob"]["hash"] = $hash_job->generateHash();
			$data["HashJob"]["expired"] = $hash_job->getExpirationDate();
			if (!$hash_job->save($data["HashJob"])) {
				throw new BeditaException(__("Error generating hash confirmation for " . $user["User"]["userid"],true));
			}
	

			// if is email, activation by email will be sent to this email (administators'))
			if (filter_var($userModerateSignup, FILTER_VALIDATE_EMAIL)) {
				$mailParams = array(
					"body" => $this->getNotifyText("userSignUpModeratedToAdmin", "mail_body"),
					"subject" => $this->getNotifyText("userSignUpModeratedToAdmin", "subject"),
					"viewsMsg" => $this->getNotifyText("userSignUpModeratedToAdmin", "viewsMsg"),
					"email" => $userModerateSignup,
					"params" => array(
						"title" => $this->controller->viewVars["publication"]["public_name"],
						"user" => $data["User"]["userid"],
						"url" => Router::url("/hashjob/exec/" . $data["HashJob"]["hash"] . "/command:activation", true)
					)
				);
			} else { // if user is not moderated the auto-activation email will be sent to the user
				$mailParams = array(
					"body" => $this->getNotifyText("userSignUp", "mail_body"),
					"subject" => $this->getNotifyText("userSignUp", "subject"),
					"viewsMsg" => $this->getNotifyText("userSignUp", "viewsMsg"),
					"email" => $data["User"]["email"],
					"params" => array(
						"title" => $this->controller->viewVars["publication"]["public_name"],
						"user" => $data["User"]["userid"],
						"url" => Router::url("/hashjob/exec/" . $data["HashJob"]["hash"] . "/command:activation", true)
					)
				);
			}

		}

		return $mailParams;
	}

	/**
	 * Signup activation for user
	 * 
	 * @param array $data
	 * @return array
	 * @throws BeditaHashException
	 * @throws BeditaException
	 */
	protected function userSignUpActivation($data) {
		$userModel = ClassRegistry::init("User");
		$user = $userModel->find("first", array(
			"conditions" => array("id" => $data["HashJob"]["user_id"]),
			"contain" => array("ObjectUser.switch='card'")
		));
		if (empty($user)) {
			throw new BeditaHashException(__("Hash isn't valid or user doesn't exist.",true));
		}

		$user["User"]["valid"] = 1;

		if (!$userModel->save($user)) {
			throw new BeditaException(__("Error saving user.",true));
		}

		if (!empty($user["ObjectUser"])) {
			
			$BEObjectModel = ClassRegistry::init("BEObject");
			$BEObjectModel->id = $user["ObjectUser"][0]["object_id"];
			if (!$BEObjectModel->saveField("status", "on")) {
				throw new BeditaHashException(__("Error saving data", true));
			}
		}


		$mailParams = array(
			"body" => $this->getNotifyText("userSignUpActivation", "mail_body"),
			"subject" => $this->getNotifyText("userSignUpActivation", "subject"),
			"viewsMsg" => $this->getNotifyText("userSignUpActivation", "viewsMsg"),
			"email" => $user["User"]["email"],
			"params" => array(
				"user" => $user["User"]["realname"],
				"title" => $this->controller->viewVars["publication"]["public_name"],
				"url" => Router::url("/login", true)
			)
		);

		return $mailParams;
	}

	/**
	 * subscribe to a newsletter
	 * 
	 * @param array $data
	 * @return array
	 * @throws BeditaHashException
	 * @throws BeditaException
	 */
	protected function newsletterSubscribe($data) {
		if(empty($data)) {
			throw new BeditaHashException(__("Error on subscribing: no data",true));
		}
		if(empty($data['newsletter_email'])) {
			throw new BeditaHashException(__("Error on subscribing: empty 'newsletter_email'",true));
		}
		if(empty($data['joinGroup'])) {
			throw new BeditaHashException(__("Error on subscribing: no mail_group indicated",true));
		}
		
		$newsletter_email = $data['newsletter_email'];
		$newsletters = $data['joinGroup'];
		$card = ClassRegistry::init('Card');
		$c = $card->find('first', array('conditions' => array("Card.newsletter_email" => $newsletter_email),'contain'=>array()));
		if(empty($c)) {
			$data['email'] = $data['newsletter_email'];
			
			if (empty($data['title'])) {
				if (!empty($data['name'])) {
					$data['title'] = $data['name'];
				}
				if (!empty($data['surname'])) {
					$data['title'] .= " " . $data['surname'];
				}
				
				if (empty($data['title'])) {
					$data['title'] = $data['newsletter_email'];
				}
				
				$data['title'] = trim($data['title']);
			}
			
			$data['status'] = "draft";
			$data['note'] = "public newsletter subscribe";
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
					$data["HashJob"]["expired"] = $hash_job->getExpirationDate();
					$hash_job->create();
					if (!$hash_job->save($data["HashJob"])) {
						throw new BeditaException(__("Error generating hash confirmation for " . $newsletter_email,true));
					}
					$joindata['status'] = 'pending';
					$body = $this->getNotifyText("newsletterConfirmSubscribe", "mail_body");
					$subject = $this->getNotifyText("newsletterConfirmSubscribe", "subject");
					$params["url"] = Router::url('/hashjob/exec/' . $data["HashJob"]["hash"] . "/command:confirm", true);
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
				
				$mailParams = array(
					"body" => $body,
					"subject" => $subject,
					"viewsMsg" => $viewsMsg,
					"email" => $newsletter_email,
					"params" => $params
				);

				return $mailParams;
			}
		}
	}

	/**
	 * confirm subscription to a newsletter
	 * 
	 * @param array $data
	 * @return array
	 * @throws BeditaHashException
	 * @throws BeditaException
	 */
	protected function newsletterSubscribeConfirm($data) {
		if ($data["HashJob"]["status"] != "pending")
			throw new BeditaHashException(__("Hash isn't valid.",true));
		
		if (empty($data["HashJob"]["mail_group_id"]))
			throw new BeditaHashException(__("Missing mail group id",true));
			
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
		
		if (empty($m)) {
			throw new BeditaHashException(__("User not associated to mailgroup or he isn't in pending status",true));
		}
		
		$mail_group_card->id = $m['MailGroupCard']['id'];
		if(!$mail_group_card->saveField("status", "confirmed")) {
			throw new BeditaException(__("Error saving mail group card",true));
		}
		
		$beObject = ClassRegistry::init("BEObject");
		$beObject->id = $data["HashJob"]['user_id'];
		if (!$beObject->saveField("status", "on")) {
			throw new BeditaException(__("Error setting on Card status", true));
		}

		$card = ClassRegistry::init("Card");
		$newsletter_email = $card->field('newsletter_email',array('id'=>$data["HashJob"]['user_id']));
		$mail_group = ClassRegistry::init('MailGroup');
		$mailGroup = $mail_group->find('first',array(
			"conditions" => array('id'=>$data["HashJob"]['mail_group_id']),
			"contain" => array()
			)
		);

		$mailParams = array(
				"body" => ( (!empty($mailGroup["MailGroup"]["confirmation_in_message"]))? $mailGroup["MailGroup"]["confirmation_in_message"] : $this->getNotifyText("newsletterSubscribed", "mail_body") ),
				"subject" => $this->getNotifyText("newsletterSubscribed", "subject"),
				"viewsMsg" => $this->getNotifyText("newsletterSubscribed", "viewsMsg"),
				"email" => $newsletter_email,
				"params" => array(
					"user" => $newsletter_email,
					"title" => $mailGroup["MailGroup"]["group_name"]
				)
		);

		return $mailParams;
		
	}

	/**
	 * unsubscribe from a newsletter
	 * @param array $data
	 * @return array
	 * @throws BeditaHashException
	 * @throws BeditaException
	 */
	protected function newsletterUnsubscribe($data) {
		if (empty($data['mail_group_id']))
			throw new BeditaHashException(__("missing mail group id", true));
		if (empty($data['card_id']))
			throw new BeditaHashException(__("missing card id", true));
		
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
					throw new BeditaHashException(__("hashjob for unsubscribe already in use", true));
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
			throw new BeditaHashException(__("User is not subscribed to mail group", true));
		}		
		
		$data["HashJob"]["user_id"] = $data["card_id"];
		$data["HashJob"]["command"] = 'confirm';
		$data["HashJob"]['mail_group_id'] = $data['mail_group_id'];
		$data["HashJob"]["hash"] = $hashjobModel->generateHash();
		$data["HashJob"]["expired"] = $hashjobModel->getExpirationDate();
		if (!$hashjobModel->save($data["HashJob"])) {
			throw new BeditaException(__("Error generating hash confirmation for " . $newsletter_email,true));
		}

		$mail_group_name = ClassRegistry::init('MailGroup')->field("group_name", array("id" => $data["mail_group_id"]));
		$newsletter_email = ClassRegistry::init('Card')->field("newsletter_email", array("id" => $data["card_id"]));

		$mailParams = array(
			"body" => $this->getNotifyText("newsletterConfirmUnsubscribe", "mail_body"),
			"subject" => $this->getNotifyText("newsletterConfirmUnsubscribe", "subject"),
			"viewsMsg" => $this->getNotifyText("newsletterConfirmUnsubscribe", "viewsMsg"),
			"email" => $newsletter_email,
			"params" => array(
				"user" => $newsletter_email,
				"title" => $mail_group_name,
				"url" => Router::url('/hashjob/exec/' . $data["HashJob"]["hash"] . "/command:confirm", true)
			)
		);

		return $mailParams;
	}

	/**
	 * Confirm newsletter unsubscribe
	 * @param array $data
	 * @return array
	 * @throws BeditaHashException
	 * @throws BeditaException
	 */
	protected function newsletterUnsubscribeConfirm($data) {
		if ($data["HashJob"]["status"] != "pending") {
			throw new BeditaHashException(__("Hash isn't valid.",true));
		}

		if (empty($data["HashJob"]["mail_group_id"])) {
			throw new BeditaHashException(__("Missing mail group id",true));
		}

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
		
		if (empty($m)) {
			throw new BeditaHashException(__("User not associated to mailgroup or he is in pending status",true));
		}

		if(!$mail_group_card->delete($m['MailGroupCard']['id'])) {
			throw new BeditaException(__("Error removing association mail group card",true));
		}
		
		$mail_group_name = ClassRegistry::init('MailGroup')->field("group_name", array("id" => $data["HashJob"]['mail_group_id']));
		$newsletter_email = ClassRegistry::init('Card')->field("newsletter_email", array("id" => $data["HashJob"]['user_id']));

		$mailParams = array(
			"body" => $this->getNotifyText("newsletterUnsubscribed", "mail_body"),
			"subject" => $this->getNotifyText("newsletterUnsubscribed", "subject"),
			"viewsMsg" => $this->getNotifyText("newsletterUnsubscribed", "viewsMsg"),
			"email" => $newsletter_email,
			"params" => array(
				"user" => $newsletter_email,
				"title" => $mail_group_name
			)
		);

		return $mailParams;
	}

	/**
	 * recover password. Send an email with hash to reset password
	 *
	 * @param array $data
	 * @return array mail params
	 * @throws BeditaHashException
	 * @throws BeditaException
	 */
	protected function recoverPassword($data) {
		$this->controller->Session->delete("userToChangePwd");
		if (empty($data["email"])) {
			throw new BeditaHashException(__("Missing email to send recover instructions", true));
		}
		$groupModel = ClassRegistry::init("Group");

		if (BACKEND_APP) {
			$groups = $groupModel->getList(array("backend_auth" => 1));
			$urlBase = "/authentications/recoverUserPassword/exec/";
		} else {
			$groups = Configure::read("authorizedGroups");
			if (empty($groups)) {
				$groups = $groupModel->getList(array("backend_auth" => 0));
			}
			$urlBase = "/hashjob/exec/";
		}

		$userModel = ClassRegistry::init("User");
		$user = $userModel->find("first", array(
				"conditions" => array("email" => trim($data["email"])),
				"contain" => array(
					"Group" => array(
						"conditions" => array("name" => $groups)
					)
				)
			)
		);
		
		if (empty($user["Group"]) || (!empty($user['User']['auth_type']) && $user['User']['auth_type'] != 'bedita')) {
			throw new BeditaHashException(__("No user with access privileges found or user uses an external authentication", true));
		}

		$hash_job = ClassRegistry::init("HashJob");
		$data["HashJob"]["user_id"] = $user["User"]["id"];
		$data["HashJob"]["command"] = 'change';
		$data["HashJob"]["hash"] = $hash_job->generateHash();
		$data["HashJob"]["expired"] = $hash_job->getExpirationDate();
		if (!$hash_job->save($data["HashJob"])) {
			throw new BeditaException(__("Error generating hash confirmation for " . $user["User"]["userid"],true));
		}
		
		$mailParams = array(
			"body" => $this->getNotifyText("recoverPassword", "mail_body"),
			"subject" => $this->getNotifyText("recoverPassword", "subject"),
			"viewsMsg" => $this->getNotifyText("recoverPassword", "viewsMsg"),
			"email" => $user["User"]["email"],
			"params" => array(
				"title" => $user["User"]["realname"],
				"user" => $user["User"]["userid"],
				"url" => Router::url($urlBase . $data["HashJob"]["hash"] . "/command:change", true)
			)
		);

		return $mailParams;
		
	}

	/**
	 * Recover password
	 * 
	 * @param array $data
	 * @return array
	 * @throws BeditaHashException
	 */
	protected function recoverPasswordChange($data) {
		$userToChangePwd = $this->controller->Session->read("userToChangePwd");
		if (empty($userToChangePwd) || $userToChangePwd["User"]["id"] != $data["HashJob"]["user_id"]) {
			$user = ClassRegistry::init("User")->find("first", array(
				"conditions" => array("id" => $data["HashJob"]["user_id"])
			));
			if (empty($user)) {
				throw new BeditaHashException(__("User not found", true));
			}
			$this->controller->Session->write("userToChangePwd", $user);
			$this->redirectPath = false;
			$this->closeHashJob = false;
			return;
		} else {
			if (empty($data["User"]["passwd"])) {
				throw new BeditaHashException(__("Missing data", true));
			}
			$pwd = trim($data["User"]["passwd"]);
			$confirmPwd = trim($this->controller->params['form']['pwd']);
			if (!$this->controller->BeAuth->checkConfirmPassword($pwd, $confirmPwd)) {
				throw new BeditaHashException(__("Passwords mismatch", true));
			}

			if (!$this->controller->BeAuth->changePassword($userToChangePwd["User"]["userid"], $pwd)) {
				throw new BeditaHashException(__("Error updating password", true));
			}

			$url = (BACKEND_APP)? "/" : "/login";

			$mailParams = array(
				"body" => $this->getNotifyText("recoverPasswordChange", "mail_body"),
				"subject" => $this->getNotifyText("recoverPasswordChange", "subject"),
				"viewsMsg" => $this->getNotifyText("recoverPasswordChange", "viewsMsg"),
				"email" => $userToChangePwd["User"]["email"],
				"params" => array(
					"user" => $userToChangePwd["User"]["realname"],
					"url" => Router::url($url, true)
				)
			);

            $this->controller->Session->delete('userToChangePwd');
            $this->controller->Session->write('userPwdChanged', true);
            return $mailParams;
		}
	}

	/**
	 * Send notification email
	 *
	 * @param array $mailParams
	 */
	protected function sendNotificationMail(array $mailParams) {
		$mailOptions = Configure::read("mailOptions");
		$mail_message_data['from'] = $mailOptions["sender"];
		$mail_message_data['reply_to'] = $mailOptions["reply_to"];
		$mail_message_data['to'] = $mailParams["email"];
		$mail_message_data['subject'] = $this->replacePlaceHolder($mailParams["subject"], $mailParams["params"]);
		$mail_message_data['body'] = $this->replacePlaceHolder($mailParams["body"],$mailParams["params"]) . "\n\n--\n" . $mailOptions["signature"];
		if (strstr($mail_message_data['body'], "[[[--BOUNDARY--]]]")) {
			$mail_message_data['mailType'] = "both";
		}
		$this->BeMail->sendMail($mail_message_data);
		
		if (!empty($mailParams["viewsMsg"])) {
			$msg = $this->replacePlaceHolder($mailParams["viewsMsg"], $mailParams["params"]);
			$this->controller->Session->setFlash($msg, "message", array("class" => "info"), 'info');
			$this->controller->set("viewMsg", $msg);
		}
	}

    /**
     * Load messages:
     *  1. default messages
     *  2. local unversioned instance messages
     *  3. frontend messages (if frontend app)
     */
    protected function loadMessages() {
        // 1. load default messages
        include BEDITA_CORE_PATH.DS . 'config' . DS. 'notify' . DS. 'default.msg.php';

        // 2. load local unversioned messages, if present - may override default
        $localMsgPath = BEDITA_CORE_PATH.DS . 'config' . DS. 'notify' . DS. 'local.msg.php';
        if (file_exists ($localMsgPath) ) {
            include $localMsgPath;
        }

        // 3. load frontend messages - if present and if frontend app
        if (!BACKEND_APP) {
            $frontendMsgPath = APP .DS . 'config' . DS. 'notify' . DS. 'frontend.msg.php';
            if (file_exists ($frontendMsgPath) ) {
                include $frontendMsgPath;
            }
        }

        $this->notifyMsg = $notify;
    }

	/**
	 * get message (in current lang, if present translation)
	 * 
	 * @param string $msgType
	 * @param string $field
	 * @return string
	 */
	protected function getNotifyText($msgType, $field) {
		if(isset($this->notifyMsg[$msgType][$this->controller->viewVars["currLang"]][$field])) {
			$text = $this->notifyMsg[$msgType][$this->controller->viewVars["currLang"]][$field];
		} else {
			$text = $this->notifyMsg[$msgType]["eng"][$field]; // default fallback
		}
		return $text;
	}

	/**
	 * replace place holder
	 * 
	 * @param unknown_type $text
	 * @param array $params
	 * @return string
	 */
	protected function replacePlaceHolder($text, array &$params) {
		if (isset($params["user"])) {
			$text = str_replace("[\$user]", $params["user"], $text);
		}
		if (isset($params["title"])) {
			$text = str_replace("[\$title]", $params["title"], $text);
		}
		if (isset($params["url"])) {
			$text = str_replace("[\$url]", $params["url"], $text);
		}
		//$text = str_replace("[\$beditaUrl]", $params["beditaUrl"], $text);
		return $text;		
	}
}
