<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2009-2014 ChannelWeb Srl, Chialab Srl
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
 * BEdita base model classes
 */
class AppModel extends Model {

	public $actsAs = array('Containable');

    /**
     * Options used by default from self::apiTransformer()
     * @var array
     */
    protected $apiTransformerOptions = array(
        'castable' => array('integer', 'float', 'double', 'date', 'datetime', 'boolean')
    );

    /**
     * Return an array of column types to transform (cast)
     * Used to build consistent REST APIs
     *
     * Possible options are:
     * - 'castable' an array of fields that the REST APIs should cast to
     *
     * @param array $options
     * @return array
     */
    public function apiTransformer($options = array()) {
        $options = array_merge($this->apiTransformerOptions, $options);
        $columnTypes = $this->getColumnTypes();
        return array_intersect($columnTypes, $options['castable']);
    }

}

/**
 * Bedita model base class
 */
class BEAppModel extends AppModel {

	protected $modelBindings = array();
	protected $sQ = ''; // internal use: start quote
	protected $eQ = ''; // internal use: end quote
	protected $driver = ''; // internal use: database driver

    public $actsAs = array();

    /**
     * Merge record result array to top level, skipping specified keys.
     *
     * @param array $record Record data.
     * @param array $skipKeys Keys to be skipped even if their value is an array.
     * @return array Record merged to single array.
     */
    function am($record, array $skipKeys = array()) {
        $tmp = array();
        foreach ($record as $key => $val) {
            if (is_array($val) && !in_array($key, $skipKeys)) {
                // #639 - Associated models merged to main object results.
                $tmp = array_merge($tmp, $val);
            } else {
                $tmp[$key] = $val;
            }
        }

        return $tmp;
    }

	protected function setupDbParams() {
    	if(empty($this->driver)) {
			$db = ConnectionManager::getDataSource($this->useDbConfig);
			$this->driver = $db->config['driver'];
			$this->sQ = $db->startQuote;
			$this->eQ = $db->endQuote;
    	}
	}

	public function getStartQuote() {
		$this->setupDbParams();
		return $this->sQ;
	}

	public function getEndQuote() {
		$this->setupDbParams();
		return $this->eQ;
	}

	public function getDriver() {
		$this->setupDbParams();
		return $this->driver;
	}

	/**
	 * Get SQL date format
	 *
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function getDefaultDateFormat($value = null, $throwOnError = false) {
		if(is_integer($value)) {
			return date("Y-m-d", $value) ;
		}

		$result = null;
		if(is_string($value) && !empty($value)) {
            $dateFormatValidation = Configure::read("dateFormatValidation");
            if(!$dateFormatValidation) {
                // if config "dateFormatValidation" not set, expect valid SQL date format
                $pattern = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$|^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/";
                if (preg_match($pattern, $value)) {
                    $result = $value;
                } else {
                    if($throwOnError) {
                        throw new BeditaException(__("Error parsing date. Wrong format", true), array("date" => $value));
                    } else {
                        $this->log("Date not recognized: " . $value . " - field left blank");
                    }
                }
            } else {
    			$d_pos = strpos($dateFormatValidation,'dd');
    			$m_pos = strpos($dateFormatValidation,'mm');
    			$y_pos = strpos($dateFormatValidation,'yyyy');
    
    			$dateType = "little-endian"; // default dd/mm/yyyy
    			if($y_pos < $m_pos && $y_pos < $d_pos) {
    				$dateType = "big-endian"; // yyyy/mm/dd
    			} elseif ($m_pos < $d_pos) {
    				$dateType = "middle-endian"; // mm/dd/yyyy
    			}
    			try {
    				$result = BeLib::sqlDateFormat($value, $dateType);
    			} catch (Exception $ex) {
    				if($throwOnError) {
    					throw new BeditaException(__("Error parsing date. Wrong format", true), array("date" => $value));
    				} else {
    					$this->log("Date not recognized: " . $value . " - field left blank");
    				}
    			}
            }
		}

		return $result ;
	}

	/**
	 * Check date field in $this->data[ModelName][$key] -> set to null if empty or call getDefaultDateFormat
	 *
	 * @param string $key
	 * @param bool $throwOnError, throw exception on error, default false
	 */
	protected function checkDate($key, $throwOnError = false) {
		$data = &$this->data[$this->name];
		if(empty($data[$key])) {
			$data[$key] = null;
		} else {
			$data[$key] = $this->getDefaultDateFormat($data[$key], $throwOnError);
		}
	}

	/**
	 * Check float/double field in $this->data[ModelName][$key] -> set to null if empty
	 *
	 * @param string $key
	 */
	protected function checkFloat($key) {
		$data = &$this->data[$this->name];
		if(empty($data[$key])) {
			$data[$key] = null;
		}
	}

	/**
	 * Check integer/generic number in $this->data[ModelName][$key] -> set to null if empty
	 *
	 * @param string $key
	 */
	protected function checkNumber($key) {
		$data = &$this->data[$this->name];
		if(empty($data[$key])) {
			$data[$key] = null;
		}
	}

	/**
	 * Check duration format in $this->data[ModelName][$key] -> set to null if empty/invalid.
	 * 
	 * @param string key
	 */
	protected function checkDuration($key) {
		$data = &$this->data[$this->name];

		if (!array_key_exists($key, $data)) {
			// Avoid E_NOTICE if PHP's error_reporting & 8 == true.
			$data[$key] = null;
			return;
		}
		$data[$key] = preg_replace("/[^a-z0-9\:\.]/i", "", $data[$key]);  // cleans string.
		$matches = array();
		if (!empty($data[$key]) && preg_match("/^(?:(?P<y>\d+)y)?(?:(?P<w>\d+)w)?(?:(?P<d>\d+)d)?(?:(?P<h>\d+)h)?(?:(?P<m>\d+)m)?(?:(?P<s>\d+)s)?$/i", $data[$key], $matches)) {
			// y w d h m s
			$matches = array_merge(array('y' => 0, 'w' => 0, 'd' => 0, 'h' => 0, 'm' => 0, 's' => 0), $matches);
			$data[$key] = ((($matches['y'] * 365 + $matches['w'] * 7 + $matches['d']) * 24 + $matches['h']) * 60 + $matches['m']) * 60 + $matches['s'];
		} elseif (!empty($data[$key]) && preg_match("/^(?:(?:(?P<h>\d+)?\:)?(?P<m>\d+)?\:)?(?P<s>\d+)?$/", $data[$key], $matches)) {
			// hh:mm:ss
			$matches = array_merge(array('h' => 0, 'm' => 0, 's' => 0), $matches);
			$data[$key] = ($matches['h'] * 60 + $matches['m']) * 60 + $matches['s'];
		} else {
			$data[$key] = null;
		}
	}

