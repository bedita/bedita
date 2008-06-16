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

	var $belongsTo = array(
		'Gallery' =>
			array(
				'className'	=> 'BEObject',
				'conditions'   	=> '',
				'foreignKey'	=> 'gallery_id',
				'dependent'	=> false
			),
		'Question' =>
			array(
				'className'	=> 'BEObject',
				'conditions'   	=> '',
				'foreignKey'	=> 'question_id',
				'dependent'	=> false
			),
	) ;				


	

//	function del($id = null, $cascade = true) {
//		// Preleva l'elenco dei commenti
//		$doc = $this->findById($this->id) ;
//		
//		// Cancella i singoli commento
//		for($i=0 ; $i < count($doc['comments']) ; $i++) {
//			if(!$this->comments->delete($doc['comments'][$i][$this->comments->primaryKey])) {
//				return false ;
//			}
//		}
//		
//		return true ;
//	}

}
?>
