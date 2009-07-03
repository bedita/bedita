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
 * General BEdita newsletter component
 * 
 * @version			
 * @modifiedby 		
 * @lastmodified	
 * 
 * $Id: be_newsletter.php
 */
class BeNewsletterComponent extends Object {

	var $uses = array('BEObject','Card') ;
	var $components = array("BeMail");

	/**
	 * startup component
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
	}
	
	/**
	 * subscribe to a newsletter
	 */
	public function subscribe($data) {
		if(empty($data)) {
			throw new BeditaException(__("Error on subscribing: no data",true));
		}
		if(empty($data['newsletter_email'])) {
			throw new BeditaException(__("Error on subscribing: empty 'newsletter_email'",true));
		}
		if(empty($data['joinGroup'])) {
			throw new BeditaException(__("Error on subscribing: no mail_group indicated",true));
		}
		$mode= (!empty($data['mode'])) ? $data['mode'] : 0;
		$newsletter_email = $data['newsletter_email'];
		$newsletters = $data['joinGroup'];
		$card = ClassRegistry::init('Card');
		$c = $card->find('first', array('conditions' => array("Card.newsletter_email" => $newsletter_email),'contain'=>array()));
		if(empty($c)) {
			$data['email'] = $data['newsletter_email'];
			$data['title'] = (!empty($data['name'])) ? $data['name'] : $data['newsletter_email'];
			unset($data['joinGroup']);
			$card->create();
			if(!($card->save($data))) {
				echo "error saving card"; exit;
			}
			$data['joinGroup'] = $newsletters;
			$c = $card->find('first', array('conditions' => array("Card.newsletter_email" => $newsletter_email),'contain'=>array()));
		}
		$mail_group = ClassRegistry::init('MailGroup');
		$mail_group_card = ClassRegistry::init('MailGroupCard');
		$mail_group_card->create();
		foreach($newsletters as $n) {
			$m = $mail_group_card->find(
				'first',
				array('conditions' => array(
					"MailGroupCard.mail_group_id"=>$n['mail_group_id'],
					"MailGroupCard.card_id" => $c['id'],
					"MailGroupCard.service_type='newsletter'"
					),
					'contain'=>array()
				)
			);
			if(!empty($m)) {
				$this->log("Email $newsletter_email already subscribed to " . $m['MailGroupCard']['mail_group_id']);
			} else {
				$mail_group_name = $mail_group->field('group_name',array('id'=>$n['mail_group_id']));
				$mail_group_card_data = $this->subscribe_to_single_newsletter($c['id'],$n['mail_group_id'],$mail_group_name,$mode);
				$mail_message_data = Configure::read("newsletterRegistrationMessage");
				$mail_message_data['from'] = Configure::read("defaultMailSender");
				$mail_message_data['to'] = $newsletter_email;
				$mail_message_data['hash'] = $mail_group_card_data['hash'];
				$mail_message_data['subject'] .= " '" . $mail_group_name . "'";
				$mail_message_data['header'] .= " '" . $mail_group_name . "'";
				if($mode==0) {
					$unsub_link = Router::url('unsubscribeNewsletter/card_id:' . $c['id'] . '/mail_group_id:' . $n['mail_group_id'] . '/hash:' . $mail_group_card_data['hash']);
					$mail_message_data['footer'] .= ' <a href="' . $unsub_link . '">apri questa pagina</a> / <a href="' . $unsub_link . '">open this page</a>';
				} elseif($mode==1) {
					$confirm_link = Router::url('confirmSubscribeNewsletter/card_id:' . $c['id'] . '/mail_group_id:' . $n['mail_group_id'] . '/hash:' . $mail_group_card_data['hash']);
					$mail_message_data['footer'] = 'Per confermare la registrazione/To confirm subscription: <a href="' . $confirm_link . '">apri questa pagina</a> / <a href="' . $confirm_link . '">open this page</a>';
					$mail_message_data['body'] = $mail_group->field('confirmation_in_message',array('id'=>$n['mail_group_id']));
				}
				$mail_message_data['body'] = $mail_message_data['header'] . "\n" . $mail_message_data['body'] . "\n" . $mail_message_data['footer'] . "\n" . $mail_message_data['signature'];
				$this->BeMail->sendMail($mail_message_data);
			}
		}
	}

