<?php
/**
 * 
 * Dato un oggetto, crea l'array delle proprietˆ da inserire nella tabella index.
 * Con config sono passati i models (array) da cui prelevare le proprietˆ.
 * Vengono inseriti:
 * integer, boolean, float, string
 * 
 * giangi@qwerg.com
 *
 */

class CreateIndexFieldsBehavior extends ModelBehavior {
	var $config = array();
	
	function setup(&$model, $config = array()) {
		$this->config[$model->name] = $config ;
	}

	
	/**
	 * @param object $model
	 * @return boolean
	 */
	function afterSave(&$model) {
		if(!isset($model->{$model->primaryKey})) return false ;	
		
		// preleva l'oggetto appena creato/salvato
		$bviorHideFields 		= $model->bviorHideFields ;
		$bviorCompactResults 	= $model->bviorCompactResults ;
		$model->bviorHideFields 	= am(array('Index', 'ObjectType', 'Version', 'Permission'), $this->config[$model->name]) ;
		$model->bviorCompactResults = true ;

		if(!($data = $model->findById($model->{$model->primaryKey}))) return false ;
		
		$model->bviorHideFields 		= $bviorHideFields ;
		$model->bviorCompactResults 	= $bviorCompactResults ;
		
		// Indicizza i campi
		$Index = array() ;
		foreach ($data as $k => $v) {
			if(is_array($v)) {
				switch(strtolower($k)) {
					case 'customproperties': 	$this->InspectionCustomProperties($data, $v, $Index) ; break ;
					case 'langtext': 			$this->InspectionLangText($data, $v, $Index) ; break ;
				}
				continue ;
			} else if(is_object($v) || is_resource($v) || $k == 'id' || is_null($v)) continue ;
			
			$this->_value2array($k, $v, $data['id'], $data['lang'], $Index) ;
		}
		
		// Salva l'indicizzazione creata
		if(!isset($model->BEObject->Index)) return true ;
		
		$size = count($Index) ;
		for ($i=0; $i < $size ; $i++) {
			if(!$model->BEObject->Index->save($Index[$i])) return false ;
			$model->BEObject->Index->{$model->BEObject->Index->primaryKey} = false ;
		}
			
		
		return true ;	
	}

	private function InspectionCustomProperties(&$data, &$value, &$Index) {
		foreach ($value as $k => $v) {
			if(is_array($v) || is_object($v) || is_resource($v) || $k == 'id' || is_null($v)) continue ;
			
			$this->_value2array($k, $v, $data['id'], $data['lang'], $Index) ;
		}
	}

	private function InspectionLangText(&$data, &$value, &$Index) {
		for($i=0 ; $i < count($value) ; $i++) {
			$v = ((isset($value[$i]['text']))?$value[$i]['text']:$value[$i]['long_text']) ;
			$this->_value2array($value[$i]['name'], $v, $value[$i]['object_id'], $value[$i]['lang'], $Index) ;
		}
	}

	private function _value2array($name, &$val, $object_id, $lang, &$Index) {
		$type = null ; 
		switch(gettype($val)) {
			case "integer" : 	{ $type = "integer" ; } break ;
			case "boolean" : 	{ $type = "bool" ; } break ;
			case "double" : 	{ $type = "float" ; } break ;
			case "string" :		{ $type = "string" ; } break ;
					
			default: {
				$type = "stream" ;
				$val = serialize($val) ;
 			}
		}
		$Index[] = array(
//			'Index' => array(
				'object_id'	=> $object_id,
				'lang'		=> $lang, 
				'name'		=> $name,
				'type'		=> $type,
				$type		=> $val
//			)
		) ;
		
	}
	
}
?>
