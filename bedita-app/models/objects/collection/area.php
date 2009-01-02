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
class Area extends BeditaCollectionModel
{

	var $actsAs = array();
	
	public $searchFields = array("title" => 10 , "description" => 6, 
		"public_name" => 10, "public_url" => 8);

	protected $modelBindings = array( 
			"detailed" =>  array("BEObject" => array("ObjectType", 
									"UserCreated", 
									"UserModified", 
									"Permissions",
									"ObjectProperty",
									"LangText")),

       		"default" => array("BEObject" => array("ObjectProperty", 
								"LangText", "ObjectType")),

			"minimum" => array("BEObject" => array("ObjectType"))		
	);
	
	function afterSave($created) {
		if (!$created) 
			return ;
		
		$tree = ClassRegistry::init('Tree', 'Model');
		$tree->appendChild($this->id, null) ;		
	}

	/**
	 * Esegue ricorsivamente solo la clonazione dei figli di tipo: Section e Community,
	 * gli altri reinscerisce un link
	 *
	 */
	protected function insertChildrenClone() {
		$conf  	= Configure::getInstance() ;
		$tree 	=& new Tree();
		
		// Preleva l'elenco dei figli
		$children = $tree->getChildren($this->oldID , null, null, false, 1, 10000000) ;
		
		// crea le nuove associazioni
		for ($i=0; $i < count($children["items"]) ; $i++) {
			$item = $children["items"][$i] ;
			
			switch($item['object_type_id']) {
				case $conf->objectTypes['section']["id"]:
				case $conf->objectTypes['community']["id"]: {
					$className	= $conf->objectTypes[$item['object_type_id']]["model"] ;
					
					$tmp = new $className() ;
					$tmp->id = $item['id'] ;
					
					$clone = clone $tmp ; 
					$tree->move($this->id, $this->oldID , $clone->id) ;
				}  break ;
				default: {
					$tree->appendChild($item['id'], $this->id) ;
				}
			}
		}
	}
	

}
?>