	/**
	 * Object search Toolbar
	 *
	 * @param integer $page
	 * @param integer $dimPage page dimension (limit sql)
	 * @param integer $size	count of all records
	 *
	 * @return array for pagination
	 *			"first" => first page; if there is only one page its value is 0 else 1
	 *			"prev" => previous page; 0 if there isn't previous page
	 *			"next" => next page; 0 if there isn't next page
	 *			"last" => last page; 0 if it's the last page
	 *			"size" => number of all records
	 *			"pages" => total number of pages
	 *			"page" => number of current page
	 *			"dim" => number of records per page
	 *			"start" => number of the record with which the page begins counting from the first page
	 *			"end" => number of the record with which the page ends counting from the first page
	 *
	 */
	function toolbar($page, $dimPage, $size) {

		$toolbar = array("first" => 0, "prev" => 0, "next" => 0, "last" => 0, "size" => 0, "pages" => 0, "page" => 0, "dim" => 0, "start" => 0, "end" => 0) ;

		if (empty($size)) {
			$size = 0;
		}

		if (empty($dimPage)) {
			$dimPage = 0;
			$pageCount = 1;
		} else {
			$pageCount = $size / $dimPage;
			settype($pageCount,"integer");
			if($size % $dimPage) {
				$pageCount++;
			}
		}

		$toolbar["pages"] 	= $pageCount;
		$toolbar["page"]  	= $page;
		$toolbar["dim"]  	= $dimPage;

		if ($page == 1) {
			if($page < $pageCount) {
				// first page
				$toolbar["next"] = $page + 1;
				$toolbar["last"] = $pageCount;
			}
		} else {
			if ($page >= $pageCount) {
				// last page
				$toolbar["first"] = 1;
				$toolbar["prev"] = $page - 1;
			} else {
				// generic page
				$toolbar["next"] = $page + 1;
				$toolbar["last"] = $pageCount;
				$toolbar["first"] = 1;
				$toolbar["prev"] = $page - 1;
			}
		}

		if ($page <= $pageCount) {
			$toolbar["start"] = (($page - 1) * $dimPage) + 1;
			$toolbar["end"] = $page * $dimPage ;
			if ($toolbar["end"] > $size) {
				$toolbar["end"] = $size;
			}
		}
		$toolbar["size"] = $size;

		return $toolbar;
	}

	/**
	 * SQL limit clausole
	 *
	 * @param int $dim, global size/count
	 * @param int $page, page num
	 * @return string
	 */
	protected function getLimitClausole($dim , $page = 1) {
		$dataSource = ConnectionManager::getDataSource($this->useDbConfig);
		$offset = ($page > 1) ? (($page -1) * $dim) : null;
		return $dataSource->limit($dim, $offset);
	}

	public function containLevel($level = "minimum") {
        $fallbacks = array(
            'api' => 'frontend',
            'frontend' => 'minimum'
        );
        if (!isset($this->modelBindings[$level])) {
            // search fallback
            $end = false;
            while (!$end) {
                if (!isset($fallbacks[$level])) {
                    $end = true;
                } else {
                    $level = $fallbacks[$level];
                    if (isset($this->modelBindings[$level])) {
                        $end = true;
                    }
                }
            }
        }

		if (!isset($this->modelBindings[$level])) {
			throw new BeditaException("Contain level not found: $level");
		}
		$this->contain($this->modelBindings[$level]);
		return $this->modelBindings[$level];
	}

    /**
     * Return self::modelBindings level
     *
     * @param string $level define the level to return. Leave empty to return all bindings level
     * @return array|false return false if $level is not set
     */
    public function getBindingsLevel($level = null) {
        if (empty($level)) {
            return $this->modelBindings;
        }
        if (!isset($this->modelBindings[$level])) {
            return false;
        }
        return $this->modelBindings[$level];
    }

    /**
     * Set self::modelBindings level
     *
     * @param string $level the level name
     * @param array $bindings array of model bindings
     */
    public function setBindingsLevel($level, array $bindings = array()) {
        $this->modelBindings[$level] = $bindings;
    }

	public function fieldsString($modelName, $alias = null, $excludeFields = array()) {
		$s = $this->getStartQuote();
		$e = $this->getEndQuote();
		$model = ClassRegistry::init($modelName);
		$kTmp = array_keys($model->schema());
		$k = array_diff($kTmp, $excludeFields);
		if(empty($alias)) {
			$alias = $modelName;
		}
		$res = $s . $alias . $e ."." . $s . implode("{$e},{$s}$alias{$e}.{$s}", $k) . $e;
		return $res;
	}

