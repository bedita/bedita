<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

/**
 * Compact find result
 * 
 * in model var actAs = array("CompactResult" => array('model to esclude'));
 *
 */
class CompactResultBehavior extends ModelBehavior {
	var $config = array();
		
	/**
	 * bviorCompactResults, default: true 
	 * Set to false if you want data formatted in classic CakePHP way
	 */
	var $bviorCompactResults = true ;
	
	/**
	 * Unset fields from data result recursively
	 */
	var $bviorHideFields = array() ;
	
	
	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ;

		if (!isset($model->bviorCompactResults)) {
			$model->bviorCompactResults = $this->bviorCompactResults ;
		}
		
		if (!isset($model->bviorHideFields)) {
			$model->bviorHideFields = $this->bviorHideFields ;
		}
	}

	function afterFind(&$model, $results) {

		if (isset($model->bviorCompactResults) && $model->bviorCompactResults === true)
  		    $this->_compactStart($model, $results);

  		// remove fields specified
		$this->_removeFields($model, $results) ;
  		
		return $results ;	
	}

  
  	/**
  	 * Remove from result fields specified
  	 *
  	 * @param unknown_type $model
  	 * @param unknown_type $results
  	 */
	private function _removeFields(&$model, &$results) {
		if(empty($model->bviorHideFields) || empty($results)) return ;
		
		// remove field
		for($i=0; $i < count($model->bviorHideFields) ; $i++) {
			if(array_key_exists($model->bviorHideFields[$i], $results)) {
				unset($results[$model->bviorHideFields[$i]]) ;
			}
		} 
		
		// verify among children
		foreach ($results as $k => $v) {
			if(!is_array($results[$k])) continue ;
			
			$this->_removeFields($model, $results[$k]) ;
		}
	}

			
  /**
   * Check if an array is numerically indexed in a standard manner.
   * [0..(n-1)], with no other keys
   *
   * @param  array  $array Array to check
   * @return boolean
   */
 	private function _isNumericArray($array) {
		if (!is_array($array)) {
 			return null;
		}
		return (array_sum(array_keys($array)) === (sizeof($array) * (sizeof($array)-1))>>1) ;
	}


	private function _compactStart(&$model, &$results) {
  		
		// Skip empty arrays
		if (empty($results)) return;

		if($this->_isNumericArray($results)) {
			for($i=0; $i < count($results); $i++) {

				if (!isset($results[$i][$model->name])) continue ;
				$this->_compact($results[$i], $model) ;
			}
		} else {
			if (!isset($results[$model->name])) return ;
			$this->_compact($results, $model) ;
		}
  }

    private function _compact(&$result, &$model) {
        $result = $model->am($result, $this->config[$model->name]);  // After improvements to BEAppModel::am() brought by #639.
    }
}
