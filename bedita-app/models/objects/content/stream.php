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
 * 				Classe base per oggetti su file
 * 
*/
class Stream extends BEAppModel
{
	var $name = 'Stream';
	var $validate = array(
		'path' 		=> array(array('rule' => VALID_NOT_EMPTY, 	'required' => true)),
		'name' 		=> array(array('rule' => VALID_NOT_EMPTY, 	'required' => true)),
		'type' 		=> array(array('rule' => VALID_NOT_EMPTY, 	'required' => true)),
//		'size' 		=> array(array('rule' => VALID_NUMBER, 		'required' => true)),
	) ;

	/**
	 * Get id from filename
	 * @param string $filename
	 */
	function getIdFromFilename($filename) {
		if(!isset($filename)) return false ;
		$rec = $this->recursive ;
		$this->recursive = -1 ;
		if(!($ret = $this->findByName($filename))) return false ;
		$this->recursive = $rec ;
		if(!isset($ret['Stream']['id'])) return false ;
		return $ret['Stream']['id'] ;
	}
	
	/**
	 * search filename and title in streams
	 *
	 * @param string $text, string to search
	 * @param array $ot stream object type to search
	 * @return unknown
	 */
	public function search($text, $ot, $excluded_ids=array()) {
		$streams = array(); 
		$this->bindModel( array('hasOne' => array('BEObject' => array(
																	'className'		=> 'BEObject',
																	'conditions'   => '',
																	'foreignKey'	=> 'id',
																	'dependent'		=> true
																)
														) ) );
		 $findedStreams = $this->find("all", array(
		 								"restrict" => array("BEObject" => "ObjectType"),
										"conditions" => array(
														"title LIKE '%" .$text. "%'", 
														"object_type_id" => $ot,
		 												"NOT" => array("BEObject.id" => $excluded_ids)			
		 												)
												)
										)  ; 
		if (!empty($findedStreams)) {
			foreach ($findedStreams as $stream) {
				$stream["Stream"]["filename"] = substr($stream["Stream"]["path"],strripos($stream["Stream"]["path"],"/")+1);
				$streams[] = array_merge($stream["Stream"], $stream["BEObject"]);
			}
		}
		return $streams;
	}
}
?>