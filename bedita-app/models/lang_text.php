<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008, 2010 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Lang text object for translation
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
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

	static $paginationOrder, $paginationDir;

	/**
	 * Return translations of fields for an object
	 * 
	 * @param int $object_id
	 * @return array
	 */
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

	/**
	 * Return object translations performing a search by $filter, including paginating data.
	 * 
	 * @param array $filter
	 * @param string $order
	 * @param boolean $dir
	 * @param int $page
	 * @param int $dim
	 * @param array $excludeIds
	 * @throws BeditaException
	 * @return array
	 */
	public function findObjs($filter = null, $order = null, $dir  = true, $page = 1, $dim = 100000, $excludeIds=array()) {

		$s = $this->getStartQuote();
		$e = $this->getEndQuote();
		
		// build sql conditions
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$beObject = ClassRegistry::init("BEObject");
		$beObjFields = $db->fields($beObject);
		$beObjFields = implode(",", $beObjFields);
		
		$langTextFields = $db->fields($this);
		$langTextFields = implode(",", $langTextFields);
		
		$fields  = "DISTINCT {$beObjFields}, {$langTextFields}" ;
		$from = "{$s}lang_texts{$e} as {$s}LangText{$e} LEFT OUTER JOIN {$s}objects{$e} 
			as {$s}BEObject{$e} ON {$s}LangText{$e}.{$s}object_id{$e}={$s}BEObject{$e}.{$s}id{$e}";
		$conditions = array();
		
		$beObjGroupBy = $this->fieldsString("BEObject");
		$langTextGroupBy = $this->fieldsString("LangText");
		$groupClausole = "GROUP BY {$beObjGroupBy}, {$langTextGroupBy}";
		
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
		if( !empty($filter['object_type_id'])  && ($filter['object_type_id']!=null) ) {
			$conditions[]="BEObject.object_type_id = '" . $filter['object_type_id'] . "'";
		}
		
		if( !empty($order) && $order == "object_type_id")  {
			$fields .= ", {$s}ObjectType.name{$e}";
			$from .= ", {$s}object_types{$e} AS {$s}ObjectType{$e}";
			$conditions[]="ObjectType.id = BEObject.object_type_id";
			$order = "{$s}ObjectType{$e}.{$s}name{$e}";
		}
		if (!empty($order) && strstr($order,"LangText.")) {
			$t = explode(".", $order);
			$langTextOrder = $t[1];
			$order = "{$s}BEObject{$e}.{$s}modified{$e}";
		}

		$otherOrder = "";
		if (array_key_exists("query", $filter)) {
			$fields .= ", SearchText.object_id AS oid, SUM( MATCH (SearchText.content) AGAINST ('".$filter["query"]."') * SearchText.relevance ) AS points";
			$from .= ", search_texts AS SearchText";
			$conditions[] = "SearchText.object_id = BEObject.id AND SearchText.lang = LangText.lang AND MATCH (SearchText.content) AGAINST ('".$filter["query"]."')";
			$otherOrder = "points DESC ";
			unset($filter["query"]);	
		}
		
		$sqlClausole = $db->conditions($conditions, true, true) ;

		$ordClausole = "";
		if(is_string($order) && strlen($order)) {
			$beObject = ClassRegistry::init("BEObject");
			if ($beObject->hasField($order)) {
				$order = "{$s}BEObject{$e}.{$s}" . $order . $e;
			}
			$ordItem = "{$order} " . ((!$dir)? " DESC " : "");
			if(!empty($otherOrder)) {
				$ordClausole = "ORDER BY " . $ordItem .", " . $otherOrder;
			} else {
				$ordClausole = " ORDER BY {$order} " . ((!$dir)? " DESC " : "") ;
			}
		} elseif (!empty($otherOrder)) {
			$ordClausole = "ORDER BY {$otherOrder}";
		}
		
		$limit 	= $this->getLimitClausole($dim, $page) ;
		// #CUSTOM QUERY
		$query = "SELECT {$fields} FROM {$from} {$sqlClausole} {$groupClausole} {$ordClausole} {$limit}";
		$tmp  	= $this->query($query) ;

		if ($tmp === false)
			throw new BeditaException(__("Error finding translations", true));

		$objects = array();
		foreach($tmp as $tr) {
			$object_id = $tr['LangText']['object_id'];
			$lang = $tr['LangText']['lang'];
			$translationTitle = $this->field("text", 
				array("object_id"=>$object_id, "lang"=>$lang, "name"=>"title"));

			$objects[] = array("LangText" => array(
				"id" => $tr['LangText']["id"], "object_id" => $object_id, "lang" => $tr['LangText']["lang"],
				"status" => $tr['LangText']["text"], "title" => $translationTitle), 
				"BEObject" => $tr['BEObject']);
		}

		// #CUSTOM QUERY
		$queryCount = "SELECT COUNT({$s}BEObject{$e}.{$s}id{$e}) AS count FROM {$from} {$sqlClausole}";
		$tmpCount = $this->query($queryCount);
		if ($tmpCount === false)
			throw new BeditaException(__("Error counting translations", true));
	
		$size = (empty($tmpCount[0][0]["count"]))? 0 : $tmpCount[0][0]["count"];
		
		// reorder by LangText fields
		if (!empty($langTextOrder)) {
			LangText::$paginationOrder = $langTextOrder;
			LangText::$paginationDir = $dir;
			usort($objects, array('LangText', 'reorderPagination'));
		}
		
		$recordset = array(
			"translations"	=> $objects,
			"toolbar"		=> $this->toolbar($page, $dim, $size)
		) ;

		return $recordset ;
	}
	
	/**
	 * compare two array elements defined by $paginationOrder var and return -1,0,1 
	 * $paginationDir is used for define LangText order of comparison
	 * if LangText comparison item are equals, order by BEObject.modified DESC
	 * 
	 * @param array $e1
	 * @param array $e2
	 * @return int (-1,0,1)
	 */
	private static function reorderPagination($e1, $e2) {
		$d1 = $e1["LangText"][LangText::$paginationOrder];
		$d2 = $e2["LangText"][LangText::$paginationOrder];
		if ($d1 != $d2) {
			return (LangText::$paginationDir)? strcmp($d1,$d2) : strcmp($d2,$d1);
		}
		return strcmp($e2["BEObject"]["modified"],$e1["BEObject"]["modified"]);
	}

}
?>