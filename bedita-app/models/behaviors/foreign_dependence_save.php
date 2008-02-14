<?php
/**
 * 
 * Serve per gestire le dipendenze
 * imposte dalle foreign key.
 * Con Configure vengono passati i nomi dei models da gestire prima 
 * del model corrente.
 * 
 * 
 * 
 * giangi@qwerg.com
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
			if(isset($model->$name)) {				
				$this->_data4Parent($model, $name, $data) ;				
				
				// Se il model ha delle associazioni hasAndBelongsToMany, formatta l'array di dati
				if(count($model->$name->hasAndBelongsToMany))  {
					$tmp = array("$name" => &$data) ;
					foreach ($model->$name->hasAndBelongsToMany as $k => $assoc) {
						if(isset($data[$k])) {
							$tmp[$k] = array($k => &$data[$k]) ;
						}
					}
					$data = &$tmp ;
					
					unset($tmp);
				}
				
				// salva il/i parent
				$run = true ;			
				if(!$model->$name->save($data)) {
					$model->validationErrors = $model->$name->validationErrors ;
					
					// Se e' gia' stato creato il primo elemento, torna esegue la cancellazione
					if(!$first) {
						$model->$firstModel->delete($id) ;
					}
					echo '<pre>';
					print_r($model->$name);
					echo '</pre>';
					die();
					return false ;
				}
				
				// Se e' il primo model della lista, preleva l'ID eventaule inserito
				if($first) {
					$firstModel = $name ;
					$id = $model->$name->getInsertID() ;
					$this->_insertNewID($model, $id) ;
					
					$first  = false ;
				}
			}
		}
		
		return true ;	
	}

	
	/**
	 * Preleva i dati d passare al parent
	 * 
	 */
/*	
	private function _data4Parent(&$model, $name, &$data) {
		//se nn ci sono dati continua
		if (is_array($model->data) && !count($model->data)) $data = array() ;
		
		if(isset($model->data[$name])) {
			$data = $model->data[$name] ;
			
		} elseif (isset($model->data[$model->name])) {
			$data = array($name => $model->data[$model->name]) ;
					
		} elseif (Set::countDim($model->data) == 1) {
			$data = array($name => $model->data) ;
		} else {
			$data = array() ;
		}
	}
*/
	private function _data4Parent(&$model, $name, &$data) {
		//se nn ci sono dati continua
		if (is_array($model->data) && !count($model->data)) $data = array() ;
		
		if(isset($model->data[$name])) {
			$data = $model->data[$name] ;
			
		} elseif (isset($model->data[$model->name])) {
			if(isset($model->data[$model->name][$name])) {
				$data = $model->data[$model->name][$name] ;
			} else {
				$data = $model->data[$model->name] ;
			}
					
		} elseif (Set::countDim($model->data) == 1) {
//			$data = array($name => $model->data) ;
			$data = $model->data ;
		} else {
			$data = array() ;
		}
	}

	/**
	 * Inserisce nei dati l'id creato
	 * 
	 */
	private function _insertNewID(&$model, $id) {
		if($id === null)	return ;
		
		if (Set::countDim($model->data) == 1) {
			$model->data['id'] = $id ;
		} else {
			$keys = array_keys($model->data) ;
			foreach ($keys as $k) {
				if(!isset($model->data[$k]['id'])) {
					$model->data[$k]['id'] = $id ;
				}
			}
		}
	}

}
?>
