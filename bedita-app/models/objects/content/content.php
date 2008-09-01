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
 * 		BEdita content, base class for all 'content types'
 */
class Content extends BEAppModel
{
	var $hasAndBelongsToMany = array(
			'ObjectRelation' =>
				array(
					'className'				=> 'BEObject',
					'joinTable'    			=> 'content_objects',
					'foreignKey'   			=> 'id',
					'associationForeignKey'	=> 'object_id',
					'unique'				=> true,
					'order'					=> 'priority'
				),
			'ObjectCategory' =>
				array(
					'className'				=> 'ObjectCategory',
					'joinTable'    			=> 'content_object_categories',
					'foreignKey'   			=> 'content_id',
					'associationForeignKey'	=> 'object_category_id',
					'unique'				=> true
				)
		) ;			

	/**
	 * afterFind: divide tags from categories
	 *
	 * @param array $result
	 * @return array $result
	 */
	function afterFind($result) {
		if (!empty($result["ObjectCategory"])) {
			
			$tag = array();
			$category = array();
			
			foreach ($result["ObjectCategory"] as $ot) {
				if (!empty($ot["object_type_id"])) {
					$category[] = $ot;
				} else {
					$tag[] = $ot;
				}
			}
			
			$result["Category"] = $category;
			$result["Tag"] = $tag;
		}
		
		return $result;
	}
		
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
			$lang			= (isset($this->data['Content']['lang'])) ? $this->data['Content']['lang']: null ;
			
			// set one-way relation
			$oneWayRelation = array_merge( Configure::read("defaultOneWayRelation"), Configure::read("cfgOneWayRelation") );
			
			$assoc 	= $this->hasAndBelongsToMany['ObjectRelation'] ;
			$table 	= $db->name($db->fullTableName($assoc['joinTable']));
			$fields = $assoc['foreignKey'] .",".$assoc['associationForeignKey'].", switch, priority"  ;

			foreach ($this->data['ObjectRelation']['ObjectRelation'] as $switch => $values) {
				
				foreach($values as $key => $val) {
					$obj_id		= isset($val['id'])? $val['id'] : false;
					$priority	= isset($val['priority']) ? "'{$val['priority']}'" : 'NULL' ;
					
					// Delete old associations
					$queriesDelete[$switch] = "DELETE FROM {$table} 
											   WHERE ({$assoc['foreignKey']} = '{$this->id}' OR {$assoc['associationForeignKey']} = '{$this->id}')  
											   AND switch = '{$switch}' ";
					if (!empty($obj_id)) {
						$queriesInsert[] = "INSERT INTO {$table} ({$fields}) VALUES ({$this->id}, {$obj_id}, '{$switch}', {$priority})" ;
						
						if(!in_array($switch,$oneWayRelation)) {
							// find priority of inverse relation
							$inverseRel = $this->query("SELECT priority 
														  FROM {$table} 
														  WHERE id={$obj_id} 
														  AND object_id={$this->id} 
														  AND switch='{$switch}'");
							
							if (empty($inverseRel[0]["content_objects"]["priority"])) {
								$inverseRel = $this->query("SELECT MAX(priority)+1 AS priority FROM {$table} WHERE id={$obj_id} AND switch='{$switch}'");
								$inversePriority = (empty($inverseRel[0][0]["priority"]))? 1 : $inverseRel[0][0]["priority"];
							} else {
								$inversePriority = $inverseRel[0]["content_objects"]["priority"];
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
							
							// save lang text							
							$this->saveLangTextObjectRelation($obj_id, $lang, $title, "title") ;
							$this->saveLangTextObjectRelation($obj_id, $lang, $description, "description") ;
						}
					}
				}
			}
			
			foreach ($queriesDelete as $qDel) {
				if ($db->query($qDel) === false)
					throw new BeditaException(__("Error deleting associations", true), $qDel);
			}
			foreach ($queriesInsert as $qIns) {
				if ($db->query($qIns)  === false)
					throw new BeditaException(__("Error inserting associations", true), $qIns);
			}
			foreach ($queriesModified as $qMod) {
				if ($db->query($qMod)  === false)
					throw new BeditaException(__("Error modifing title and description", true), $qMod);
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
	
	/**
	 * save lang text
	 */
	public function saveLangTextObjectRelation($obj_id, $lang, $value, $field) {
		if(!isset($lang)) return false ;
		
		$id = $this->ObjectRelation->LangText->field("id", "object_id = {$obj_id} AND name= '{$field}' ");	
		if(@empty($value)) {;			
			if($id) $this->ObjectRelation->LangText->delete($id) ;
			
			return false ;
		}
		
		$data = array(
			"object_id"	=> $obj_id,
			"lang"		=> $lang,
			"name"		=> $field,
			"text"		=> $value
		) ;
		if($id) $data["id"] = $id;
		
		$this->ObjectRelation->LangText->id = false ;
		
		return $this->ObjectRelation->LangText->save($data) ;
	}
	
}
?>
