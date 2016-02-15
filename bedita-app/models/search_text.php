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
 * Search text model: index object texts
 * 
 */
class SearchText extends BEAppModel
{
    /**
     * External search engine model (e.g. "ElasticSearch", "Sphinx")
     * @var BEAppModel
     */
    private $indexModel = null;
    
	var $belongsTo = array(
		'BEObject' =>
			array(
				'fields'		=> 'id',
				'foreignKey'	=> 'object_id'
			)
	);
	
	

	/**
	 * Save search data for a model
	 * 
	 * @param object $model
	 * @throws BeditaException
	 * @return boolean
	 */
	public function createSearchText($model) {
		
		$bviorCompactResults = null;
		if (isset($model->bviorCompactResults)) {
			$bviorCompactResults = $model->bviorCompactResults ;
		}
		$model->bviorCompactResults = true ;
		$model->containLevel("default");
		if (!($data = $model->findById($model->{$model->primaryKey}))) {
			throw new BeditaException("Error loading {$model->name}");
		}
		$model->bviorCompactResults = $bviorCompactResults ;
		
		$searchFields = $this->getSearchFields($model);
		if (empty($data["id"])) {
			$data["id"] = $model->{$model->primaryKey};
		}
		
		$this->checkIndexModel();
		if($this->indexModel) {
			$res = $this->indexModel->indexObject($searchFields, $data);
			if(!empty($res["error"])) {
			    throw new BeditaException("crete object index error: " . 
			            $res["error"]);
			}
		} else {
			$this->saveSearchTexts($searchFields, $data);
		}
		
		return true ;
	}
	
    private function checkIndexModel() {
        if ($this->indexModel === null) {
            $engine = Configure::read("searchEngine");
            if (empty($engine)) {
                $this->indexModel = false;
            } else {
                $this->indexModel = ClassRegistry::init($engine);
            }
        }
    }
	
	/**
	 * Remove index data for object id
	 * @param unknown $id
	 */
    public function removeObject($id) {
        $this->checkIndexModel();
        if ($this->indexModel) {
            return $this->indexModel->removeObject($id);
        } else {
            return $this->deleteAll("object_id=".$id) ;
        }
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

	/**
	 * Save search data multilang
	 * 
	 * @param array $dataLangText
	 * @throws BeditaException
	 */
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
		if(!$this->deleteAll(array("SearchText.object_id" => $objectId, "SearchText.lang" =>$data['lang']), false)) {
			throw new BeditaException("Error deleting old search text items : " . $objectId . "-". $data['lang']);
		}
		$this->saveSearchTexts($searchFields, $data);
	}
	
