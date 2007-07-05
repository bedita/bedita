<?php
/**
 *
 * PHP versions 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2007
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
 * @author 		giangi giangi@qwerg.com	
 * 		
 * 				Esprime  le relazioni tra oggetti di tipo contenuto 		
*/
class BaseDocument extends BEAppModel
{
	var $name 		= 'BaseDocument';
	var $recursive 	= 2 ;

	var $hasMany = array(
		'links' =>
				array(
					'className'		=> 'Link',
					'foreignKey'	=> 'object_id',
//					'fields'		=> 'id, start, end',
					'dependent'		=> true
				),
		) ;	

	var $hasAndBelongsToMany = array(
			'comments' =>
				array(
					'className'				=> 'BEObject',
					'joinTable'    			=> 'content_bases_objects',
					'foreignKey'   			=> 'id',
					'associationForeignKey'	=> 'object_id',
					'unique'				=> true,
					'fields'				=> 'comments.id, comments.status',
					'conditions'			=> "content_bases_objects.switch ='COMMENTS'",
					'switch'				=> "COMMENTS",
				),
		) ;			

	function __construct() {
		parent::__construct() ;
	}

	/**
	 * Le associazioni di tipo HABTM di questo modello non possono essere
	 * salvate con i metodi di cakePHP (la tabella di unione utilizza 
	 * 3 capi: id, object_id e swtich che determina il tipo di unione).
	 * 
	 * I dati delle associazioni vengono temporaneamente rimossi e poi 
	 * salvati dopo il modello (in $this->afterSave())
	 * 
	 */
	function beforeSave() {
		$this->tempData = array() ;
		 
		foreach ($this->hasAndBelongsToMany as $k => $assoc) {
			if(!isset($this->data[$k][$k])) continue ;
			
			$this->tempData[$k] = &$this->data[$k][$k] ;
			unset($this->data[$k][$k]) ;
		}
		
		return true ;
	}
	
	function afterSave() {
		$db 		= &ConnectionManager::getDataSource($this->useDbConfig);
		$queries 	= array() ;
		
		// Gestisce i commenti
		$assoc 	= $this->hasAndBelongsToMany['comments'] ;
		$table 	= $db->name($db->fullTableName($assoc['joinTable']));
		$fields = $assoc['foreignKey'] .",".$assoc['associationForeignKey'].", switch"  ;
			
		for($i=0; isset($this->tempData['comments']) && $i < count($this->tempData['comments']); $i++) {
			$id 	= $this->id ;
			$obj_id	= $this->tempData['comments'][$i]['id'] ;
			$switch	= $assoc['switch'] ;
				
			// Cancella la precedente associazione con questo commento
			$queries[] = "DELETE FROM {$table} WHERE {$assoc['foreignKey']} = '{$this->id}' AND {$assoc['conditions']} " ;
				
			// Se non e' richiesta la cancellazione reinserisce il commento
			if(!isset($this->tempData['comments'][$i]['delete'])) {
				$queries[] = "INSERT INTO {$table} ({$fields}) VALUES ({$id}, {$obj_id}, '{$switch}')" ;
			}
		}
		
		// Esegue le query
		for($i=0; $i < count($queries); $i++) {
			$db->query($queries[$i]);
		}

		unset($this->tempData);
		
		// Scorre le associazioni hasMany
		foreach ($this->hasMany as $name => $assoc) {
			$db 		=& ConnectionManager::getDataSource($this->useDbConfig);
			$model 		= new $assoc['className']() ; 
			
			// Cancella le precedenti associazioni
			$table 		= (isset($model->useTable)) ? $model->useTable : ($db->name($db->fullTableName($assoc->className))) ;
			$id 		= (isset($this->data[$this->name]['id'])) ? $this->data[$this->name]['id'] : $this->getInsertID() ;		
			$foreignK	= $assoc['foreignKey'] ;
			
			$db->query("DELETE FROM {$table} WHERE {$foreignK} = '{$id}'");
			
			// Se non ci sono dati da salvare esce
			if(!isset($this->data[$this->name][$name])) continue ;
			if (!(is_array($this->data[$this->name][$name]) && count($this->data[$this->name][$name]))) continue ;
			
			// Salva le nuove associazioni
			$size = count($this->data[$this->name][$name]) ;
			for ($i=0; $i < $size ; $i++) {
				$modelTmp	 	 = new $assoc['className']() ; 
				$data 			 = &$this->data[$this->name][$name][$i] ;
				$data[$foreignK] = $id ; 
				if(!$modelTmp->save($data)) return false ;
				
				unset($modelTmp);
			}
		}
		
		return true ;
	}

	function del($id = null, $cascade = true) {
		// Preleva l'elenco dei commenti
		$doc = $this->findById($this->id) ;
		
		// Cancella i singoli commento
		for($i=0 ; $i < count($doc['comments']) ; $i++) {
			if(!$this->comments->delete($doc['comments'][$i][$this->comments->primaryKey])) {
				return false ;
			}
		}
		
		return true ;
	}

}
?>
