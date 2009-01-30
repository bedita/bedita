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

/**
 * Perform a children object delete dependant to original object to delete
 */
class DeleteDependentObjectBehavior extends ModelBehavior {
	var $config = array();
	
	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ; // nomi dei tipi di oggetti da prelevare
	}

	/**
	 * Preleva gli oggetti figli da cancellare
	 *
	 * @return unknown
	 */
	
	function beforeDelete(&$model) {
		// Se non vengono indicati delle tipologie di oggetti, esce
		if(!count($this->config[$model->name])) return ;
		
		$filter = false ;
		$conf  = Configure::getInstance() ;
		
		foreach ($this->config[$model->name] as $type) {
			if(!is_array($filter)) $filter = array() ;
			$filter[] = $conf->objectTypes[strtolower($type)]["id"] ;
		}
		
		// Se sono stati selezionati tipi specifici, aggiunge anche il tipo del model corrente
		if($filter) {
			$filter[] = $conf->objectTypes[strtolower($model->name)]["id"] ;
		}
		
		// Preleva gli oggetti discendenti 
//		if(!class_exists('Tree')) loadModel('Tree');		
//		$tree = new Tree ;
		$tree = ClassRegistry::init("Tree");
		$descendents = $tree->getAll($model->id, null, null, $filter) ;
		
		
//		$descendents = $model->findObjects($model->id, null, null, $filter, null, true, 1, 100000, true);
		
//		foreach ($descendents["items"] as $item) {
//			if(!$this->_deleteDescs($item)) {
//				return false ;
//			}
//		}
		for ($i=0; isset($descendents[0]['children']) && $i <count($descendents[0]['children']) ; $i++) {
			if(!$this->_deleteDescs($descendents[0]['children'][$i])) {
				return false ;
			}
		}
		
		return true ;
	}

	private function _deleteDescs(&$tree) {
		for ($i=0; $i <count($tree['children']) ; $i++) {
			if(!$this->_deleteDescs($tree['children'][$i])) {
				return false ;
			}
		}
		
		// Preleva la tipologia dell'oggetto
		$conf  = Configure::getInstance() ;
		
		if(!isset($conf->objectTypes[$tree['object_type_id']]["model"])) return true ;
		$modelName 	= $conf->objectTypes[$tree['object_type_id']]["model"];
		
//		if(!class_exists($modelName)) loadModel($modelName);		
//		$model = new $modelName ;

		$model = ClassRegistry::init($modelName);
		
		// Cancella l'oggetto
		$result = $model->del($tree['id']);
		
		return $result ;
	}
	
}
?>