	/**
	 * perform an objects search
	 *
	 * @param integer $id		root id, if it's set perform search on the tree
	 * @param string $userid	user: null (default) => no permission check. ' ' => guest/anonymous user,
	 * @param string $status	object status
	 * @param array  $filter	example of filter:
     *                          parent_id => used if $id is empty as root id
	 * 							"object_type_id" => array(21,22,...),
	 *							"ModelName.fieldname => "value",
	 * 							"query" => "text to search"
	 * 							....
	 *
	 *							reserved filter words:
	 *							"category" => "val" search by category id or category name
	 *							"relation" => "val" search by object_relations swicth
	 *							"rel_object_id" => "val" search object relateds to a particular object (object_relation object_id)
	 *							...
	 *							see all in BuildFilter behavior
     *
     *                          "afterFilter" => array() define some operations executed after the objects search
     *                                           to spec on array params see BEAppModel::findObjectsAfterFilter()
	 *
	 * @param string $order		field to order result (id, status, modified..)
	 * @param boolean $dir		true (default), ascending, otherwise descending.
	 * @param integer $page		Page number (for pagination)
	 * @param integer $dim		Page dim (for pagination). Default get all
	 * @param boolean $all		true: all tree levels (discendents), false: only first level (children)
	 * @param array $excludeIds Array of id's to exclude
	 */
	public function findObjects($id = null, $userid = null, $status = null, $filter = array(), $order = null, $dir = true, $page = 1, $dim = null, $all = false, $excludeIds = array()) {

        $afterFilter = array();
        if (isset($filter['afterFilter'])) {
            if (is_array($filter['afterFilter'])) {
                $afterFilter = $filter['afterFilter'];
            }
            unset($filter['afterFilter']);
        }

        // if 'count_permission' filter is set and 'num_of_permission' order is not requested
        // avoid join and count them after filter
        if (!empty($filter['count_permission']) && $order != 'num_of_permission') {
            unset($filter['count_permission']);
            $afterFilter[] = array(
                'className' => 'Permission',
                'methodName' => 'countPermissions'
            );
        }

        // if 'count_annotation' filter is set and 'num_of_annotation_object' order is not requested
        // avoid join and count them after filter
        // else join only annotation related to 'num_of_annotation_object' and count others in after filter
        if (!empty($filter['count_annotation'])) {
            if (!is_array($filter['count_annotation'])) {
                $countAnnotation = array($filter['count_annotation']);
            } else {
                $countAnnotation = $filter['count_annotation'];
            }
            unset($filter['count_annotation']);
            $countAnnotationNames = array();
            foreach ($countAnnotation as $annotationModelName) {
                $countAnnotationNames[$annotationModelName] = 'num_of_' . Inflector::underscore($annotationModelName);
            }

            if (in_array($order, $countAnnotationNames)) {
                $flipCountAnnotationNames =  array_flip($countAnnotationNames);
                $a = $flipCountAnnotationNames[$order];
                $filter['count_annotation'] = array($a);
                $countAnnotation = array_diff($countAnnotation, $filter['count_annotation']);
            }

            if (!empty($countAnnotation)) {
                 $afterFilter[] = array(
                    'className' => 'Annotation',
                    'methodName' => 'countAnnotations',
                    'options' => array(
                        'type' => $countAnnotation
                    )
                );
            }
        }

        if (isset($filter['parent_id'])) {
            $id = (!$id && !empty($filter['parent_id']))? $filter['parent_id'] : $id;
            unset($filter['parent_id']);
        }

        if (isset($filter['descendants'])) {
            $all = true;
            unset($filter['descendants']);
        }

        // if filter 'tree_related_object' is set
        // it filters objects that have some relation with objects located
        // on $id tree branch or on $id tree branch descendants (if $all is true)
        if ($id && isset($filter['tree_related_object'])) {
            $objectIds = array();
            $tree = ClassRegistry::init('Tree');
            if ($all) {
                $objectIds = $tree->find('list', array(
                    'fields' => array('id'),
                    'conditions' => array('object_path LIKE' => '%/' . $id . '/%'),
                    'group' => 'id'
                ));
            } else {
                $objectIds = $tree->find('list', array(
                    'fields' => array('id'),
                    'conditions' => array('parent_id' => $id)
                ));
            }
            $filter['ObjectRelation.object_id'] = $objectIds;
            // avoid to search objects children on $id branch tree
            $id = null;
        }

        if (!empty($filter['searchstring'])) {
            if (empty($filter['query'])) {
                $filter['query'] = $filter['searchstring'];
            }
            unset($filter['searchstring']);
        }

		$s = $this->getStartQuote();
		$e = $this->getEndQuote();

		$beObjFields = $this->fieldsString("BEObject");
		$fields = 'DISTINCT ' . $beObjFields;
		$from = "{$s}objects{$e} as {$s}BEObject{$e}";
		$conditions = array();
		$groupClausole = $beObjFields;

        $filterKeysString = implode('|', array_keys($filter));
        if (strstr($filterKeysString, 'Content.')) {
            $contentFields = $this->fieldsString('Content', null, array('id'));
            $fields .= ', ' . $contentFields;
            $from .= " LEFT OUTER JOIN {$s}contents{$e} as {$s}Content{$e} ON {$s}BEObject{$e}.{$s}id{$e}={$s}Content{$e}.{$s}id{$e}";
            $groupClausole .= ', ' . $contentFields;
            // if set remove Content::addContentFields() from afterFilter
            foreach ($afterFilter as $key => $f) {
                if ($f['className'] == 'Content' && $f['methodName'] == 'appendContentFields') {
                    unset($afterFilter[$key]);
                }
            }
        }

		if (!empty($status)) {
			$conditions[] = array("{$s}BEObject{$e}.{$s}status{$e}" => $status);
        }

        // actual SQL limit page (may vary using external searchEngine)
        $limitPage = $page;
        $rankOrder = array();
        $searchCount = null;
        if (!empty($filter["query"])) {
            $engine = Configure::read("searchEngine");
            if (!empty($engine)) {
                $options = array("id" => $id, "userid" => $userid,
                        "status" => $status, "filter" => $filter, "page" => $page,
                        "dim" => $dim, "all" => $all);
                $searchEngine = ClassRegistry::init($engine);
                $result = $searchEngine->searchObjects($options);
                $conditions[] = array("{$s}BEObject{$e}.{$s}id{$e}" => $result["ids"]);
                if (empty($order)) { // user rank order on empty $order
                    $rank = 1;
                    foreach ($result["ids"] as $idFound) {
                        $rankOrder[$idFound] = $rank++;
                    }
                }
                $searchCount = $result["total"];
                unset($filter["query"]);
                $limitPage = 1;
            // default search engine
            } else {
            	if (!empty($filter['searchType'])) {
            		$sType = $filter['searchType'];
            	} else {
                    $sType = (empty($filter['substring']))? 'fulltext' : 'like';
                }
                $filter['query'] = array(
                    'searchType' => $sType,
                    'searchString' => $filter['query']
                );
            }
        }
        if (isset($filter['searchType'])) {
        	unset($filter['searchType']);
        }

		if(!empty($excludeIds)) {
			$conditions["NOT"] = array(array("{$s}BEObject{$e}.{$s}id{$e}" => $excludeIds));
		}

		// setup filter to get only allowed objects
		// exclude backend private objects and object that stay only in private publication/section
		if (BACKEND_APP && $userid) {
			$filter["allowed_to_user"] = $userid;
		}

		// get specific query elements
		if (!$this->Behaviors->attached('BuildFilter')) {
			$this->Behaviors->attach('BuildFilter');
		}

		$sqlItems = $this->getSqlItems($filter);
        $otherFields = $sqlItems['fields'];
        $otherFrom = $sqlItems['from'];
        $otherJoins = $sqlItems['joins'];
        $otherConditions = $sqlItems['conditions'];
        $otherGroup = $sqlItems['group'];
        $otherOrder = $sqlItems['order'];
        $useGroupBy = $sqlItems['useGroupBy'];

		if (!empty($otherFields)) {
			$fields = $fields . $otherFields;
        }

		$conditions = array_merge($conditions, $otherConditions);
		$from .= $otherJoins . $otherFrom;

		if (!empty($id)) {
			$treeFields = $this->fieldsString("Tree");
			$fields .= "," . $treeFields;
			if ($this->getDriver() == 'mysql') {
				// #MYSQL
				$groupClausole .= ", {$s}Tree{$e}.{$s}id{$e}";
			} else {
				// #POSTGRES (@TODO: this clausole do not exclude double results. To fix it)
				$groupClausole .= "," . $this->fieldsString("Tree");
			}
			$from .= ", {$s}trees{$e} AS {$s}Tree{$e}";
			$conditions[] = " {$s}Tree{$e}.{$s}id{$e}={$s}BEObject{$e}.{$s}id{$e}" ;

			if ($all) {
				$cond = "";
				if ($this->getDriver() == 'mysql') {
					// #MYSQL
					$cond = " {$s}Tree{$e}.{$s}object_path{$e} LIKE (CONCAT((SELECT {$s}object_path{$e} FROM {$s}trees{$e} WHERE {$s}id{$e} = {$id}), '/%')) " ;
				} else {
					// #POSTGRES
					$cond = " {$s}Tree{$e}.{$s}object_path{$e} LIKE ((SELECT {$s}object_path{$e} FROM {$s}trees{$e} WHERE {$s}id{$e} = {$id}) || '/%') " ;
				}
				$conditions[] = $cond;
			} else {
				$conditions[] = array("{$s}Tree{$e}.{$s}parent_id{$e}" => $id) ;
			}
			if (empty($order) && empty($filter['query'])) {
				$order = "{$s}Tree{$e}.{$s}priority{$e}";
				$section = ClassRegistry::init("Section");
				$priorityOrder = $section->field("priority_order", array("id" => $id));
				if(empty($priorityOrder))
					$priorityOrder = "asc";
				$dir = ($priorityOrder == "asc");
			}
		}

        // if $order is empty and not performing search then set a default order
        if ((empty($order) || !preg_match('/^[a-z0-9`., _-]+$/i', trim($order))) && empty($filter['query'])) {
            $order = "{$s}BEObject{$e}.{$s}id{$e}";
            $dir = false;
        }

		// build sql conditions
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$sqlClausole = $db->conditions($conditions, true, true) ;

        if ($useGroupBy || !empty($otherGroup) || ($id && $all)) {
            $groupClausole = 'GROUP BY ' . $groupClausole . $otherGroup;
        } else {
            $groupClausole = '';
        }

		$ordClausole = "";
        if (is_string($order) && strlen($order)) {
			$beObject = ClassRegistry::init("BEObject");
			if ($beObject->hasField($order))
				$order = "{$s}BEObject{$e}.{$s}{$order}{$e}";
            $ordItem = $order . ((!$dir)? "DESC " : "");
			if (!empty($otherOrder)) {
				$ordClausole = "ORDER BY " . $ordItem .", " . $otherOrder;
			} else {
				$ordClausole = " ORDER BY {$order} " . ((!$dir)? " DESC " : "") ;
			}
		} elseif (!empty($otherOrder)) {
			$ordClausole = "ORDER BY {$otherOrder}";
		}

		$limit = (!empty($dim))? $this->getLimitClausole($dim, $limitPage) : '';
		$query = "SELECT {$fields} FROM {$from} {$sqlClausole} {$groupClausole} {$ordClausole} {$limit}";

		// #CUSTOM QUERY
		$tmp = $this->query($query);

		if ($tmp === false) {
			throw new BeditaException(__("Error finding objects", true));
        }

        if ($searchCount === null) {
    		$queryCount = "SELECT COUNT(DISTINCT {$s}BEObject{$e}.{$s}id{$e}) AS count FROM {$from} {$sqlClausole}";
    
    		// #CUSTOM QUERY
    		$tmpCount = $this->query($queryCount);
    		if ($tmpCount === false) {
    			throw new BeditaException(__("Error counting objects", true));
            }
    
    		$size = (empty($tmpCount[0][0]["count"]))? 0 : $tmpCount[0][0]["count"];
        } else {
            $size = $searchCount;
        }

		$recordset = array(
			"items"		=> array(),
			"toolbar"	=> $this->toolbar($page, $dim, $size) );

        // Keys to be skipped when merging results. #639 - Associated models merged to main object results.
        $skipKeys = array('RelatedObject', 'ReferenceObject', 'DateItem', 'ObjectProperty');
        foreach ($tmp as $item) {
            // #639 - Associated models merged to main object results.
            array_push($recordset['items'], $this->am($item, $skipKeys));
        }

		// reorder array using search engine rank
        if (!empty($rankOrder)) {
            $tmpOrder = array();
            foreach ($recordset['items'] as $item) {
                $id = $item['id'];
                $tmpOrder[$rankOrder[$id]] = $item;
            }
            ksort($tmpOrder);
            $recordset['items'] = array_values($tmpOrder);
        }

        // after filter callbacks
        if (!empty($afterFilter)) {
            $this->findObjectsAfterFilter($recordset['items'], $afterFilter);
        }

		return $recordset;
	}

