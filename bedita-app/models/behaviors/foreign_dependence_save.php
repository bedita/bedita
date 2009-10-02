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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

/**
 * 
 * Serve per gestire le dipendenze
 * imposte dalle foreign key.
 * Con Configure vengono passati i nomi dei models da gestire prima 
 * del model corrente.
 *
 */

class ForeignDependenceSaveBehavior extends ModelBehavior {
	var $config = array();
	
	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ;
	}

	/**
	 * Se sono stati definiti dei models da salvare precedentemente 
	 * al corrent, gli passa i dati e ne richiede il salvataggio.
	 *
	 * @param object $model
	 * @return boolean
	 */
	function beforeSave(&$model) {
		$first 		= true ;		// true, quando processa il primo model (se necessario crea il nuovo id)
		$id			= null ;		// Se presente e' stato creato un nuovo elemento
		$firstModel	= null ;		// Il nome del Model iniziale
		
		foreach ($this->config[$model->name] as $name) {

			$data = array($name => $model->data[$model->name]);
			// Se il model ha delle associazioni hasAndBelongsToMany, formatta l'array di dati
			if(count($model->$name->hasAndBelongsToMany))  {
				foreach ($model->$name->hasAndBelongsToMany as $k => $assoc) {
					if(isset($data[$name][$k])) {
						$data[$k] = array($k => &$data[$name][$k]);
					}
				}
			}
				
			// salva il/i parent
			$run = true ;
			$model->$name->create();
			if(!$res = $model->$name->save($data)) {
				$model->validationErrors = $model->$name->validationErrors ;
				// Se e' gia' stato creato il primo elemento, torna esegue la cancellazione
				if(!$first) {
					$model->$firstModel->delete($id) ;
				}
				return false ;
			}
			
			// Se e' il primo model della lista, preleva l'ID eventaule inserito
			if($first) {
				$firstModel = $name ;
				if(empty($model->data[$model->name]['id'])) {
					$id = $model->$name->getInsertID() ;
					$model->data[$model->name]['id'] = $id;
				}
				
				if ($firstModel == "BEObject" && !empty($res["BEObject"]["user_modified"]))
					$model->data[$model->name]["user_modified"] = $res["BEObject"]["user_modified"];
				
				$first  = false ;
			}
		}
		return true ;	
	}
}
?>
