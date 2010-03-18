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
										"Permissions",
										"ObjectProperty",
										"LangText",
										"Alias")),

       			"default" => array("BEObject" => array("ObjectProperty", 
									"LangText", "ObjectType")),

				"minimum" => array("BEObject" => array("ObjectType"))		
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

	//////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	/**
	 * Format data for the creation of a clone; every object type execute specific operation calling this method
	 * Find the id of the branch where the object is
	 *
	 * @param array $data		Data to format
	 * @param object $source	Source object
	 */
	protected function _formatDataForClone(&$data, $source = null) {
		if(!class_exists('Tree')) loadModel('Tree');

		$tree = new Tree();
		
		$data['parent_id'] = $tree->getParent($data['id'])  ;		
		parent::_formatDataForClone($data);
	}	
	
	/**
	 * Execute recursively only the clonation of types: Section e Community, for the others insert a link
	 */
	protected function insertChildrenClone() {
		$conf  	= Configure::getInstance() ;
		$tree 	= new Tree();
		
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
    
	public function feedsAvailable($areaId) {
        $this->bindModel( array(
        	'hasOne' => array(
        		'Tree' => array('foreignKey' => 'id')
        		)
        	)
        );
		
        $feeds = $this->find('all', array(
                'conditions' => array('Section.syndicate' => 'on', 'BEObject.status' => 'on', "Tree.path LIKE '/$areaId/%'"), 
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