    /**
     * callback called by BEAppModel::findObjects() to work on list of BEdita objects
     *
     * @param  array $items  list of BEdita objects filtered by BEAppModel::findObjects()
     * @param  array $params it's an array of configurable parameters to launch callbacks on Models or Behaviors.
     *                       Every callback has to return the array of BEdita objects passed to it.
     *
     *                       It can be a plain array:
     *
     *                       array(
     *                           'type' => 'Model' or 'Behavior' default to 'Model'
     *                           'className' => 'ClassName' for Behavior it's the class name without Behavior suffix
     *                           'methodName' => 'methodName' the method name of ClassName
     *                           'options' => array() array of options to pass to ClassName::methodName()
     *                       )
     *
     *                       or it can be a multidimensional array, for example
     *
     *                       array(
     *                           array(
     *                               'type' => 'Model',
     *                               'className' => 'ModelClassName',
     *                               'methodName' => 'modelMethodName',
     *                               'options' => array()
     *                           ),
     *                           array(
     *                               'type' => 'Behavior',
     *                               'className' => 'BehaviorClassName',
     *                               'methodName' => 'behaviorMethodName',
     *                               'options' => array()
     *                           )
     *                       )
     *
     *                       If type is 'Model' the 'modelMethodName' method of 'ModelClassName' should be defined as
     *
     *                       public function modelMethodName($items, $options) {
     *                           ....
     *                           return $items;
     *                       }
     *
     *                       If type is 'Behavior' the 'behaviorMethodName' method 'BehaviorClassName' should be defined as
     *
     *                       public function behaviorMethodName(&$model, $items, $options) {
     *                           ....
     *                           return $items;
     *                       }
     */
    protected function findObjectsAfterFilter(array &$items, array $params) {
        // multidimensional array two or more callbacks
        if (isset($params[0])) {
            foreach ($params as $value) {
                if (!empty($value['className'])) {
                    $this->findObjectsAfterFilter($items, $value);
                }
            }
        // only one callback
        } else {
            $default = array('type' => 'Model', 'className' => '', 'methodName' => '', 'options' => array());
            $params = array_merge($default, $params);
            if (!empty($params['className']) && !empty($params['methodName'])) {
                if ($params['type'] == 'Model') {
                    $modelClass = ClassRegistry::init($params['className']);
                    if (method_exists($modelClass, $params['methodName'])) {
                        $items = $modelClass->{$params['methodName']}($items, $params['options']);
                    }
                } elseif ($params['type'] == 'Behavior') {
                    if (App::import('Behavior', $params['className'])) {
                        $behaviorClass = $params['className'];
                        if (method_exists($behaviorClass . 'Behavior', $params['methodName'])) {
                            if (!$this->Behaviors->attached($behaviorClass)) {
                                $this->Behaviors->attach($behaviorClass);
                            }
                            $items = $this->{$params['methodName']}($items, $params['options']);
                            $this->Behaviors->detach($behaviorClass);
                        }
                    }
                }
            }
        }
    }

}

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////

