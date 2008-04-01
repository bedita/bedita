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
 * 				Esprime  le relazioni tra oggetti di tipo contenuto:
 * 				oggetti associati per lingua, obj immagini, multimedia, e allegati		
*/
class ContentBase extends BEAppModel
{
	var $name = 'ContentBase';

	var $hasAndBelongsToMany = array(
			'ObjectRelation' =>
				array(
					'className'				=> 'BEObject',
					'joinTable'    			=> 'content_bases_objects',
					'foreignKey'   			=> 'id',
					'associationForeignKey'	=> 'object_id',
					'unique'				=> true,
					'order'					=> 'priority'
				),
			'ObjectCategory' =>
				array(
					'className'				=> 'ObjectCategory',
					'joinTable'    			=> 'content_bases_object_categories',
					'foreignKey'   			=> 'content_base_id',
					'associationForeignKey'	=> 'object_category_id',
					'unique'				=> true
				)
		) ;			

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
		if (!empty($this->data['ObjectRelation']['ObjectRelation'])) {
			$this->tempData = array() ;
			$this->tempData['ObjectRelation'] = &$this->data['ObjectRelation']['ObjectRelation'] ;
			unset($this->data['ObjectRelation']['ObjectRelation']) ;
		}
		return true ;
	}
	
	function afterSave() {
		if (!empty($this->tempData)) {
			$db 		= &ConnectionManager::getDataSource($this->useDbConfig);
			$queriesDelete 	= array() ;
			$queriesInsert = array() ;
			
			foreach ($this->tempData as $values) {
				$assoc 	= $this->hasAndBelongsToMany['ObjectRelation'] ;
				$table 	= $db->name($db->fullTableName($assoc['joinTable']));
				$fields = $assoc['foreignKey'] .",".$assoc['associationForeignKey'].", switch, priority"  ;
				
				for($i=0; $i < count($values); $i++) {
					$obj_id		= $values[$i]['id'] ;
					$switch		= $values[$i]['switch'] ;
					$priority	= isset($values[$i]['priority']) ? "'{$values[$i]['priority']}'" : 'NULL' ;
					
					// Delete old associations
					$queriesDelete[$switch] = "DELETE FROM {$table} WHERE {$assoc['foreignKey']} = '{$this->id}' AND switch = '{$switch}' ";
					$queriesInsert[] = "INSERT INTO {$table} ({$fields}) VALUES ({$this->id}, {$obj_id}, '{$switch}', {$priority})" ;
				}
			}
			
			foreach ($queriesDelete as $qDel) {
				$db->query($qDel);
			}
			foreach ($queriesInsert as $qIns) {
				$db->query($qIns);
			}
			unset($this->tempData);
		}
		return true ;
	}
	
	/**
	 * Definisce i valori di default.
	 */		
	function beforeValidate() {
		if(isset($this->data[$this->name])) $data = &$this->data[$this->name] ;
		else $data = &$this->data ;
		
	 	$default = array(
			'start' 			=> array('getDefaultDateFormat', (isset($data['start']) && !empty($data['start']))?$data['start']:time()),
			'end'	 		=> array('getDefaultDateFormat', ((isset($data['end']) && !empty($data['end']))?$data['end']:null)),
			'type' 		=> array('getDefaultTextFormat', (isset($data['type']))?$data['type']:null),
		) ;
		
		foreach ($default as $name => $rule) {
			if(!is_array($rule) || !count($rule)) {
				$data[$name] = $rule ;
				continue ;
			}
			
			$method = $rule[0];
			unset($rule[0]);
			
			if (method_exists($this, $method)) {
				$data[$name] = call_user_func_array(array(&$this, $method), $rule);
			} 
		}

		return true ;
	}
	
}
?>
