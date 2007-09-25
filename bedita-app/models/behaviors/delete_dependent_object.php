<?php
/**
 * 
 * Serve per cancellare gli oggetti figli dipendenti dall'oggetto da cancellare.
 * Come le sezioni quando si cancella un'area, non server per gli oggetti
 * che stanno all'interno di + contenitori e/o per gli oggetti che non contengono
 * altri oggetti in modo esclusivo.
 * 
 * giangi@qwerg.com
 *
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
		
		$filter = 0 ;
		$conf  = Configure::getInstance() ;
		
		foreach ($this->config[$model->name] as $type) {
			$filter |= $conf->objectTypes[strtolower($type)] ;
		}
		
		// Se sono stati selezionati tipi specifici, aggiunge anche il tipo del model corrente
		if($filter) {
			$filter |= $conf->objectTypes[strtolower($model->name)] ;
		}
		
		// Preleva gli oggetti discendenti 
		if(!class_exists('Tree')) loadModel('Tree');		
		$tree = new Tree ;
		
		$descendents = $tree->getAll($model->id, null, null, $filter) ;
		
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
		
		if(!isset($conf->objectTypeModels[$tree['object_type_id']])) return true ;
		$modelName 	= $conf->objectTypeModels[$tree['object_type_id']];
		
		if(!class_exists($modelName)) loadModel($modelName);		
		$model = new $modelName ;

		// Cancella l'oggetto
		$result = $model->Delete($tree['id']);
		
		return $result ;
	}
	
}
?>
