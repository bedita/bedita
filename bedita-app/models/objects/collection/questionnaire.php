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
class Questionnaire extends BEAppCollectionModel
{
	var $name 		= 'Questionnaire';
	var $useTable 	= 'view_questionnaires' ;
	var $recursive 	= 2 ;
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'Collection'),
			'DeleteDependentObject'	=> array('question'),
			'DeleteObject' 			=> 'objects',
	); 

	var $hasOne = array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => 'BEObject.object_type_id = 4',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Collection' =>
				array(
					'className'		=> 'Collection',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
	) ;			

	function __construct() {
		parent::__construct() ;
	}

	/**
 	* Sovrascrive completamente il save() l'oggetto non ha una tabella
 	* specifica ma una vista, non deve salvare
 	*/
	function save($data = null, $validate = true, $fieldList = array()) {
		$conf = Configure::getInstance() ;		
		
		if(isset($data['BEObject']) && !isset($data['BEObject']['object_type_id'])) {
			$data['BEObject']['object_type_id'] = $conf->objectTypes[strtolower($this->name)]["id"] ;
		} else if(!isset($data['object_type_id'])) {
			$data['object_type_id'] = $conf->objectTypes[strtolower($this->name)]["id"] ;
		}

		$this->set($data);

		if ($validate && !$this->validates()) {
			return false;
		}

		if (!empty($this->behaviors)) {
			$behaviors = array_keys($this->behaviors);
			$ct = count($behaviors);
			for ($i = 0; $i < $ct; $i++) {
				if ($this->behaviors[$behaviors[$i]]->beforeSave($this) === false) {
					return false;
				}
			}
		}
		
		if(empty($this->id)) $created = true ;
		else $created = false ; 

		$this->setInsertID($this->BEObject->id);
		$this->id = $this->BEObject->id ;
		
		if (!empty($this->behaviors)) {
			$behaviors = array_keys($this->behaviors);
			$ct = count($behaviors);
			for ($i = 0; $i < $ct; $i++) {
				$this->behaviors[$behaviors[$i]]->afterSave($this, null);
			}
		}

		$this->afterSave($created) ;
		$this->data = false;
		$this->_clearCache();
		$this->validationErrors = array();
		
		return true ;
	}
	
	/**
	 * Inserisce nell'albero
	 */
	function afterSave($created) {
		if (!$created) return ;
		
		if(!class_exists('Tree')) loadModel('Tree');
		$tree 	=& new Tree();
		$tree->appendChild($this->id, null) ;		
	}
	
 	function getQuestions($userid = null, $status = null) {
 		return  $this->getChildren($this->id, $userid, $status,  false, 1, 1000000) ;
 	}

	function appendChild($id, $idParent = null) {
		return $this->handlerChildren($id, (isset($idParent)?$idParent:$this->id), 5) ;
	}
	
	function moveChildUp($id, $idParent = null) {
		return $this->handlerChildren($id, (isset($idParent)?$idParent:$this->id), 1) ;
	}
	
	function moveChildDown($id, $idParent = null) {
		return $this->handlerChildren($id, (isset($idParent)?$idParent:$this->id), 2) ;
	}

	function moveChildFirst($id, $idParent = null) {
		return $this->handlerChildren($id, (isset($idParent)?$idParent:$this->id), 3) ;
	}
	
	function moveChildLast($id, $idParent = null) {
		return $this->handlerChildren($id, (isset($idParent)?$idParent:$this->id), 4) ;
	}

	function removeAllChildren($idParent = null) {
		if(!$this->Faq->getItems($queries)) return false ;
		
		if(!class_exists('FaqQuestion')) loadModel('FaqQuestion');
		$FaqQuestion =& new FaqQuestion();
		
		foreach ($queries as $query) {
			if(!$FaqQuestion->delete($query['id'])) return false ;
		}
		
		return true ;
	}

	/**
	 * Esegue l'operazione passata
	 *
	 * @param unknown_type $id			id del figlio
	 * @param unknown_type $idParent	id della FAQ
	 * @param unknown_type $operation	operazione: 1: up, 2: down, 3:first, 4:last, 5: append
	 */
	private function handlerChildren($id, $idParent, $operation) {
		if(!class_exists('Tree')){
			loadModel('Tree');
		}
		$tree =& new Tree();
		
		switch($operation) {	
			case 1: $ret = $tree->moveChildUp($id, $idParent) ; break ;
			case 2: $ret = $tree->moveChildDown($id, $idParent) ; break ;
			case 3: $ret = $tree->moveChildFirst($id, $idParent) ; break ;
			case 4: $ret = $tree->moveChildLast($id, $idParent) ; break ;
			case 5: $ret = $tree->appendChild($id, $idParent) ; break ;
		}
		
		return $ret ;
	}
}
?>
