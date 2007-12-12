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
 * 				Esprime  le relazioni degli oggetti con l'ecommerce (todo)	e altri 
 * 				oggetti multimediali principali
*/
class Content extends BEAppModel
{
	var $name 		= 'Content';

	var $belongsTo = array(
	) ;

	var $hasAndBelongsToMany = array(
			'categories' =>
				array(
					'className'				=> 'TypedObjectCategory',
					'joinTable'    			=> 'contents_typed_object_categories',
					'foreignKey'   			=> 'content_id',
					'associationForeignKey'	=> 'typed_object_category_id',
					'unique'				=> true,
					'fields'				=> 'categories.id, categories.label',
				),
		) ;			
	
	function __construct() {
		parent::__construct() ;

	}
	
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
