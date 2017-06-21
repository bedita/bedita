<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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
 * Behavior class to send email notification to users.
 * Currenly handles this scenarios:
 * 	- new notes or comments added to an object
 *  - object has been modified
 *  - user profile created or modified
 * Notifications events are triggered accordingo to user preferences
 * (on comments/notes and objects created)
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class NotifyBehavior extends ModelBehavior {

	protected $modelNameToUserField = array("Comment" => "comments", "EditorNote" => "notes");
	protected $notifyMsg = null;

	function setup(&$model, $settings=array()) {
	}

	function afterSave($model, $created) {

		$data =& $model->data[$model->alias];
		$users = array();
		$creator = array();

		if ($model->name == "Comment" || $model->name == "EditorNote") {

			$userField = $this->modelNameToUserField[$model->name];
			// exit if comment has been modified (not created)
			if(!$created && $userField == "comments") {
				return;
			}

			$c = ClassRegistry::init($model->name)->find("first", array(
					"conditions" => array(
						"ReferenceObject.id" => $data["object_id"]
					),
					"contain" => array("ReferenceObject")
				)
			);

			$userModel = ClassRegistry::init("User");
			$conditions = array("(" .$userField . "='all'
								OR (" .$userField . "='mine' AND id='". $c["ReferenceObject"]["user_created"] ."'
								))");
			if($userField == "notes") { // don't send mail to editor itself
				$conditions[] = "id <> " . $data["user_modified"];
			}

			$users = $userModel->getUsersToNotify($conditions);

			// exclude editor note notifies for users without backend authorization
			if($userField == "notes") {
				foreach ($users as $key => $u) {
					$backendAuth = Set::extract("/Group/backend_auth", $u);

					if (array_sum($backendAuth) == 0) {
						unset($users[$key]);
					}
				}
			}

			if(empty($data['author'])) {
				$data['author'] = $userModel->field("userid",
					array("id" => $data["user_modified"]));
			}
			$data['object_title'] = $c["ReferenceObject"]["title"];

			$this->prepareAnnotationMail($model, $users);
		} else if($model->name == "User") {

			$this->prepareUserSettingsMail($model, $created);

		} else if (!$created && !empty($data["user_modified"])
				&& !empty($data["user_created"])) {
			$userModel = ClassRegistry::init("User");
			$data['author'] = $userModel->field("userid",array("id" => $data["user_modified"]));
			$creator = $userModel->getUsersToNotify(array("notify_changes" => "1",
				"id = " . $data["user_created"], "id <> " . $data["user_modified"]));
			if(!empty($creator)) {
				$this->prepareObjectChangeMail($model, $creator);
			}
		}
	}

	public function prepareUserSettingsMail($userModel, $created) {
		$modData =& $userModel->data[$userModel->alias];
		if(!empty($modData["email"])) { // send only if email field is present...

			$this->loadMessages();
			$msgType = $created ? "newUser" : "updateUser";
			$detailMsg = (!empty($modData["passwd"]) && !$created) ? "Your password was changed!" : "";
			$detailMsg .= ($modData["valid"] == 0) ? "\nYour account is blocked!" : "";

			$params = array("author" => "",
				"title" => "",
				"url" => Configure::read("beditaUrl"),
				"beditaUrl" => Configure::read("beditaUrl"),
				"text" => "Real Name: ". $modData["realname"] . "\nUserid: " .
					$modData["userid"] . "\n" . $detailMsg . "\n",
			);

			$users = array(0=>array("User" => $modData));
			$this->createMailJob($users, $msgType, $params);
		}
	}

	public function prepareAnnotationMail($model, array &$users) {

		$this->loadMessages();
		$modData =& $model->data[$model->alias];
		$msgType = Inflector::underscore($model->alias); // note or comment
		if($msgType == "comment") {
			$modData["url_id"] = $modData["id"]; // if comment, point to comment detail
		} else {
			$modData["url_id"] = $modData["object_id"]; // if note, point to annotated obj
		}

		$params = array("author" => $modData["author"],
			"title" => $modData["object_title"],
			"url" => $this->getContentUrl($modData),
			"beditaUrl" => Configure::read("beditaUrl"),
			"text" => $modData["description"],
		);


		$this->createMailJob($users, $msgType, $params);
	}

    public function prepareObjectChangeMail($model, array &$users) {

        $this->loadMessages();
        $modData =& $model->data[$model->alias];
        $modData['url_id'] =  $modData['id'];
        $params = array('author' => !empty($modData['author']) ? $modData['author'] : '',
            'title' => !empty($modData['title']) ? $modData['title'] : '',
            'url' => $this->getContentUrl($modData),
            'text' => !empty($modData['description']) ? $modData['description'] : '',
            'beditaUrl' => Configure::read('beditaUrl'),
        );
        $this->createMailJob($users, 'contentChange', $params);
    }

	/**
	 * create custom mail jobs using notify messages
	 *
	 * @param Model $model
	 * @param array $users
	 * @param String $msgType
	 * @param array $params
	 */
	public function prepareCustomMail(&$model, array &$users, $msgType, array &$params) {
		if (!empty($msgType)) {
			$this->loadMessages();
			$this->createMailJob($users, $msgType, $params);
		}
	}

	protected function createMailJob(array &$users, $msgType, array &$params) {
		$jobModel = ClassRegistry::init("MailJob");
		$jobModel->containLevel("default");
		$data = array();
		$data["status"] = "unsent";
		$conf = Configure::getInstance();
		foreach ($users as $u) {

			$data["recipient"] = $u['User']['email'];
			$params["user"] = $u['User']['userid'];
			$lang = isset($u['User']['lang']) ? $u['User']['lang'] : "eng";
			$subject = $this->getNotifyText($msgType, "subject", $params, $lang);

			$data["mail_params"] = serialize(array("reply_to" => $conf->mailOptions["reply_to"],
						"sender" => $conf->mailOptions["sender"],
						"subject" => $subject,
						"signature" => $conf->mailOptions["signature"]
			));
			$data["mail_body"] = $this->getNotifyText($msgType, "mail_body", $params, $lang);
			// skip creation if a duplicate mail is already present
			$res = $jobModel->find("all", array(
				"conditions" => array("recipient" => $data["recipient"], "status" => $data["status"],
				"mail_body" => $data["mail_body"])));
			if(!empty($res)) {
				$this->log("duplicate job for " . $data["recipient"], LOG_DEBUG);
			} else {
				$jobModel->create();
				if (!$jobModel->save($data)) {
					throw new BeditaException(__("Error creating mail jobs"),true);
				}
			}
		}

	}

	protected function loadMessages() {
		// merge default, backend local and frontend messages if present
		$notify = array();
		require(BEDITA_CORE_PATH.DS."config".DS."notify".DS."default.msg.php");

		if (file_exists(BEDITA_CORE_PATH.DS."config".DS."notify".DS."local.msg.php")) {
			require(BEDITA_CORE_PATH.DS."config".DS."notify".DS."local.msg.php");
		}

		if (!BACKEND_APP && file_exists(APP . "config" . DS . "notify" . DS . "frontend.msg.php")) {
			require(APP . "config" . DS . "notify" . DS . "frontend.msg.php");
		}
		$this->notifyMsg = &$notify;
	}

	/**
	 * Return well formatted notification text replacing markplace with related text
	 *
	 * @param  string $msgType the name (key) of notification message array (self::notifyMsg)
	 * @param  string $field   the field of notification message array (self::notifyMsg)
	 * @param  array  $params  array of fields to replace in notification text.
	 *                         The key is the markplace and the value is the text that replace the markplace
	 *                         "title" => "my title", replace markplace [$title] with "my title"
	 * @param  string $lang    language of notification
	 * @return string          well formatted text ready to notify
	 */
	protected function getNotifyText($msgType, $field ,array &$params, $lang) {
		if(isset($this->notifyMsg[$msgType][$lang][$field])) {
			$text = $this->notifyMsg[$msgType][$lang][$field];
		} else {
			$text = $this->notifyMsg[$msgType]["eng"][$field]; // default fallback
		}
		// replace [BEdita] with projectName in subject field
		if ($field == "subject") {
			$projectName = Configure::read('projectName');
			if (!empty($projectName)) {
				$text = str_replace("[BEdita]", "[$projectName]", $text);
			}
		}
		// replace markplace as [$user], [$title], etc... with $params["user"], $params["title"], etc...
		if (preg_match_all("/\[\\\$(.+?)\]/", $text, $matches)) {
			foreach($matches[1] as $key => $m) {
				if (!empty($params[$m])) {
					$text = str_replace($matches[0][$key], $params[$m], $text);
				}
			}
		}
		return $text;
	}

	/**
	 * return notificated content url
	 */
	protected function getContentUrl($modData) {
		$url = "";
		if (!BACKEND_APP) {
			if (!empty($modData["notification_content_url"])) {
				$url = $modData["notification_content_url"];
			}
		} else {
			$url = Configure::read("beditaUrl") . "/view/" . $modData["url_id"];
		}
		return $url;
	}

}

?>