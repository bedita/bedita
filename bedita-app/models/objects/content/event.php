<?php
/**
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
 * @author 		giangi giangi@qwerg.com, ste ste@channelweb.it
 * 		
*/
class Event extends BeditaContentModel
{
	var $actsAs 	= array(
			'CompactResult' 		=> array('DateItem'),
			'SearchTextSave'		=> array(),
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> 'objects',
	); 	 

	var $hasMany = array(
			'DateItem' =>
				array(
					'className'		=> 'DateItem',
					'foreignKey'	=> 'content_id',
					'dependent'		=> true
				)
		) ;

	function afterSave() {
		
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
			if (!isset($this->data[$this->name][$name]) || !(is_array($this->data[$this->name][$name]) && count($this->data[$this->name][$name]))) 
				continue ;
			
			// Salva le nuove associazioni
			$size = count($this->data[$this->name][$name]) ;
			for ($i=0; $i < $size ; $i++) {
				$modelTmp	 	 = new $assoc['className']() ; 
				$data 			 = &$this->data[$this->name][$name][$i] ;
				$data[$foreignK] = $id ; 
				if(!$modelTmp->save($data)) 
					return false ;
				
				unset($modelTmp);
			}
		}
		
		return true ;
	}

}
?>
