<?php
/**
 * Compatta il risultato di un find o findAll di un dato model.
 * nella fase di setup devono essere passate le chiavi dell'array associativo
 * da escludere dal merge ed inserire come sono.
 * 
 * * per disattivare settare la variabile membro del model:
 * bviorCompactResults = false.		Default: true
 * giang@qwerg.com
 *
 */

class CompactResultBehavior extends ModelBehavior {
	var $config = array();
		
	/**
	 * Compatta il risultato per default
	 *
	 */
	var $bviorCompactResults = true ;
	
	/**
	 * Elimina dal risultato di ritorno i campi indicati
	 * Ricorsivamente.
	 * 
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
		// If switch has been disabled then cancel
  		if ($model->bviorCompactResults) $this->_compactStart($model, $results);

  		// toglie i campi richiesti
		$this->_removeFields($model, $results) ;
  		
		return $results ;	
  }

  
  	/**
  	 * Toglie dal risultato in campi richiesti
  	 *
  	 * @param unknown_type $model
  	 * @param unknown_type $results
  	 */
	private function _removeFields(&$model, &$results) {
		if(empty($model->bviorHideFields) || empty($results)) return ;
		
		// toglie le proprieta'
		for($i=0; $i < count($model->bviorHideFields) ; $i++) {
			if(array_key_exists($model->bviorHideFields[$i], $results)) {
				unset($results[$model->bviorHideFields[$i]]) ;
			}
		} 
		
		// verifica tra i figli
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
		$excludeKey = &$this->config[$model->name] ;
		$ret = array() ;
		foreach($excludeKey as $key) {
			if(!isset($result[$key])) continue ;
			$result[$model->name][$key] = &$result[$key] ;
			unset($result[$key]) ;
		}
		
		$result = $model->am($result) ;
	}

}
?>
