<?php
/**
 * 
 * Update/create searchable text, indexed by mysql fulltext
 */

class SearchTextSaveBehavior extends ModelBehavior {
	var $config = array();
	
	function setup(&$model, $config) {
	}
	
	/**
	 * @param object $model
	 * @return boolean
	 */
	function afterSave($model, $created) {
		if(!isset($model->{$model->primaryKey})) 
		  throw new BeditaException("Missing primary key from {$model}");	
		$searchTextModel = ClassRegistry::init("SearchText");
		$searchTextModel->createSearchText($model);		
	}

}
?>