// Internal class user for views
class _emptyAfterFindView {
	function afterFind($result) { return $result ; }
}

/**
 * BEdita base app object class. BEdita objects should extend BEAppObjectModel
 */
class BEAppObjectModel extends BEAppModel {
	var $recursive 	= 2 ;

	public $actsAs = array(
        'Callback',
        'CompactResult' => array(),
        'SearchTextSave',
        'RevisionObject',
        'ForeignDependenceSave' => array('BEObject'),
        'DeleteObject' => 'objects',
        'Notify'
	);

	var $hasOne= array(
			'BEObject' =>
			array(
				'className'		=> 'BEObject',
				'conditions'   => '',
				'foreignKey'	=> 'id',
				'dependent'		=> true
			)
		);

	public $objectTypesGroups = array();

	/**
	 * Overrides field, don't use CompactResult in field()
	 *
	 * @param string $name
	 * @param array $conditions
	 * @param string $order
	 */
	public function field($name, $conditions = null, $order = null) {

		$compactEnabled = $this->Behaviors->enabled('CompactResult');
		if ($compactEnabled) {
			$this->Behaviors->disable('CompactResult');
		}
		$res = parent::field($name, $conditions, $order);
		if ($compactEnabled) {
			$this->Behaviors->enable('CompactResult');
		}
		return $res;
	}

	/**
	 * Overrides saveField, don't use CompactResult in saveField()
	 *
	 * @param string $name
	 * @param array $conditions
	 * @param string $order
	 */
	public function saveField($name, $value, $validate = false) {

		$dependanceEnabled = $this->Behaviors->enabled('ForeignDependenceSave');
		if ($dependanceEnabled) {
			$this->Behaviors->disable('ForeignDependenceSave');
		}
		$res = parent::saveField($name, $value, $validate);
		if ($dependanceEnabled) {
			$this->Behaviors->enable('ForeignDependenceSave');
		}
		return $res;
	}

