<?php
/**
 * 
 * @author  ste@channelweb.it           
*/
class SearchText extends BEAppModel
{
	var $belongsTo = array(
		'BEObject' =>
			array(
				'fields'		=> 'id',
				'foreignKey'	=> 'object_id'
			)
	);

	public function createSearchText($model) {
		
		$bviorCompactResults = null;
		if(isset($model->bviorCompactResults)) {
			$bviorCompactResults = $model->bviorCompactResults ;
		}
		$model->bviorCompactResults = true ;
		$model->containLevel("default");
		if(!($data = $model->findById($model->{$model->primaryKey}))) 
		  throw new BeditaException("Error loading {$model->name}");
		$model->bviorCompactResults = $bviorCompactResults ;
		
		$searchFields = array();
		$conf = Configure::getInstance();
		if(isset( $conf->searchFields[$model->name])) {
			$searchFields = $conf->searchFields[$model->name];
		} elseif($model->searchFields != null) {
			$searchFields = $model->searchFields;
		}

		if(!empty($searchFields)) {
			$indexFields = array_keys($searchFields);
			foreach ($data as $k => $v) {
				if(in_array($k, $indexFields)) {
	                if (!empty($v)) {
						$sText = array(
			                'object_id' => $data['id'],
			                'lang'      => $data['lang'], 
			                'content'   => $v,
			                'relevance' => $searchFields[$k]
		                );
	
		                $this->create();
		                if(!$this->save($sText)) 
		                    throw new BeditaException("Error saving search text {$model}: $k => $v");
	                }
				}
			}
		}
		return true ;
	}

}
?>
