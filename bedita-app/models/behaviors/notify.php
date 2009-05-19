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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class NotifyBehavior extends ModelBehavior {
 
	private $modelNameToUserField = array("Comment" => "comments", "EditorNote" => "notes");
	
	function setup(&$model, $settings=array()) {
	}
	
	function afterSave($model, $created) {
		
		$data =& $model->data[$model->alias];
		$users = array();
		$creator = array();
		
		if ($model->name == "Comment" || $model->name == "EditorNote") {
			$c = ClassRegistry::init($model->name)->find("first", array(
					"conditions" => array(
						"ReferenceObject.id" => $data["object_id"]
					),
					"contain" => array("ReferenceObject")
				)
			);
			
			$userField = $this->modelNameToUserField[$model->name];
			$userModel = ClassRegistry::init("User");
			$conditions = array("(" .$userField . "='all' 
								OR (" .$userField . "='mine' AND id='". $c["ReferenceObject"]["user_created"] ."'
								))");
			$users = $userModel->getUsersToNotify($conditions);
			
		} else if (!$created && !empty($data["user_modified"])) {
			$userModel = ClassRegistry::init("User");
			$creator = $userModel->getUsersToNotify(array("notify_changes" => "1", "id <> " . $data["user_modified"]));
		}
		
		$users = array_merge($creator,$users);
//		pr($users);exit;
//TODO: add to mail_jobs recipient fields and create jobs

	}
	
	
	 
	
}
 
?>