	/**
	 * confirm subscription to a newsletter
	 */
	public function confirmSubscribe($params) {
		$mail_group_card = ClassRegistry::init('MailGroupCard');
		$m = $mail_group_card->find(
			'first',
			array('conditions' => array(
				"MailGroupCard.mail_group_id"=>$params['mail_group_id'],
				"MailGroupCard.card_id" => $params['card_id'],
				"MailGroupCard.hash" => $params['hash'],
				"MailGroupCard.service_type='newsletter'",
				"MailGroupCard.status='pending'"
				),
				'contain'=>array()
			)
		);
		if(!empty($m)) {
			$mail_group_card->id = $m['MailGroupCard']['id'];
			if(!$mail_group_card->saveField("status", "confirmed")) {
				throw new BeditaException(__("Error saving mail group card",true));
			}
			$card = ClassRegistry::init('Card');
			$card->create();
			$newsletter_email = $card->field('newsletter_email',array('id'=>$params['card_id']));
			$mail_group = ClassRegistry::init('MailGroup');
			$mail_group->create();
			$mail_group_name = $mail_group->field('group_name',array('id'=>$params['mail_group_id']));
			$mail_message_data = Configure::read("newsletterRegistrationMessage");
			$mail_message_data['from'] = Configure::read("defaultMailSender");
			$mail_message_data['to'] = $newsletter_email;
			$mail_message_data['hash'] = $params['hash'];
			$mail_message_data['subject'] .= " '" . $mail_group_name . "'";
			$mail_message_data['header'] .= " '" . $mail_group_name . "'";
			$unsub_link = Router::url('/pages/unsubscribeNewsletter/card_id:' . $params['card_id']. '/mail_group_id:' . $params['mail_group_id'] . '/hash:' . $params['hash']);
			$mail_message_data['footer'] .= ' <a href="' . $unsub_link . '">apri questa pagina</a> / <a href="' . $unsub_link . '">open this page</a>';
			$mail_message_data['body'] = $mail_message_data['header'] . "\n" . $mail_message_data['body'] . "\n" . $mail_message_data['footer'] . "\n" . $mail_message_data['signature'];
			$this->BeMail->sendMail($mail_message_data);
		}
	}

	/**
	 * unsubscribe from a newsletter
	 */
	public function unsubscribe($params) {
		$mail_group_card = ClassRegistry::init('MailGroupCard');
		$mail_group_card->create();
		$m = $mail_group_card->find(
			'first',
			array('conditions' => array(
				"MailGroupCard.mail_group_id"=>$params['mail_group_id'],
				"MailGroupCard.card_id" => $params['card_id'],
				"MailGroupCard.hash" => $params['hash'],
				"MailGroupCard.service_type='newsletter'",
				"MailGroupCard.status='confirmed'"
				),
				'contain'=>array()
			)
		);
		if(!empty($m)) {
			if(!$mail_group_card->delete($m['MailGroupCard']['id'])) {
				throw new BeditaException(__("Error removing mail group card",true));
			}
		}
	}

	private function subscribe_to_single_newsletter($card_id,$mail_group_id,$mail_group_name,$mode) {
		$mail_group_card = ClassRegistry::init('MailGroupCard');
		$mail_group_card->create();
		$data = array();
		$data['card_id'] = $card_id;
		$data['mail_group_id'] = $mail_group_id;
		$data['status'] = ($mode==0) ? 'confirmed' : 'pending';
		$data['command'] = 'confirm';
		$data['service_type'] = 'newsletter';
		$data["hash"] = md5($card_id . microtime() . $mail_group_name);
		$mail_group_card->save($data);
		return $data;
	}
}
?>