    /**
     * Rebuild indexes for text search
     * 
     * @param array $options
     *      'returnOnlyFailed' => true (default) return only 'failed' and 'langTextFailed' array
     *                            false return also 'success' and 'langTextSuccess' array
     *      'delete' => delete index first (default false)
     *      'type' => index object type only (default unset -> all types)
     *      'log' => true to log errors
     *      'min' => min id to index (included)
     *      'max' => max id to index (included)
	 *
	 * @return array contains:
	 *			'success' => array of objects data successfully indexed. Each item contains:
	 *						'id' => object id indexed
	 *			'failed' => array of objects data on which rebuild index failed. Each item contains:
	 *						'id' =>  object id,
	 *						'error' => message error,
	 *						'details' => error detail
	 *			'langTextSuccess' => array of translations successfully indexed. Each item contains:
	 *						'object_id' => object id on which the translations was done,
	 *						'lang' => the translation language
	 *			'langTextFailed' => array of translations on which rebuild index failed. Each item contains:
	 *						'object_id' =>  object id on which the translations failed,
	 *						'lang' => the translation language,
	 *						"error" => message error,
	 *						"detail" => error detail
	 */
	public function rebuildIndex($options) {
	    
	    $returnOnlyFailed = (isset($options['returnOnlyFailed']))? $options['returnOnlyFailed'] : true;
	    $deleteIndex = (isset($options['delete']))? $options['delete'] : false;
	    $conditions = array();
        if (!empty($options['type'])) {
            $conditions['object_type_id'] = $options['type'];
            $this->log('using object type id: '. $options['type'], 'index');
        }
        if (!empty($options['min'])) {
            $conditions[] = 'id >= ' . $options['min'];
            $this->log('using min id: '. $options['min'], 'index');
        }
        if (!empty($options['max'])) {
            $conditions[] = 'id <= ' . $options['max'];
            $this->log('using max id: '. $options['max'], 'index');
        }
        $beObj = ClassRegistry::init("BEObject");
		$beObj->contain();
		$nObj = $beObj->find('count', array('conditions' => $conditions));
		$pageSize = 1000;
		$pageNum = 0;

		$this->log('num objects to index: '. $nObj, 'index');
		$this->initIndex($deleteIndex);
		$results = array('failed' => array(), 'langTextFailed' => array());
		if (!$returnOnlyFailed) {
			$results = array_merge($results, array('success' => array(), 'failed' => array()));
		}
        $count = 0;
		while( ($pageSize * $pageNum) < $nObj ) {
			$res = $beObj->find('list',array(
					'fields' => array('id'),
                    'conditions' => $conditions,
                    'order' => array('id' => 'asc'),
					'limit' => $pageSize,
					'offset' => $pageNum * $pageSize,
			));
			$pageNum++;
			foreach ($res as $id) {
                $count++;
				$type = $beObj->getType($id);
				if(empty($type)) {
					$results['failed'][] = array("id" => $id, "error" => "Object type not found for object id ". $id);
					$this->log('type not found for object: '. $id, 'index');
				} else {
					$model = ClassRegistry::init($type);
					$model->{$model->primaryKey} = $id;
					try {
						
						if (!$this->deleteAll("object_id=".$id)) {
							throw new BeditaException(__("Error deleting all search text indexed for object", true) . " " . $id);
						}
						$this->createSearchText($model);
                        $this->log("($count/$nObj) index created for: $id", 'index');
						if (!$returnOnlyFailed) {
							$results['success'][] = array("id" => $id);
						}
					} catch (BeditaException $ex) {
						$results['failed'][] = array("id" => $id, "error" => $ex->getMessage(), 'detail' => $ex->getDetails());
					}
				}
			}
				
		}
		
		// lang texts
		$this->log('indexing langtext translations...', 'index');
		$langText = ClassRegistry::init("LangText");
		$res = $langText->find('all',array("fields"=>array('DISTINCT LangText.object_id, LangText.lang')));	
		foreach ($res as $r) {
			
			$lt = $langText->find('all',array("conditions"=>array("LangText.object_id"=>$r['LangText']['object_id'], 
												"LangText.lang" => $r['LangText']['lang'])));	
			$dataLang = array();
			foreach ($lt as $item) {
				$dataLang[] = $item['LangText'];
			}
			try {
				$this->saveLangTexts($dataLang);
				if (!$returnOnlyFailed) {
					$results['langTextSuccess'][] = array(
						"object_id" => $r['LangText']['object_id'],
						"lang" => $r['LangText']['lang']
					);
				}
			} catch (BeditaException $ex) {
				$results['langTextFailed'][] = array(
					"object_id" => $r['LangText']['object_id'],
					"lang" => $r['LangText']['lang'],
					"error" => $ex->getMessage(),
					"detail" => $ex->getDetails()
				);
			}
		}
		$this->log('rebuildIndex done', 'index');
		return $results;
	}

    /**
     * Init new index before rebuild
     * @param boolean $delete
     */
    private function initIndex($delete) {
        $this->checkIndexModel();
        if($delete && !$this->indexModel) {
            $conditions = array("SearchText.id > 0");
            $this->deleteAll($conditions);
        }

        if($this->indexModel) {
            $this->indexModel->createIndex($delete);
        }
	}
	
	private function saveSearchTexts(array &$searchFields, array &$data) {
		
		if (!empty($searchFields)) {
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
		                if (!$this->save($sText)) 
		                    throw new BeditaException(__("Error saving search text {$model}: $k => $v", true));
	                }
				}
			}
		}
	}
}
?>