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
class User extends BEAppModel
{

	var $validate = array(
		'userid' => array(
			'rule' => 'notEmpty'
		),
		'pasword' => array(
			'rule' => 'notEmpty'
		)
	);

	protected $modelBindings = array( 
		"detailed" =>  array("Group", "Card", "Permission"),
		"default" => array("Group", "Card"),
		"minimum" => array()		
	);
	
	var $hasAndBelongsToMany = array(
			'Group',
			'Card' =>
				array(
					'className'    => 'Card',
					'joinTable'       => 'object_users',
					'foreignKey'      => 'user_id',
					'associationForeignKey' => 'object_id',
					'unique'    => true
				)
			);

	var $hasMany = array(
		'Permission' =>
			array(
				'className'		=> 'Permission',
				'condition'		=> "Permission.switch = 'user' ",
				'fields'		=> 'Permission.object_id, Permission.switch, Permission.flag',
				'foreignKey'	=> 'id',
				'dependent'		=> true
			)
	);

	private $hBTM = null; 
		
    function unbindGroups() {
        $this->hBTM = $this->hasAndBelongsToMany;
    	$this->unbindModel(array('hasAndBelongsToMany' => array('Group')), false);
    }
    	
    function rebindGroups() {
        $this->bindModel(array('hasAndBelongsToMany' => $this->hBTM));
    }
        
    /**
	 * Viene riformattato il risultato:
	 * 		id => ; passwd => ; realname => ; userid => ; groups => array({1..N} nomi_grupppi)
	 *
	 * @param unknown_type $user
	 */
	function compact(&$user) {
		unset($user['Permission']) ;
		
		$user['User']['groups'] = array() ;
		foreach ($user['Group'] as $group) {
			$user['User']['groups'][] = $group['name'] ;
		}
		
		unset($user['Group']) ;
		
		$user = $user['User'] ;
	}
}
?>
