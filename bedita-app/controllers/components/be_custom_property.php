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
 * Custom properties handling component
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: $
 * @modifiedby 		$LastChangedBy: $
 * @lastmodified	$LastChangedDate: $
 * 
 * $Id: $
 */

class BeCustomPropertyComponent extends Object {
	static $SWITCH_USER		= 'user' ;
	static $SWITCH_GROUP	= 'group' ;
	
	var $controller			= null ;
	
	function __construct() {
	} 
	
	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
	}
	
	/**
	 * Aggiunge 1 o + permessi a 1 o + moduli.
	 * 
	 *
	 * @param mixed $names	Se una stringa, e' il nome di modulo solo
	 * 						se un array, {0..N} nomi di moduli
	 * @param array $perms	{1..N} items:
	 * 						name, switch, flag
	 * 							name	userid o nome gruppo
	 * 							switch  PermissionComponent::SWITCH_USER o PermissionComponent::SWITCH_GROUP
	 * 							flag	insieme di bit con le operazioni sopra definite
	 * @return boolean
	 */
	function add($names, &$perms) {
		$this->array2perms($perms, $formatedPerms) ;
		
		if(!is_array($names)) $names = array($names); 
		foreach ($names as $name) {
			
			for($i=0; $i < count($formatedPerms) ; $i++) {
				$item = &$formatedPerms[$i] ;
				
				if($this->PermissionModule->replace($name, $item['name'], $item['switch'], $item['flag']) === false) {
					return false ;
				}				
			}
		}
		
		return true ;
	}
	
	/**
	 * Formatta, per salvataggio, le custom properties passate
	 *
	 * @param unknown_type $data
	 */
	function setupForSave(&$data) {
		$tmp = array() ;
		if(!@count($data)) return ;
		
		foreach($data as $name => $value) {
//			if(!(isset($value["name"])  && isset($value["type"]) && isset($value["value"]))) continue ;
			
			switch($value["type"]) {
				case "integer" : 	{ settype($value["value"], "integer") ; } break ;
				case "bool" : 		{ settype($value["value"], "boolean") ; } break ;
				case "float" : 		{ settype($value["value"], "double") ; } break ;
				case "string" :		{ settype($value["value"], "string") ; } break ;
			}
			
			$tmp[$name] = $value["value"] ;
		}
		
		$data = $tmp ;
	}
	
}

?>