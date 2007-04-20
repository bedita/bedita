<?php
/**
 * Created on 05/apr/07
 *
 * Parameters:
 * 
 * Author: Alberto Pagliarini
 * 
 */
 
 class ParamsHelper extends Helper
 {
 	
 	var $paginationParams = array("page","limit","order","sort","direction");
 	
 	function filterPaginatorParams() {
 		
 		$paramsFiltered = array();
 		//pr($this->params["pass"]);
 		if (!empty($this->params["pass"])) {
	 		foreach ($this->params["pass"] as $key => $param) {
	 			if (!in_array($key, $this->paginationParams, true)) $paramsFiltered[$key] = $param;
	  		}
 		}
 		
 		return $paramsFiltered; 
 		
 	}
 	
 }
?>