	function save($data = null, $validate = true, $fieldList = array()) {
		if(isset($data['BEObject']) && empty($data['BEObject']['object_type_id'])) {
            $data['BEObject']['object_type_id'] = BeLib::getObject('BeConfigure')->getObjectTypeId($this->name);
		} else if(!isset($data['object_type_id']) || empty($data['object_type_id'])) {
            $data['object_type_id'] = BeLib::getObject('BeConfigure')->getObjectTypeId($this->name);
		}

		// Se c'e' la chiave primaria vuota la toglie
		if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey])) {
			unset($data[$this->primaryKey]) ;
		}

		$data = (!empty($data[$this->alias]))? $data : array($this->alias => $data);

		// format data array for HABTM relations in cake way
		if (!empty($this->hasAndBelongsToMany)) {
			foreach ($this->hasAndBelongsToMany as $key => $val) {
				if (!empty($data[$this->alias][$key][$key])) {
					$data[$key][$key] = $data[$this->alias][$key][$key];
					unset($data[$this->alias][$key]);
				} elseif (!empty($data[$this->alias][$key])) {
					$data[$key][$key] = $data[$this->alias][$key];
					unset($data[$this->alias][$key]);
				} elseif ( (isset($data[$this->alias][$key]) && is_array($data[$this->alias][$key]))
							|| (isset($data[$this->alias][$key][$key]) && is_array($data[$this->alias][$key][$key])) ) {
					$data[$key][$key] = array();
				}
			}
		}

		$result = parent::save($data, $validate, $fieldList) ;

		return $result ;
	}

	/**
	 * Clone a BEdita object starting from object id
	 * It should be called from a BEdita object model as Document, Section, etc...
	 *
	 * @param int $id, the BEdita object id
	 * @param array $options, see BEAppObjectModel::arrangeDataForClone
	 * @return type
	 */
	public function cloneObject($id, array $options = array()) {
		$this->containLevel("detailed");
		$data = $this->findById($id);
		$this->arrangeDataForClone($data, $options);
		$this->create();
		return $this->save($data);
	}

	/**
	 * Arrange an array to cloning a BEdita object
	 *
	 * @param array $data, should come from a find
	 * @param array $options, default values are:
	 *				"nicknameSuffix" => "", suffix to append at the original object nickname
	 *				"keepTitle" => false, true to keep the original object title
	 *				"keepUserCreated" => false, true to keep the original user created
	 */
	public function arrangeDataForClone(array &$data, array $options = array()) {
		$defaultOptions = array("nicknameSuffix" => false, "keepTitle" => false, "keepUserCreated" => false);
		$options = array_merge($defaultOptions, $options);
		$toUnset = array("id", "ObjectType", "SearchText", "UserCreated", "UserModified", "Version");
		if (isset($data["nickname"])) {
			if ($options["nicknameSuffix"]) {
				$data["nickname"] .= $options["nicknameSuffix"];
			} else {
				$toUnset[] = "nickname";
			}
		}
		if (!$options["keepUserCreated"]) {
			$toUnset[] = "user_created";
		}
		foreach ($toUnset as $label) {
			if (isset($data[$label])) {
				unset($data[$label]);
			}
		}
		if (!$options["keepTitle"]) {
			$data["title"] .= " - " . __("copy", true);
		}
		if (!empty($data["Permission"]) && is_array($data["Permission"])) {
			foreach ($data["Permission"] as &$perm) {
				if (isset($perm["object_id"])) {
					unset($perm["object_id"]);
				}
				unset($perm["ugid"]);
				unset($perm["id"]);
			}
		}
		if (!empty($data["ObjectProperty"])) {
			$objectProperty = array();
			foreach ($data["ObjectProperty"] as $op) {
				if (!empty($op["value"]["property_value"])) {
					$objectProperty[] = array(
						"property_type" => $op["property_type"],
						"property_id" => $op["id"],
						"property_value" => $op["value"]["property_value"]
					);
				}
			}
			$data["ObjectProperty"] = $objectProperty;
		}
		if (!empty($data["RelatedObject"])) {
			$relatedObject = array();
			foreach ($data["RelatedObject"] as $key => $value) {
				$relatedObject[$value["switch"]][] = array(
					"id" => $value["object_id"],
					"switch" => $value["switch"],
					"priority" => $value["priority"]
				);
			}
			$data["RelatedObject"] = $relatedObject;
		}
        if (!empty($data['Category'])) {
            $cat = array();
            foreach ($data['Category'] as $k => $value) {
                $cat[] = $value['id'];
            }
            $data['Category'] = $cat;
        }
	}

	/**
	 * Updates hasMany model rows:
	 *   * delete all rows of hasMany models except ones with "id" set in $data array
	 *   * saves/updates all hasMany data rows from $data array
	 * 
	 * @throws BeditaException
	 * @return boolean
	 */
	protected function updateHasManyAssoc() {

		foreach ($this->hasMany as $name => $assoc) {

			if (isset($this->data[$this->name][$name])) {
				$model 		= ClassRegistry::init($assoc['className']) ;

				// delete previous associations
				$id = (isset($this->data[$this->name]['id'])) ? $this->data[$this->name]['id'] : $this->getInsertID() ;
				$foreignK = $assoc['foreignKey'] ;
				
				$exclude = array();
				foreach ($this->data[$this->name][$name] as $hasManyObj){
				    if(!empty($hasManyObj["id"])){
				        $exclude[] = $hasManyObj["id"];
				    }
				}
				$conditions = array($foreignK => $id, "NOT" => array("id" => $exclude));
				$model->deleteAll($conditions);

				// if there isn't data to save then exit
				if (!isset($this->data[$this->name][$name]) || !(is_array($this->data[$this->name][$name]) && count($this->data[$this->name][$name])))
					continue ;

				// save new associations
				$size = count($this->data[$this->name][$name]) ;
				for ($i=0; $i < $size ; $i++) {
					$model->create();
					$data 			 = &$this->data[$this->name][$name][$i] ;
					$data[$foreignK] = $id ;
					if(!$model->save($data)) {
						throw new BeditaException(__("Error saving associated data", true), $data);
					}
				}
			}
		}

		return true ;
	}

    /**
     * default values for Contents
     */
    protected function validateContent() {
    	$this->checkDate('start_date');
    	$this->checkDate('end_date');
    	$this->checkDuration('duration');
        return true ;
    }

    public function checkType($objTypeId) {
    	return ($objTypeId == Configure::read("objectTypes.".Inflector::underscore($this->name).".id"));
    }

    public function getTypeId() {
        return Configure::read("objectTypes.".Inflector::underscore($this->name).".id");
    }

    /**
     * Return an array of column types to transform (cast) for generic BEdita object type
     * Used to build consistent REST APIs
     *
     * In general it returns all castable fields from:
     * - main Model table
     * - tables that extend the object (ForeignDependenceSave)
     * - GetTag, Category, Tag
     *
     * Possible options are:
     * - 'castable' an array of fields that the REST APIs should cast to
     *
     * @param array $options
     * @return array
     */
    public function apiTransformer(array $options = array()) {
        $options = array_merge($this->apiTransformerOptions, $options);
        $transformer = parent::apiTransformer($options);
        if (!empty($this->actsAs['ForeignDependenceSave'])) {
            foreach ($this->actsAs['ForeignDependenceSave'] as $modelName) {
                $object = array_intersect($this->$modelName->getColumnTypes(), $options['castable']);
                $transformer = array_merge($transformer, $object);
            }
        }
        $transformer['GeoTag'] = $this->BEObject->GeoTag->apiTransformer();
        $transformer['Category'] = $transformer['Tag'] = $this->BEObject->Category->apiTransformer();
        return $transformer;
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Bedita simple object
**/

class BeditaSimpleObjectModel extends BEAppObjectModel {

	public $searchFields = array(
		"title" => 10,
		"nickname" => 8,
		"description" => 6,
		"note" => 2
	);

	public $useTable = 'objects';

	public $actsAs 	= array(
        'Callback',
        'CompactResult' => array(),
        'SearchTextSave',
        'Notify'
	);

	public $hasOne= array();
}

class BeditaObjectModel extends BeditaSimpleObjectModel {

	public $actsAs = array(
        'Callback',
        'CompactResult' => array(),
        'SearchTextSave',
        'DeleteObject' => 'objects',
        'Notify'
	);

	public $hasOne = array(
		'BEObject' =>
			array(
				'className'		=> 'BEObject',
				'foreignKey'	=> 'id'
			)
	);

    protected $modelBindings = array(
        'detailed' => array(
            'BEObject' => array(
                'ObjectType',
                'UserCreated',
                'UserModified',
                'Permission',
                'ObjectProperty',
                'LangText',
                'RelatedObject',
                'Annotation',
                'Category'
            )
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'ObjectType',
                'Annotation',
                'Category',
                'RelatedObject'
            )
        ),
        'minimum' => array('BEObject' => array('ObjectType')),
        'frontend' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'ObjectType',
                'Annotation',
                'Category',
                'RelatedObject'
            )
        ),
        'api' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'Category'
            )
        )
    );

	public function save($data = null, $validate = true, $fieldList = array()) {
		$data2 = $data;

		foreach($data2 as $key => $value) {
			if (!is_array($value)){
				unset($data2[$key]);
			}
		}

		if(isset($data['BEObject']) && empty($data['BEObject']['object_type_id'])) {
            $data['BEObject']['object_type_id'] = BeLib::getObject('BeConfigure')->getObjectTypeId($this->name);
		} else if(!isset($data['object_type_id']) || empty($data['object_type_id'])) {
            $data['object_type_id'] = BeLib::getObject('BeConfigure')->getObjectTypeId($this->name);
		}

		if(isset($data[$this->primaryKey]) && empty($data[$this->primaryKey])) {
			unset($data[$this->primaryKey]) ;
		}

		$data = (!empty($data[$this->alias]))? array("BEObject" => $data[$this->alias]) : array("BEObject" => $data);

		$beObject = ClassRegistry::init("BEObject");

		// format data array for HABTM relations in cake way
		if (!empty($beObject->hasAndBelongsToMany)) {
			foreach ($beObject->hasAndBelongsToMany as $key => $val) {
				if (!empty($data[$beObject->alias][$key][$key])) {
					$data[$key][$key] = $data[$beObject->alias][$key][$key];
					unset($data[$beObject->alias][$key]);
				} elseif (!empty($data[$beObject->alias][$key])) {
					$data[$key][$key] = $data[$beObject->alias][$key];
					unset($data[$beObject->alias][$key]);
				} elseif ( (isset($data[$beObject->alias][$key]) && is_array($data[$beObject->alias][$key]))
							|| (isset($data[$beObject->alias][$key][$key]) && is_array($data[$beObject->alias][$key][$key])) ) {
					$data[$key][$key] = array();
				}
			}
		}

        if (empty($data['id']) && empty($data['BEObject']['id']) && empty($data[$this->name]['id'])) {
            $beObject->create();
        } else {
            $beObject->create(null);
        }
        if (!$res = $beObject->save($data, $validate, $fieldList)) {
			return $res;
		}

		$data2["id"] = $beObject->id;
		$this->create(null);
		$res = parent::save($data2, $validate, $fieldList);
		//$res = Model::save($data, $validate, $fieldList) ;
		//$res = ClassRegistry::init("Model")->save($data2, $validate, $fieldList);

		return $res;
	}

}

