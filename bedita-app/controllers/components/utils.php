<?php
/**
 * Created on 18/apr/07
 *
 * Parameters:
 * 
 * Author: Alberto Pagliarini
 * 
 */
 
 class UtilsComponent extends Object {
 	
 	function startup(&$controller) {
		
		$this->controller 	= $controller;
		
	}
 	
 	/**
 	 * collpase an array:
 	 *  
 	 * this method works on one level of array
 	 * 
 	 * @param array record
 	 * @param string keyFilter key used to collapse. If not specified all array is collapsed
 	 */
	function collapse($record=array(), $keyFilter=null) {
		
		$res = array();
		
		for ($i =0; $i < count($record); $i++) {
			
			$tmp = array();
			$subrecord = $record[$i] ;
			
			foreach ($subrecord as $key => $val) {
				if(is_array($val)) {
					if ($key == $keyFilter || $keyFilter === null) $tmp = array_merge($tmp, $val) ;
					else $tmp = array_merge($tmp, array($key=>$val)) ;	
				}
				else $tmp[$key] = $val ;
			}
			
			$res[$i] = $tmp;
		}
		
		return $res ;
	}
 }
?>
