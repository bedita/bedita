<?php
/**
 * 
 * Serve per cnacellare un oggetto rispettando e usando le dipendenze
 * imposte dalle foreign key. Viene cancellato solo il record della
 * tabella base, il resto lo fase MySQL.
 * Con Configure viene passato il nome delal tabella radice.
 * 
 * 
 * giangi@qwerg.com
 *
 */

class DeleteObjectBehavior extends ModelBehavior {
	var $config = array();
	
	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ;
	}

	/**
	 * Elimina tutte le associazioni, saranno reiserite dopo la cancellazione.
	 * Dati i vincoli (foreignKey)  tra le tabelle in DB, viene forzata la 
	 * cancellazione del record della tabella iniziale, objects
	 *
	 * @return unknown
	 */
	
	function beforeDelete(&$model) {
		$model->tmpAssociations = array();
		$model->tmpTable 		= $model->table ;
		
		$associations = array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany');
		foreach ($associations as $association) {
			$model->tmpAssociations[$association] = $model->$association ;
			$model->$association = array() ;
		}
		$configure = $this->config[$model->name] ;
		
		$model->table =  (isset($configure) && is_string($configure)) ? $configure : $model->table ;
		
		return true ;
	}

	/**
	 * Reinserice le associazioni
	 *
	 */
	function afterDelete(&$model) {
		// Cancella i riferimenti del'oggetto nell'albero
		if(!class_exists('Tree')){
			loadModel('Tree');
		}		
		$tree = new Tree ;
		$tree->del($model->id) ;
		
		// Ripristina le associazioni
		foreach ($model->tmpAssociations as $association => $v) {
			$model->$association = $v ;
		}
		unset($model->tmpAssociations) ;
		
		$model->table = $model->tmpTable ;
		unset($model->tmpTable) ;
	}

}
?>
