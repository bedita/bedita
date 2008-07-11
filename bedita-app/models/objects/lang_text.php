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
*/
class LangText extends BEAppModel
{
	var $name = 'LangText';

	var $belongsTo = array(
		'BEObject' =>
			array(
				'className'		=> 'BEObject',
				'fields'		=> 'id',
				'foreignKey'	=> 'id'
			)
	);

	public function langsForObject($object_id) {
		$result = array();
		$langs=$this->find('all',
			array(
				'fields'=>array('lang'),
				'conditions'=>array("LangText.object_id = '$object_id'","LangText.name = 'status'")
			)
		);
		foreach($langs as $k => $v) {
			$result[]=$v['LangText']['lang'];
		}
		return $result;
	}
}
?>