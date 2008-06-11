<?php
/**
 * 
 * Update/create searchable text, indexed by mysql
 */

class SearchTextSaveBehavior extends ModelBehavior {
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
		App::import("Model", "SearchText");
		$searchTextModel = new SearchText();
		$searchTextModel->createSearchText($model);		
	}

}
?>