/**
 * Bedita content model relations
**/

class BeditaContentModel extends BEAppObjectModel {

	public $searchFields = array(
		"title" => 10,
		"nickname" => 8,
		"creator" => 6,
		"description" => 6,
		"subject" => 4,
		"abstract" => 4,
		"body" => 4,
		"note" => 2
	);

	function beforeValidate() {
    	return $this->validateContent();
    }

}

/**
 * Bedita annotation model
**/

class BeditaAnnotationModel extends BEAppObjectModel {

	public $searchFields = array(
		"title" => 10,
		"nickname" => 8,
		"description" => 6,
		"body" => 4,
		"author" => 3,
		"note" => 2
	);

	var $belongsTo = array(
		"ReferenceObject" =>
			array(
				'className'		=> 'BEObject',
				'foreignKey'	=> 'object_id',
			),
	);

	public $actsAs = array(
        'Callback',
        'CompactResult'	=> array("ReferenceObject"),
        'SearchTextSave',
        'ForeignDependenceSave' => array('BEObject'),
        'DeleteObject' => 'objects',
        'Notify'
	);

    protected $modelBindings = array(
        'detailed' => array(
            'BEObject' => array(
                'ObjectType',
                'UserCreated',
                'Version' => array('User.realname', 'User.userid')
            ),
            'ReferenceObject'
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectType',
                'UserCreated'
            ),
            'ReferenceObject'),
        'minimum' => array('BEObject' => array('ObjectType')),
        'frontend' => array('BEObject', 'ReferenceObject'),
        'api' => array('BEObject')
    );

}


/**
 * Base model for simple stream objects.
 */
class BeditaSimpleStreamModel extends BEAppObjectModel {

	public $searchFields = array(
		"title" => 10,
		"nickname" => 8,
		"description" => 6,
		"subject" => 4,
		"abstract" => 4,
		"body" => 4,
		"name" => 6,
		"original_name" => 8,
		"note" => 2
	);

    protected $modelBindings = array(
        'detailed' => array(
            'BEObject' => array(
                'ObjectType',
                'Permission',
                'UserCreated',
                'UserModified',
                'RelatedObject',
                'Annotation',
                'Category',
                'LangText',
                'ObjectProperty',
                'Alias',
                'Version' => array('User.realname', 'User.userid')
            ),
            'Content'
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'ObjectType',
                'Annotation',
                'Category'
            ),
            'Content'
        ),
        'minimum' => array(
            'BEObject' => array('ObjectType', 'Category'),
            'Content'
        ),
        'frontend' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty',
                'Category',
                'RelatedObject'
            ),
            'Content'
        ),
        'api' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty',
                'Category'
            ),
            'Content'
        )
    );

	public $actsAs = array(
        'Callback',
        'CompactResult' => array(),
        'SearchTextSave' => array(),
        'RevisionObject',
        'ForeignDependenceSave' => array('BEObject', 'Content'),
        'DeleteObject' => 'objects',
        'Notify'
	);

	var $hasOne= array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' =>
				array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
	);

    function beforeValidate() {
        return $this->validateContent();
    }

}

