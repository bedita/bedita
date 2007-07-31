<?
/**
 * 
 * Estende la classe AppModel.
 * 
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2006
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
 * Ridefinisce la funzione save() per i model che rappresentano gli oggetti.
 * Permette il setp automatico del campo object_type_id.
 * 
*/

class BEAppObjectModel extends BEAppModel {
	
	/**
 	* Sovrascrive e poi chiama la funzione del parent xchŽ deve settare 
 	* ove necessario, il tipo di oggetto da salvare
 	*/
	function save($data = null, $validate = true, $fieldList = array()) {
		$conf = Configure::getInstance() ;		
		
		if(isset($data['Object']) && !isset($data['Object']['object_type_id'])) {
			$data['Object']['object_type_id'] = $conf->objectTypes[strtolower($this->name)] ;
		} else if(!isset($data['object_type_id'])) {
			$data['object_type_id'] = $conf->objectTypes[strtolower($this->name)] ;
		}

		// Se c'e' la chiave primaria vuota la toglie
		if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey])) {
			unset($data[$this->primaryKey]) ;		
		}
		
		return parent::save($data, $validate, $fieldList) ;
	}

	/**
	 * Torna le custom properties di un oggetto come
	 * rappresentate in DB.
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
/*	
	function getCustomProperties ($id = null) {
		$ret = $this->Object->CustomProperties->findAllByObjectId($id) ;
 		
 		for($i=0; $i < count($ret) ; $i++) {
 			$ret[$i] = $ret[$i]["CustomProperty"] ;
 		}
		return $ret ;
	}
*/
}

?>