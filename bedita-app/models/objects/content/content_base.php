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
	 * Le associazioni di tipo HABTM ObjectRelation di questo modello non possono essere
	 * salvate con i metodi di cakePHP (la tabella di unione utilizza 
	 * 3 capi: id, object_id e swtich che determina il tipo di unione).
	 * 
	 * I dati delle associazioni vengono temporaneamente rimossi e poi 
	 * salvati dopo il modello (in $this->afterSave())
	 * 
	 */
	function beforeSave() {
		$this->restoreObjectRelation = $this->hasAndBelongsToMany['ObjectRelation'];
		$this->unbindModel( array('hasAndBelongsToMany' => array('ObjectRelation')) );
		return true ;
	}
	
	function afterSave() {
		if (!empty($this->data['ObjectRelation'])) {
			$this->bindModel( array(
				'hasAndBelongsToMany' => array(
						'ObjectRelation' => $this->restoreObjectRelation
							)
				) 
			);
			$db 		= &ConnectionManager::getDataSource($this->useDbConfig);
			$queriesDelete 	= array() ;
			$queriesInsert 	= array() ;
			$queriesModified 	= array() ;
			
			foreach ($this->data['ObjectRelation'] as $values) {
				$assoc 	= $this->hasAndBelongsToMany['ObjectRelation'] ;
				$table 	= $db->name($db->fullTableName($assoc['joinTable']));
				$fields = $assoc['foreignKey'] .",".$assoc['associationForeignKey'].", switch, priority"  ;	
				foreach($values as $val) {
					$obj_id		= isset($val['id'])? $val['id'] : false;
					$switch		= $val['switch'] ;
					$priority	= isset($val['priority']) ? "'{$val['priority']}'" : 'NULL' ;
					
					// Delete old associations
					$queriesDelete[$switch] = "DELETE FROM {$table} 
											   WHERE ({$assoc['foreignKey']} = '{$this->id}' OR {$assoc['associationForeignKey']} = '{$this->id}')  
											   AND switch = '{$switch}' ";
					if (!empty($obj_id)) {
						$queriesInsert[] = "INSERT INTO {$table} ({$fields}) VALUES ({$this->id}, {$obj_id}, '{$switch}', {$priority})" ;
						
						if($switch != "link") {	
							// find priority of inverse relation
							$inverseRel = $this->query("SELECT priority 
														  FROM {$table} 
														  WHERE id={$obj_id} 
														  AND object_id={$this->id} 
														  AND switch='{$switch}'");
							
							if (empty($inverseRel[0]["content_bases_objects"]["priority"])) {
								$inverseRel = $this->query("SELECT MAX(priority)+1 AS priority FROM {$table} WHERE id={$obj_id} AND switch='{$switch}'");
								$inversePriority = (empty($inverseRel[0][0]["priority"]))? 1 : $inverseRel[0][0]["priority"];
							} else {
								$inversePriority = $inverseRel[0]["content_bases_objects"]["priority"];
							}						
							$queriesInsert[] = "INSERT INTO {$table} ({$fields}) VALUES ({$obj_id}, {$this->id}, '{$switch}', ". $inversePriority  .")" ;
						}
						
						/**
						 * Proposta x salvare le modifiche a title e description di oggetto relazionato se ci sono i dati sufficenti. (giangi) 
						 */
						$modified = (isset($val['modified']))? ((boolean)$val['modified']) : false;
						if($modified && $obj_id) {
							$title 		= isset($val['title']) ? addslashes($val['title']) : "" ;
							$description 	= isset($val['description']) ? addslashes($val['description']) : "" ;
							
							$queriesModified[] = "UPDATE objects  SET title = '{$title}', description = '{$description}' WHERE id = {$obj_id} " ;
						}
					}
				}
			}
			foreach ($queriesDelete as $qDel) {
				$db->query($qDel);
			}
			foreach ($queriesInsert as $qIns) {
				$db->query($qIns);
			}
			foreach ($queriesModified as $qIns) {
				$db->query($qIns);
			}
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