/**
 * Base model for stream objects.
 */
class BeditaStreamModel extends BEAppObjectModel {

	public $searchFields = array(
		"title" => 10,
		"nickname" => 8,
		"description" => 6,
		"subject" => 4,
		"abstract" => 4,
		"body" => 4,
		"name" => 6,
		"original_name" => 8,
		"note" => 2
	);

    protected $modelBindings = array(
        'detailed' => array(
            'BEObject' => array(
                'ObjectType',
                'Permission',
                'UserCreated',
                'UserModified',
                'RelatedObject',
                'Category',
                'ObjectProperty',
                'LangText',
                'Annotation',
                'Alias',
                'Version' => array('User.realname', 'User.userid')
            ),
            'Content',
            'Stream'
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'ObjectType',
                'RelatedObject',
                'Category',
                'Annotation'
            ),
            'Content',
            'Stream'
        ),
        'minimum' => array(
            'BEObject' => array('ObjectType', 'Category'),
            'Content',
            'Stream'
        ),
        'frontend' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty',
                'Category',
                'RelatedObject'
            ),
            'Content',
            'Stream'
        ),
        'api' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty',
                'Category'
            ),
            'Content',
            'Stream'
        )
    );


	public $actsAs = array(
        'Callback',
        'CompactResult' => array(),
        'SearchTextSave' => array(),
        'RevisionObject',
        'ForeignDependenceSave' => array('BEObject', 'Content', 'Stream'),
        'DeleteObject' => 'objects',
        'Notify'
	);

	var $hasOne= array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' =>
				array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Stream' =>
				array(
					'className'		=> 'Stream',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
	);

    function beforeValidate() {
        return $this->validateContent();
    }

}

/**
 * Base class for products
 *
 */
class BeditaProductModel extends BEAppObjectModel {

	public $searchFields = array(
		"title" => 10,
		"nickname" => 8,
		"description" => 6,
		"abstract" => 4,
		"body" => 4,
		"note" => 2
	);

    protected $modelBindings = array(
        'detailed' => array(
            'BEObject' => array(
                'ObjectType',
                'Permission',
                'UserCreated',
                'UserModified',
                'RelatedObject',
                'ObjectProperty',
                'LangText',
                'Category',
                'Annotation',
                'Alias',
                'Version' => array('User.realname', 'User.userid')
            ),
            'Product'
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'ObjectType'
            ),
            'Product'
        ),
        'minimum' => array(
            'BEObject' => array('ObjectType'),
            'Product'
        ),
        'frontend' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty',
                'RelatedObject',
                'Category',
                'Annotation',
                'GeoTag'
            ),
            'Product'
        ),
        'api' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty',
                'Category',
                'GeoTag'
            ),
            'Product'
        )
    );

	public $actsAs = array(
        'Callback',
        'CompactResult' => array(),
        'SearchTextSave' => array(),
        'ForeignDependenceSave' => array('BEObject', 'Product'),
        'DeleteObject' => 'objects',
        'Notify'
	);

	var $hasOne= array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Product' =>
				array(
					'className'		=> 'Product',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
	);
}


////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Base model for collection objects.
 */
class BeditaCollectionModel extends BEAppObjectModel {

	public $actsAs = array(
        'Callback',
        'CompactResult' => array(),
        'SearchTextSave',
        'RevisionObject',
        'ForeignDependenceSave' => array('BEObject'),
        'DeleteDependentObject'	=> array('section'),
        'DeleteObject' => 'objects',
        'Notify'
    );

	var $recursive 	= 2;

	var $hasOne = array(
			'BEObject' =>
				array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Tree' =>
				array(
					'foreignKey'	=> 'id',
				)
	);
	
	public function deleteCollection($id) {
		return ClassRegistry::init('Tree')->removeBranch($id);
	}
}

/**
 * Base model for import filters.
 */
abstract class BeditaImportFilter extends BEAppModel {

	public $useTable = false;

	protected $typeName = "";
	protected $mimeTypes = array();

	/**
	 * Import BE objects from XML source string
	 *
	 * @param string $sourcePath, path to source to import, e.g. path to local files, urls...
	 * @param array $options, import options: "sectionId" => import objects in this section
	 * @return array , result array containing
	 * 	"objects" => number of imported objects
	 *  "message" => generic message (optional)
	 *  "error" => error message (optional)
	 * @throws BeditaException
	 */
	public function import($sourcePath, array $options = array())  {
		throw new BeditaException(__("Missing method", true));
	}

	/**
	 * Supported mime types
	 *
	 * @return array , array of supported mime types like
	 * 	"text/xml", "application/xml"
	 */
	public function mimeTypes() {
		return $this->mimeTypes;
	}

	/**
	 * Filter logical name
	 */
	public function name() {
		return $this->typeName;
	}

};

/**
 * Base model for export filters.
 */
abstract class BeditaExportFilter extends BEAppModel {

	public $useTable = false;

	protected $typeName = "";
	protected $mimeTypes = array();

	/**
	 * Export objects in XML format
	 *
	 * @param array $objects, object to export array
	 * @param array $options, export options
	 * @return array containing
	 * 	"content" - export content
	 *  "contentType" - content mime type
	 *  "size" - content length
	 * @throws BeditaException
	 */
	public function export(array &$objects, array $options = array()) {
		throw new BeditaException(__("Missing method", true));
	}

	/**
	 * Supported mime types
	 *
	 * @return array , result array containing supported mime types in the form
	 * 	"xml" => "text/xml", "zip" => "application/zip",....
	 */
	public function mimeTypes() {
		return $this->mimeTypes;
	}

	/**
	 * Filter logical name
	 */
	public function name() {
		return $this->typeName;
	}

	/**
	 * Validate resource (after export)
	 */
	public function validate($resource, array $options = array()){
		return __("No 'validate' method found");
	}

};
