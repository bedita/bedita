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
		$limit = $this->getLimitClausole($page, $dim);
		$objects_status=$this->find('all',
			array(
				'fields'=>array('id','object_id','name','text','long_text','lang'),
				'conditions'=>$conditions,
				'limit'=>$limit,
				'order'=>$order
			)
		);

		$objects = array();
		$objects_title = array();
		foreach($objects_status as $os) {
			$object_id = $os['LangText']['object_id'];
			$lang = $os['LangText']['lang'];
			$objects_title[$object_id][$lang] = $this->field("text", 
				array("object_id"=>$object_id, "lang"=>$lang, "name"=>"title"));
			$this->BEObject->restrict(array("BEObject" => array()));
			if(!($obj = $this->BEObject->findById($object_id))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $object_id));
			}
			$objects[$object_id][$lang] = $obj;
		}

		$recordset = array(
			"objects_status"		=> $objects_status,
			"objects_title"			=> $objects_title,
			"objects_translated"	=> $objects,
			"toolbar"				=> $this->toolbar($page, $dim, $conditions)
		) ;

		return $recordset ;
	}

}
?>