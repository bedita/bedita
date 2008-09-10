<?php
/**
 * 
 * Update/create searchable text, indexed by mysql
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
	function afterSave($model) {
		if(!isset($model->{$model->primaryKey})) 
		  throw new BeditaException("Missing primary key from {$model}");	
		
		// preleva l'oggetto appena creato/salvato
		$bviorCompactResults 	= $model->bviorCompactResults ;
		$model->bviorCompactResults = true ;
		$mode->contain(array("BEObject"));
		if(!($data = $model->findById($model->{$model->primaryKey}))) 
		  throw new BeditaException("Error loading {$model}");
		$model->bviorCompactResults 	= $bviorCompactResults ;

        if(!isset($model->BEObject->SearchText)) 
          return true ;
        
        $relevance = array("title" => 10 , "description" => 5);
		foreach ($data as $k => $v) {
			if($k === 'title' || $k === 'description') {
                $sText = array(
	                'object_id' => $data['id'],
	                'lang'      => $data['lang'], 
	                'content'   => $v,
	                'relevance' => $relevance[$k]
                );
                if(!$model->BEObject->SearchText->save($sText)) 
                    throw new BeditaException("Error saving search text {$model}: $k => $v");
			}
		}

		return true ;	
	}

}
?>
