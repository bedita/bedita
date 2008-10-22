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
class MailMessage extends BeditaContentModel
{
	var $actsAs 	= array(
			'CompactResult' 		=> array("MailGroup"),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject', 'Content'),
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
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"RelatedObject"
															),
									 "Content", "MailGroup"
									),
				"default" => array("BEObject" => array("ObjectType", "RelatedObject"), "Content"),
									
				"mailgroup" => array("MailGroup"),

				"minimum" => array("BEObject" => array("ObjectType"))
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
		)
	);
	
	
	function beforeValidate() {

        $this->checkDate('start');
        $this->checkDate('end');
		$this->checkDate('start_sending');

        $data = &$this->data[$this->name] ;
        if(!empty($data['start_sending']) && !empty($data['start_sending_time'])) {
            $data['start_sending'] .= " " . $data['start_sending_time'];
        }

        return true;
	}
}
?>