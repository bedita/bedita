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

			if(!$model->$name->save($data)) {
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
				$first  = false ;
			}
		}
		return true ;	
	}
}
?>
