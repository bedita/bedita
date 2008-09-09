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
		return $this->updateHasManyAssoc();
	}

}
?>
