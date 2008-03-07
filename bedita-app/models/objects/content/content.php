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
 * @author 		giangi giangi@qwerg.com	
 * 		
 */
class Content extends BEAppModel
{
	var $hasAndBelongsToMany = array(
			'ObjectCategory' =>
				array(
					'className'				=> 'ObjectCategory',
					'joinTable'    			=> 'contents_object_categories',
					'foreignKey'   			=> 'content_id',
					'associationForeignKey'	=> 'object_category_id',
					'unique'				=> true,
				),
		) ;			
	
	function beforeSave() {
		foreach ($this->hasAndBelongsToMany as $k => $assoc) {
			$model = new $assoc['className']() ;
			
			if(!isset($this->data[$k][$k]) || !is_array($this->data[$k][$k])) continue ;
			
			for($i=0; $i < count($this->data[$k][$k]); $i++) {
				$this->data[$k][$k][$i] = $this->data[$k][$k][$i][$model->primaryKey] ;
			}
		}

		return true ;
	}
}
?>
