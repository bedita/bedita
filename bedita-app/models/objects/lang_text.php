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
 * @license
 * @author 		giangi giangi@qwerg.com	 ste ste@channelweb.it		
*/
class LangText extends BEAppModel
{
	var $belongsTo = array(
		'BEObject' =>
			array(
				'fields'	=> 'id, status',
				'foreignKey'	=> 'object_id'
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
	
	function findObjs($filter = null, $order = null, $dir  = true, $page = 1, $dim = 100000, $excludeIds=array()) {

		$conditions = array("LangText.name = 'status'");
		if( !empty($filter['lang']) && ($filter['lang']!=null) ) {
			$conditions[]="LangText.lang = '" . $filter['lang'] . "'";
		}
		if( !empty($filter['status']) && ($filter['status']!=null) ) {
			$conditions[]="LangText.text = '" . $filter['status'] . "'";
		}
		if( !empty($filter['obj_id'])  && ($filter['obj_id']!=null) ) {
			$conditions[]="LangText.object_id = '" . $filter['obj_id'] . "'";
		}
		$limit = $this->_getLimitClausole($page, $dim);
		$objects_status=$this->find('all',
			array(
				'fields'=>array('id','object_id','name','text','long_text','lang'),
				'conditions'=>$conditions,
				'limit'=>$limit,
				'order'=>$order
			)
		);
		$res=$this->find('all',
			array(
				'fields'=>array('id','object_id','name','text','long_text','lang'),
				'conditions'=>array("LangText.name = 'title'")
			)
		);
		$objects = array();
		$objects_title = array();
		foreach($res as $k => $v) {
			$objects_title[$v['LangText']['object_id']][$v['LangText']['lang']] = $v['LangText']['text'];
			$this->BEObject->restrict(array("BEObject" => array()));
			if(!($obj = $this->BEObject->findById($v['LangText']['object_id']))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
			}
			$objects[$v['LangText']['object_id']][$v['LangText']['lang']] = $obj;
		}

		$recordset = array(
			"objects_status"		=> $objects_status,
			"objects_title"			=> $objects_title,
			"objects_translated"	=> $objects,
			"toolbar"				=> $this->_toolbar($page, $dim)
		) ;

		return $recordset ;
	}

	private function _getLimitClausole($page = 1, $dim = 100000) {
		$offset = ($page > 1) ? (($page -1) * $dim) : null;
		return isset($offset) ? "$offset, $dim" : "$dim" ;
	}
	
	private function _toolbar($page = null, $dimPage = null) {

		$size = $this->find('count',
			array(
				'conditions'=>array("LangText.name = 'status'")
			)
		);

		$toolbar = array("first" => 0, "prev" => 0, "next" => 0, "last" => 0, "size" => 0, "pages" => 0, "page" => 0, "dim" => 0) ;
		
		if(!$page || empty($page)) $page = 1 ;
		if(!$dimPage || empty($dimPage)) $dimPage = $size ;
		
		$pageCount = $size / $dimPage ;
		settype($pageCount,"integer");
		if($size % $dimPage) $pageCount++ ;
		
		$toolbar["pages"] 	= $pageCount ;
		$toolbar["page"]  	= $page ;
		$toolbar["dim"]  	= $dimPage ;
		
		if($page == 1) {
			if($page < $pageCount) {
				$toolbar["next"] = $page+1 ;
				$toolbar["last"] = $pageCount ;
			} 
		} else {
			if($page >= $pageCount) {
				// Last
				$toolbar["first"] = 1 ;
				$toolbar["prev"] = $page-1 ;
			} else {
				// Middle
				$toolbar["next"] = $page+1 ;
				$toolbar["last"] = $pageCount ;
				$toolbar["first"] = 1 ;
				$toolbar["prev"] = $page-1 ;
			}
		}

		$toolbar["start"]	= (($page-1)*$dimPage)+1 ;
		$toolbar["end"] 	= $page * $dimPage ;
		if($toolbar["end"] > $size) $toolbar["end"] = $size ;

		$toolbar["size"] = $size ;
		return $toolbar ;
	}
}
?>