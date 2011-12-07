<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Search text object
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
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
		
		$searchFields = $this->getSearchFields($model);
		$this->saveSearchTexts($searchFields, $data);
		return true ;
	}
	
	private function getSearchFields(BEAppModel $model) {
		$searchFields = array();
		$conf = Configure::getInstance();
		if(isset( $conf->searchFields[$model->name])) {
			$searchFields = $conf->searchFields[$model->name];
		} elseif($model->searchFields != null) {
			$searchFields = $model->searchFields;
		}
		return $searchFields;
	}
	
	public function saveLangTexts(array &$dataLangText) {
		$objectId = $dataLangText[0]['object_id'];
		$beObject = ClassRegistry::init("BEObject");
		$modelClass = $beObject->getType($objectId);
		$model = ClassRegistry::init($modelClass);
		$searchFields = $this->getSearchFields($model);
		$data = array();
		$data['lang'] = $dataLangText[0]['lang'];
		$data['id'] = $objectId;
		foreach ($dataLangText as $lang) {
			$data[$lang['name']] = $lang['text'];
		}
		// first delete old items
		if(!$this->deleteAll(array("SearchText.object_id" => $objectId, "SearchText.lang" =>$data['lang']), false))
			throw new BeditaException("Error deleting old search text items : " . $objectId . "-". $data['lang']);
		$this->saveSearchTexts($searchFields, $data);
	}
			
	private function saveSearchTexts(array &$searchFields, array &$data) {
		
		if(!empty($searchFields)) {
			$indexFields = array_keys($searchFields);
	        $lang = !empty($data['lang'])? $data["lang"] : Configure::read("defaultLang");
			
			// clean search text before save
			$deleteRes = $this->deleteAll(array("object_id" => $data['id'], "SearchText.lang" => $lang), false);
			if (!$deleteRes) {
				throw new BeditaException(__("Error saving search text", true));
			}

			foreach ($data as $k => $v) {
				if(in_array($k, $indexFields)) {
	                if (!empty($v)) {
						$sText = array(
			                'object_id' => $data['id'],
			                'lang'      => $lang, 
			                'content'   => $v,
			                'relevance' => $searchFields[$k]
		                );
	
		                $this->create();
		                if(!$this->save($sText)) 
		                    throw new BeditaException(__("Error saving search text {$model}: $k => $v", true));
	                }
				}
			}
		}
	}
}
?>