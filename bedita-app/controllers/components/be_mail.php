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

	private $layoutNewsletter = "newsletter";

	private $templateNewsletter = "newsletter";

	private $boundaryPlaceholder = "[[[--BOUNDARY--]]]";

	/**
	 * startup component
	 * set smtp options if it's in configuration (bedita.cfg.php)
	 * @param unknown_type $controller
	 */
	function startup(&$controller=null) {
		$this->Controller = &$controller;
		foreach ($this->components as $comp) {
			if (!isset($this->$comp)) {
				App::import('Component', $comp);
				$componentName = $comp . "Component";
				$this->{$comp} = new $componentName($this->Controller) ;
				$this->{$comp}->initialize($this->Controller);
			}
		}
	}

	/**
	 * send single mail from mail_messages table
	 *
	 * @param int $msg_id
	 * @param string $to, recipient email
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
		$data["from"] = (!empty($res["sender_name"]))? $mailMsgModel->getCompleteSender(null, $res["sender"], $res["sender_name"]) : $res["sender"];
		$data["subject"] = $res["subject"];
		$data["replyTo"] = $res["reply_to"];
		$data["mailType"] = ($html)? "both" : "txt";

		$data["body"] = $this->prepareMailBody($res, $html);

		$this->sendMail($data);
	}

	/**
	 * Prepare mail body using template
	 *
	 * @param array $message, mail_message array from a find on MailMessage
	 * @param bool $html, mail type
	 * @param int $mail_group_id used to get publication public url and build unsubscribe link
	 * @param $card_id used to built unsubscribe link
	 * @return string body (html or txt) of the message
	 */
	private function prepareMailBody($message, $html=true, $mail_group_id=null, $card_id=null) {
		$unsubscribeurl = "";
		$subscriberName = "";
		if (!empty($mail_group_id)) {
			$publicationUrl = ClassRegistry::init("MailGroup")->getPublicationUrlByGroup($mail_group_id);
			if (!empty($card_id)) {
				$unsubscribeurl = $publicationUrl . "/hashjob/newsletter_unsubscribe/mail_group_id:".$mail_group_id."/card_id:".$card_id;
			}
		}

		if (!empty($message["RelatedObject"]) && $message["RelatedObject"][0]["switch"] == "template") {

			$mailTemplate = ClassRegistry::init("MailTemplate");
			$template = $mailTemplate->find("first", array(
					"conditions" => array("MailTemplate.id" => $message["RelatedObject"][0]["object_id"]),
					"contain" => array("Content")
				)
			);

			$treeModel = ClassRegistry::init("Tree");
			$pub_id = $treeModel->getParent($template["id"]);
			$areaModel = ClassRegistry::init("Area");
			$templatePublicationUrl = $areaModel->field("public_url", array("id" => $pub_id));

			$txtBody = str_replace("[\$newsletterTitle]",  strip_tags($message["title"]), $template["abstract"]);
			if (strstr($txtBody,"[\$subscriber]") && !empty($card_id)) {
				$card = ClassRegistry::init("Card")->find("first", array(
						"conditions" => array("id" => $card_id),
						"contain" => array()
					)
				);
				if (!empty($card["company"]) && $card["company"] == 1) {
					$subscriberName = $card["company_name"];
				} else {
					if (!empty($card["name"])) {
						$subscriberName = $card["name"];
					}
					if (!empty($card["surname"])) {
						$subscriberName .= " " . $card["surname"];
					}
				}
			}
			$txtBody = str_replace("[\$subscriber]",  $subscriberName, $txtBody);
			$txtBody = preg_replace("/<!--bedita content block-->[\s\S]*<!--bedita content block-->/", $message["abstract"], $txtBody);
			$txtBody = str_replace("[\$signature]", $message["signature"], $txtBody);
			$txtBody = str_replace("[\$signoutlink]", $unsubscribeurl, $txtBody);
			$body = str_replace("[\$privacydisclaimer]", $message["privacy_disclaimer"], $txtBody);

			if ($html) {
				// get css
				$css = (!empty($templatePublicationUrl))? $templatePublicationUrl . "/css/" . Configure::read("newsletterCss") : "";
				$htmlMsg = "<html><head>%s</head><body>%s</body></html>";
				$style = "";
				if (!empty($css)) {
					$style = '<link rel="stylesheet" type="text/css" href="'.$css.'" />';
				}
				$htmlBody = str_replace("[\$newsletterTitle]", $message["title"], $template["body"]);
				$htmlBody = str_replace("[\$subscriber]",  $subscriberName, $htmlBody);
				$htmlBody = preg_replace("/<!--bedita content block-->[\s\S]*<!--bedita content block-->/", $message["body"], $htmlBody);
				$htmlBody = str_replace("[\$signature]", $message["signature"], $htmlBody);
				$htmlBody = str_replace("[\$signoutlink]", $unsubscribeurl, $htmlBody);
				$htmlBody = str_replace("[\$privacydisclaimer]", $message["privacy_disclaimer"], $htmlBody);
				$body .= $this->boundaryPlaceholder . sprintf($htmlMsg, $style, $htmlBody);
			}

			return $body;

		}

		return ($html)? $message["abstract"] . $this->boundaryPlaceholder . "<html><body>".$message["body"]."</body></html>" : $message["abstract"];
	}

	/**
	 * send single mail from $data array
	 *
	 * @param array $data
	 */
	public function sendMail($data=array(), $createLog = true) {
		if (!$this->send($data)) {
			if($createLog) {
				$this->mailLog("ERROR: " . $this->Email->smtpError, "err",
					$data["to"], $data["subject"], $data);
			}
			throw new BeditaMailException(__("Mail delivery failed", true), $this->Email->smtpError);
		}
		if($createLog) {
			$this->mailLog("sent OK", "info", $data["to"], $data["subject"]);
		}
	}

	/**
	 * save mail log data
	 *
	 * @param string $msg
	 * @param string $level
	 * @param array $recipient
	 * @param string $subj
	 * @param array $data
	 */
	private function mailLog($msg, $level = "info", $recipient = null,
			$subj = null, array& $data = array()) {

		$mailLog = ClassRegistry::init("MailLog");
		$logData = array("MailLog"=>array("msg"=>$msg,
			"recipient" => $recipient, "log_level" => $level,
			"subject" => $subj));
		if(!empty($data)) {
			$logData["MailLog"]["mail_params"]	= serialize($data);
		}
		$mailLog->save($logData);
	}

	/**
	 * set to "injob" messages with status=pending and start_sending <= now
	 * @return array $msgIds
	 */
	public function lockMessages() {
		$msgIds = array();
		$mailMsgModel = ClassRegistry::init("MailMessage");
		$mailMsgModel->containLevel("mailgroup");
		$msgToLock = $mailMsgModel->find("all", array(
									"conditions" => array(
											"MailMessage.mail_status" => "pending",
											"MailMessage.start_sending <= '" . date("Y-m-d H:i:s",time()) . "'",
											)
									)
								);
		if (!empty($msgToLock)) {
			foreach ($msgToLock as $key => $message) {
				if (!empty($message["MailGroup"])) {
					$mailMsgModel->id = $message["id"];
					if (!$mailMsgModel->saveField("mail_status", "injob")) {
						throw new BeditaException(__("Mail message lock failed: id " . $message["id"]), true);
					}
					$msgIds[] = $message["id"];
				}
			}
		}
		return $msgIds;
	}

	/**
	 * create jobs from message with status "injob"
	 * @param array $msgIds
	 */
	public function createJobs(array $msgIds) {

		if (empty($msgIds)) {
			return ;
		}

		$mailMsgModel = ClassRegistry::init("MailMessage");
		$msgToSend = $mailMsgModel->find("all", array(
									"conditions" => array(
										//"MailMessage.mail_status" => "injob",
										"MailMessage.id" => $msgIds
										),
									"contain" => array("BEObject" => array("RelatedObject"), "Content", "MailGroup")
									)
								);

		$groupCardModel = ClassRegistry::init("MailGroupCard");
		$groupCardModel->bindModel(array(
				"belongsTo" => array(
					'BEObject' => array(
						'className'		=> 'BEObject',
						'foreignKey'	=> 'card_id'
					)
				)
			), false);
		$groupCardModel->contain("Card", "BEObject");

		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("default");

		$data["status"] = "unsent";
		$data["process_info"] = getmypid();

		foreach ($msgToSend as $message) {

			$data["mail_message_id"] = $message["id"];

			if (!empty($message["MailGroup"])) {
				foreach ($message["MailGroup"] as $group) {

					$res = $groupCardModel->find("all", array(
						"conditions" => array(
							"mail_group_id" => $group["id"],
							"MailGroupCard.status" => "confirmed",
							"Card.mail_status" => "valid",
							"BEObject.status" => "on"
							)
						)
					);

					foreach ($res as $groupCard) {
						// create job only if it doesn't exist
						if ($jobModel->find("count", array(
														"conditions" => array(
																"card_id" => $groupCard["MailGroupCard"]["card_id"],
																"mail_message_id" => $message["id"]
																),
														"contain" => array()
														)
											) == 0) {
							$data["card_id"] = $groupCard["MailGroupCard"]["card_id"];

							// prepare mail body using template
							$data["mail_body"] = $this->prepareMailBody($message, $groupCard["Card"]["mail_html"], $group["id"], $data["card_id"]);

							$jobModel->create();
							if (!$jobModel->save($data)) {
								throw new BeditaException(__("Error creating jobs"),true);
							}
						}
					}

				}
			}
		}

		$groupCardModel->unbindModel(
			array("belongsTo" => array('BEObject')),
			false
		);
	}

	/**
	 * execute active jobs
	 */
	public function sendQueuedJobs(array $msgIds) {

		if (empty($msgIds)) {
			return ;
		}
		$process_info = getmypid();
		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("detailed");
		$jobsToDo = $jobModel->find("all", array(
								"conditions" => array(
									"MailJob.status" => "unsent",
									"MailJob.mail_message_id" => $msgIds,
									"MailJob.process_info" => $process_info
								)
							)
						);

		$messagesSent = array();
		$mailMsgModel = ClassRegistry::init("MailMessage");

		foreach ($jobsToDo as $job) {

			if (!in_array($job["MailMessage"]["id"], $messagesSent)) {
				$messagesSent[] = $job["MailMessage"]["id"];
			}

			if ($job["Card"]["mail_status"] == "valid") {
				$data["to"] = $job["Card"]["newsletter_email"];
				$data["from"] = (!empty($job["MailMessage"]["sender_name"]))? $mailMsgModel->getCompleteSender(null, $job["MailMessage"]["sender"], $job["MailMessage"]["sender_name"]) : $job["MailMessage"]["sender"];
				$data["replyTo"] = $job["MailMessage"]["reply_to"];
				$data["subject"] = $job["MailMessage"]["Content"]["subject"];
				$data["mailType"] = (!empty($job["Card"]["mail_html"]))? "both" : "txt";
				$data["body"] = $job["MailJob"]["mail_body"];
				unset($job["MailMessage"]);
				unset($job["Card"]);

				$count = $jobModel->find("count", array(
								"conditions" => array(
									"MailJob.status" => "unsent",
									"MailJob.id" => $job["MailJob"]["id"],
									"MailJob.process_info" => $process_info
								)
							)
						);

				if ($count == 0) {
					$this->log("MailJob " . $job["MailJob"]["id"] . " handled by another process");
				} else {
					try {
						$this->sendMail($data, false);
						$job["MailJob"]["sending_date"] = date("Y-m-d H:i:s",time());
						$job["MailJob"]["status"] = "sent";
						$jobModel->save($job);
					} catch(BeditaMailException $ex) {
						$job["MailJob"]["status"] = "failed";
						$job["MailJob"]["smtp_err"] = $ex->getSmtpError();
						$jobModel->save($job);
						$this->log($ex->errorTrace());
					} catch (BeditaException $ex) {
						$job["MailJob"]["status"] = "failed";
						$jobModel->save($job);
						$this->log($ex->errorTrace());
					}
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
		$mailMsgModel->Behaviors->enable('ForeignDependenceSave');

	}

	/**
	 * Send notification mail and save mail job data
	 * Try to send all "unsent" notifications and all "pending" notifications stayed in queue too much time (see $timeout param)
	 *
	 * @param $timeout, time in minute for check when a message is considered blocked (it has to resend)
	 */
	public function notify($timeout = 20) {
		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("minimum");
		// get unset notifications
		$jobsToSend = $jobModel->find('all', array(
			"conditions" => array("mail_message_id is NULL", "status" => "unsent")
		));

		// get blocked notifications
		$timeoutTS = mktime(date("H"), date("i")-$timeout, date("s"), date("n"), date("j"), date("Y"));
		$timeoutDate = date("Y-m-d H:i:s", $timeoutTS);
		$jobsBlocked = $jobModel->find('all', array(
			"conditions" => array(
				"mail_message_id is NULL",
				"status" => "pending",
				"modified < '" . $timeoutDate . "'"
			)
		));

		// merge jobs and update them
		$jobsToSend = array_merge($jobsToSend, $jobsBlocked);
		foreach ($jobsToSend as $job) {
			$jobModel->id = $job['MailJob']['id'];
			$jobModel->saveField("status", "pending");
		}

		$mailMsgModel = ClassRegistry::init("MailMessage");
		foreach ($jobsToSend as $job) {
            $data = array();
            $mailParams = unserialize($job['MailJob']['mail_params']);
			$data["to"] = $job["MailJob"]["recipient"];

			$data["from"] = (!empty($mailParams["sender_name"]))? $mailMsgModel->getCompleteSender(null, $mailParams["sender"], $mailParams["sender_name"]) : $mailParams["sender"];
			$data["replyTo"] = $mailParams["reply_to"];
			$data['subject'] = $mailParams['subject'];
			$data['mailType'] = !empty($mailParams['mail_type']) ? $mailParams['mail_type'] : 'txt';
			$data["body"] = $job["MailJob"]["mail_body"];
			if(!empty($mailParams["signature"])) {
				$data["body"] .= "\n\n--\n" . $mailParams["signature"];
			}
			$jobModel->id = $job['MailJob']['id'];
            if (!$this->send($data)) {
                $this->log('Notification mail delivery failed, job id: ' 
                    . $jobModel->id . ' ' . $this->Email->smtpError, 'error');
                $jobModel->saveField('status', 'failed');
                $jobModel->saveField('smtp_err', $this->Email->smtpError);
            } else {
                $jobModel->saveField('status', 'sent');
                $jobModel->saveField('sending_date', date('Y-m-d H:i:s'));
            }
        }
	}

	/**
	 * return array of messages that result blocked and
	 * update pid of mail jobs blocked
	 *
	 * @param int $timeout, time in minute for check when a message is considered blocked
	 * @return array of message ids
	 */
	public function getMessagesBlocked($timeout=20) {
		$msgBlocked = array();
		$process_info = getmypid();
		$timeoutTS = mktime(date("H"), date("i")-$timeout, date("s"), date("n"), date("j"), date("Y"));
		$timeoutDate = date("Y-m-d H:i:s", $timeoutTS);
		$messageModel = ClassRegistry::init("MailMessage");
		$messageInJob = $messageModel->find("all", array(
				"conditions" => array(
					"mail_status" => "injob",
					"start_sending < '" . $timeoutDate . "'"
				),
				"fields" => array("id"),
				"contain" => array()
			)
		);

		if (!empty($messageInJob)) {
			$jobModel = ClassRegistry::init("MailJob");
			foreach ($messageInJob as $m) {

				$lastSendingDate = $jobModel->field("sending_date", array("mail_message_id" => $m["id"]), "sending_date DESC");

				if ($lastSendingDate < $timeoutDate) {
					$jobsBlocked = $jobModel->find("all", array(
							"conditions" => array(
								"mail_message_id" => $m["id"],
								"status" => "unsent",
								"created < '" . $timeoutDate . "'"
							),
							"contain" => array()
						)
					);

					if (!empty($jobsBlocked)) {
						foreach ($jobsBlocked as $job) {
							$data["id"] = $job["MailJob"]["id"];
							$data["process_info"] = $process_info;
							$jobModel->save($data);
						}
						$msgBlocked[] = $m["id"];
					}
				}
			}
		}

		return $msgBlocked;
	}

	/**
	 * if available set smtp options to EmailComponent
	 */
	protected function setSmtpOptions() {
		$smtpOptions = Configure::read("smtpOptions");
		if (!empty($smtpOptions) && is_array($smtpOptions)) {
			$this->Email->smtpOptions = $smtpOptions;
			$this->Email->delivery = 'smtp';
		}
	}

	/**
	 * prepare data for email sending
	 *
	 * @param array $data
	 * @throws BeditaException
	 */
	private function prepareData(&$data) {
        $this->Email->reset();

        // check required fields
        if (empty($data['to'])) {
            $this->log('Missing recipient in mail message', 'error');
            return false;
        }

        if (empty($data['from'])) {
            $this->log('Missing from field in mail message', 'error');
            return false;
        }

        if (empty($data['subject'])) {
            $this->log('Missing subject in mail message', 'error');
            return false;
        }

		$this->setSmtpOptions();
		$this->Email->to = $data["to"];
		$this->Email->from = $data["from"];
		$this->Email->subject = $data["subject"];
		$this->Email->replyTo = (!empty($data["replyTo"]))? $data["replyTo"] : "";
		$this->Email->sendAs = (!empty($data["mailType"]))? $data["mailType"] : "txt";
		if (!empty($data["cc"]) && is_array($data["cc"])) {
			$this->Email->cc = $data["cc"];
		}
		if (!empty($data["bcc"]) && is_array($data["bcc"])) {
			$this->Email->bcc = $data["bcc"];
		}

		// force the default line length (70) to avoid breakline in url
		$this->Email->lineLength = "500";

		if (!empty($data["layout"])) {
			$this->Email->layout = $data["layout"];
		}
		if (!empty($data["template"])) {
			$this->Email->template = $data["template"];
		}

		// set template vars for html mail
		if ($this->Email->sendAs == "both" && strstr($data["body"], $this->boundaryPlaceholder)) {
			$bodyParts = explode($this->boundaryPlaceholder, $data["body"]);
			$this->Controller->set("textContent", $bodyParts[0]);
			$this->Controller->set("htmlContent", $bodyParts[1]);
			$this->Email->layout = (!empty($data["layout"]))? $data["layout"] : $this->layoutNewsletter;
			$this->Email->template = (!empty($data["template"]))? $data["template"] : $this->templateNewsletter;
			$data["body"] = null;
		}
		return true;
	}

	/**
	 * send email
	 *
	 * @param array $data
	 */
    private function send($data) {
        if (!$this->prepareData($data)) {
            return false;
        }
        if (!$this->Email->send($data['body'])) {
            return false;
        }
        return true;
    }
}
?>