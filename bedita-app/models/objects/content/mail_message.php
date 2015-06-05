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
 * Mail message content
 */
class MailMessage extends BeditaContentModel
{
	var $actsAs 	= array(
			'CompactResult' 		=> array("MailGroup"),
			'ForeignDependenceSave' => array('Content'),
			'DeleteObject' 			=> 'objects',
	); 
	
	var $hasOne= array(
			'BEObject' => array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' => array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
		);
	
	var $hasAndBelongsToMany = array(
			'MailGroup' =>	array (
					'joinTable' => 'mail_group_messages'
				)
	);

    protected $modelBindings = array(
        "detailed" => array(
            "BEObject" => array(
                "ObjectType",
                "UserCreated",
                "UserModified",
                "Permission",
                "RelatedObject",
                "Annotation",
                "Version" => array("User.realname", "User.userid")
            ),
            "Content",
            "MailGroup"
        ),
        "default" => array(
            "BEObject" => array("ObjectType", "RelatedObject"),
            "Content"
        ),
        "mailgroup" => array("MailGroup"),
        "minimum" => array(
            "BEObject" => array("ObjectType"),
            "Content"
        )
    );

	var $validate = array(
		"subject" => array(
			"rule" 			=> array('custom', '/.+/') ,
			"required" 		=> true,
			"message" 		=> "Subject required"
		),
		
		"sender" => array(
			"rule"	=> "email",
			"required" => true,
			"message"	=> "Please supply a valid email address."
		),
		"reply_to" => array(
			"rule" => "email",
			"allowEmpty" => true,
			"message"	=> "Please supply a valid email address."
		),
		"bounce_to" => array(
			"rule" => "email",
			"allowEmpty" => true,
			"message"	=> "Please supply a valid email address."
		)
	);
	
	
	function beforeValidate() {

        $this->checkDate('start_date');
        $this->checkDate('end_date');
		$this->checkDate('start_sending');

        $data = &$this->data[$this->name] ;
        if(!empty($data['start_sending']) && !empty($data['start_sending_time'])) {
            $data['start_sending'] .= " " . $data['start_sending_time'];
        }
		if(empty($data['subject'])) {
			$data['subject'] = $data['title'];
		}
        return true;
	}

	/**
	 * return a complete sender email address "sender name <sender@bedita.com>
	 *
	 * @param int $id MailMessage.id (if not empty use MailMessage.id otherwise use the others parameters)
	 * @param string $senderEmail
	 * @param string $senderName
	 * @return mixed, false if it's not find any email
	 */
	public function getCompleteSender($id=null, $senderEmail=null, $senderName=null) {
		if (!empty($id)) {
			$res = $this->find("first", array(
				"conditions" => array("id" => $id),
				"fields" => array("sender_name", "sender"),
				"contain" => array()
			));
			if (empty($res)) {
				return false;
			}
			$senderName = $res["sender_name"];
			$senderEmail = $res["sender"];
		}
		if (empty($senderEmail)) {
			return false;
		}
		$sender = (!empty($senderName))? $senderName . " <" . $senderEmail . ">" : $senderEmail;
		return $sender;
	}
}
