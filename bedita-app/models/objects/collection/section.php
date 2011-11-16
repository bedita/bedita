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
 * Section of website/publication
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Section extends BeditaCollectionModel
{
	var $actsAs = array();
	
	public $searchFields = array("title" => 10 , "description" => 6);

	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
										"UserCreated", 
										"UserModified", 
										"Permission",
										"ObjectProperty",
										"LangText",
										"Alias",
										"Version" => array("User.realname", "User.userid")
										)
									),

       			"default" => array("BEObject" => array("ObjectProperty", 
									"LangText", "ObjectType")),

				"minimum" => array("BEObject" => array("ObjectType")),
		
				"frontend" => array("BEObject" => array("LangText"), "Tree")
		);
	
	var $validate = array(
		'title'	=> array(
			'rule' => 'notEmpty',
			'required' => true
		),
		'parent_id'	=> array(
			'rule' => 'notEmpty'
		)
	) ;
	

	function afterSave($created) {
		if (!$created) 
			return ;
		
		$tree = ClassRegistry::init('Tree');
		if($tree->appendChild($this->id, $this->data[$this->name]['parent_id'])===false)
			return false;
		return true;
	}
    
	public function feedsAvailable($areaId) {
        $feeds = $this->find('all', array(
                'conditions' => array('Section.syndicate' => 'on', 'BEObject.status' => 'on', "Tree.object_path LIKE '/$areaId/%'"),
                'fields' => array('BEObject.nickname', 'BEObject.title'),
                'contain' => array("BEObject", "Tree"))
        );
        $feedNames = array();
        foreach ($feeds as $f) {
        	$feedNames[] = $f['BEObject'];	
        }
        
        return $feedNames;
    }
    
}
?>
