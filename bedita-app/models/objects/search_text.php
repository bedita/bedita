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
				'className'		=> 'BEObject',
				'fields'		=> 'id',
				'foreignKey'	=> 'id',
			)
	);

	public function createSearchText($model) {
		
		$bviorCompactResults = null;
		if(isset($model->bviorCompactResults)) {
			$bviorCompactResults = $model->bviorCompactResults ;
		}
		$model->bviorCompactResults = true ;
		$model->restrict(array("BEObject"));
		if(!($data = $model->findById($model->{$model->primaryKey}))) 
		  throw new BeditaException("Error loading {$model->name}");
		$model->bviorCompactResults = $bviorCompactResults ;
		$relevance = array("title" => 10 , "description" => 5);
		foreach ($data as $k => $v) {
			if($k === 'title' || $k === 'description') {
                if (!empty($v)) {
					$sText = array(
		                'object_id' => $data['id'],
		                'lang'      => $data['lang'], 
		                'content'   => $v,
		                'relevance' => $relevance[$k]
	                );
	                
	                $this->create();
	                if(!$this->save($sText)) 
	                    throw new BeditaException("Error saving search text {$model}: $k => $v");
                }
			}
		}

		return true ;
	}

}
?>
