<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008, 2010 ChannelWeb Srl, Chialab Srl
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
 *
 * BuildFilter Class
 * build custom sql statements used in BEAppModel::findObjects() method to filter list of BEdita objects
 *
 * It can be extended with custom behavior classes to refine query according to your needs
 *
 * Example:
 *
 * App::import('Behavior', 'BuildFilter');
 *
 * ClassNameFilterBehavior extends BuildFilterBehavior {
 *
 *      public function myFilterMethod($data) {
 *
 *      }
 *
 * }
 *
 * to call that method you have to build your filter to pass at BEAppModel::findObjects() as
 *
 * $filter['ClassNameFilter.myFilterMethod'] = $yourData;
 *
 * $yourData item will be passed to myFilterMethod as first argument
 *
 * methods of your custom filter class should be return an array that can have the following keys:
 *  - "fields" => string of fields to add to query
 *  - "fromStart" => string of FROM statement to insert at the begin of the string (LEFT, RIGHT and INNER JOIN statements)
 *  - "fromEnd" => string of FROM statement to insert at the end of the string (list of tables and aliases)
 *  - "conditions" => array of conditions to add at WHERE statement
 *  - "group" => string to add at GROUP statement
 *  - "order" => string to add at ORDER statement
 *
 *
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $LastChangedDate$
 *
 * $Id$
 */
class BuildFilterBehavior extends ModelBehavior {

    /**
     * string of query fields
     * @var string
     */
    private $fields = "";

    /**
     * sql FROM statement
     * it hasn't to contain the "FROM" string
     * @var string
     */
    private $from = '';

    /**
     * sql JOINS built using CakePHP
     * @var string
     */
    private $joins = '';

    /**
     * sql conditions
     * @var array
     */
    private $conditions = array();

    /**
     * sql GROUP statement
     * it hasn't to contain the "GROUP" string
     * @var string
     */
    private $group = "";

    /**
     * sql ORDER BY statement
     * it hasn't to contain the "ORDER BY" string
     * @var string
     */
    private $order = "";

    /**
     * filter parameters used to build the sql statements
     * @var array
     */
    protected $filter = array();

    /**
     * sql start quote
     * @var string
     */
    protected $startQuote = "";

    /**
     * sql end quote
     * @var string
     */
    protected $endQuote = "";

    /**
     * model
     * @var BEAppModel
     */
    protected $model = "";

    /**
     * sql driver
     * @var string
     */
    private $driver = "";

    /**
     * translate some filter shortcut to extended version
     *
     * @var array
     */
    private $map = array(
        'relation' => 'ObjectRelation.switch',
        'rel_object_id' => 'ObjectRelation.object_id',
        'comment_object_id' => 'Comment.object_id'
    );

    private $dataSource = null;

    /**
     * Force BEAppModel::findObjects() to use 'GROUP BY' clausole
     * its value is returned in self::getSqlItems()
     *
     * @var boolean
     */
    private $useGroupBy = false;

    function setup(&$model, $settings=array()) {
        $this->model = $model;
        if(empty($this->sQ)) {
            $this->dataSource = ConnectionManager::getDataSource($model->useDbConfig);
            $this->startQuote = $this->dataSource->startQuote;
            $this->endQuote = $this->dataSource->endQuote;
            $this->driver = $this->dataSource->config["driver"];
        }
    }

    /**
     * Prepare and return $afterFilter array built from $filter
     *
     * @param array $filter The filter to apply
     * @param string $order The order string
     * @return array
     */
    public function prepareAfterFilter(array &$filter, $order) {
        $afterFilter = array();
        if (array_key_exists('afterFilter', $filter)) {
            if (is_array($filter['afterFilter'])) {
                $afterFilter = $filter['afterFilter'];
            }
            unset($filter['afterFilter']);
        }

        // if 'count_permission' filter is set and 'num_of_permission' order is not requested
        // avoid join and count them after filter
        if (!empty($filter['count_permission']) && $order != 'num_of_permission') {
            $afterFilter[] = array(
                'className' => 'Permission',
                'methodName' => 'countPermissions'
            );
        }

        $filterKeysString = implode('|', array_keys($filter));
        if (strstr($filterKeysString, 'Content.')) {
            // if set remove Content::addContentFields() from afterFilter
            foreach ($afterFilter as $key => $f) {
                if ($f['className'] == 'Content' && $f['methodName'] == 'appendContentFields') {
                    unset($afterFilter[$key]);
                }
            }
        }

        // if 'count_annotation' filter is set and 'num_of_annotation_object' order is not requested
        // count them after filter
        // else count others in after filter
        if (!empty($filter['count_annotation'])) {
            $countAnnotation = !is_array($filter['count_annotation']) ? array($filter['count_annotation']) : $filter['count_annotation'];

            $countAnnotaionModel = Inflector::camelize(str_replace('num_of_', '', $order));

            if (in_array($countAnnotaionModel, $countAnnotation)) {
                $countAnnotation = array_diff($countAnnotation, array($countAnnotaionModel));
            }

            if (empty($countAnnotation)) {
                return;
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

        return $afterFilter;
    }

    /**
     * Check `$filter['count_permission']` and remove it if necessary.
     * It is removed if:
     *
     * * it's not empty and it doesn't appear in `$order` as 'num_of_permission' string
     *
     * @param array $filter The array of filters to apply
     * @param string $order The order string
     * @return void
     */
    public function prepareCountPermissionFilter(array &$filter, $order) {
        if (!array_key_exists('count_permission', $filter)) {
            return;
        }

        if (!empty($filter['count_permission']) && $order != 'num_of_permission') {
            unset($filter['count_permission']);
        }
    }

    /**
     * Check `$filter['count_annotation']` and remove it if necessary.
     * It is removed if:
     *
     * * it is empty
     * * it doesn't appear in `$order` as 'num_of_object' string (for example 'num_of_comment', etc...)
     *
     * @param array $filter The array of filters to apply
     * @param string $order The order string
     * @return void
     */
    public function prepareCountAnnotationFilter(array &$filter, $order) {
        if (!array_key_exists('count_annotation', $filter)) {
            return;
        }

        if (empty($filter['count_annotation'])) {
            unset($filter['count_annotation']);
            return;
        }

        if (!is_array($filter['count_annotation'])) {
            $filter['count_annotation'] = array($filter['count_annotation']);
        }

        $searchAnnotation = Inflector::camelize(str_replace('num_of_', '', $order));
        if (!in_array($searchAnnotation, $filter['count_annotation'])) {
            unset($filter['count_annotation']);
        }
    }

    /**
     * Prepare object type filter transforming
     * object type string in object type id
     *
     * @param array $filter The array of filters to apply
     * @return void
     */
    public function prepareObjectTypeFilter(&$filter) {
        if (!array_key_exists('object_type', $filter)) {
            return;
        }

        if (empty($filter['object_type'])) {
            unset($filter['object_type']);
            return;
        }

        if (!is_array($filter['object_type'])) {
            $filter['object_type'] = array($filter['object_type']);
        }
        foreach ($filter['object_type'] as $ot) {
            $filter['object_type_id'][] = Configure::read('objectTypes.' . $ot . '.id');
        }
        unset($filter['object_type']);
    }

    /**
     * Prepare 'query' search filter
     *
     * @param array $filter The filter to apply
     * @param array $options [description]
     * @return void
     */
    public function prepareSearchFilter(array &$filter) {
        if (array_key_exists('searchstring', $filter)) {
            if (empty($filter['query'])) {
                $filter['query'] = $filter['searchstring'];
            }
            unset($filter['searchstring']);
        }

        if (array_key_exists('searchType', $filter)) {
            $searchType = $filter['searchType'];
            unset($filter['searchType']);
        }

        if (empty($filter['query'])) {
            return;
        }

        if (empty($searchType)) {
            $searchType = empty($filter['substring']) ? 'fulltext' : 'like';
        }

        $filter['query'] = array(
            'searchType' => $searchType,
            'searchString' => $filter['query']
        );
    }

    /**
     * Get results using a search engine
     *
     * For example it could use Elasticsearch.
     *
     * Return false if no search engine is found or if $options['filter']['query'] is empty.
     * Return an array as
     *
     * ```
     * array(
     *     'result' => array(), // the array of results found by search engine
     *     'count' => 12, // the number of results
     *     'order' => array() // an array with object ids as key and the rank as value
     * )
     * ```
     *
     * @param string $engine The engine Model name i.e. 'ElasticSearch'
     * @param array $options An array of options.
     *                       At least $options['filter']['query'] must be populated
     * @return array|bool
     */
    public function searchEngineResult($engine, $options) {
        if (empty($engine) || empty($options['filter']['query'])) {
            return false;
        }

        if (array_key_exists('searchString', $options['filter']['query'])) {
            $options['filter']['query'] = $options['filter']['query']['searchString'];
        }

        $searchEngine = ClassRegistry::init($engine);
        if (empty($searchEngine)) {
            return false;
        }
        $result = $searchEngine->searchObjects($options);
        $searchResult = array(
            'result' => $result,
            'count' => $result['total'],
            'order' => null
        );

        // use rank order
        if (empty($options['order'])) {
            $rank = 1;
            foreach ($result['ids'] as $idFound) {
                $searchResult['order'][$idFound] = $rank++;
            }
        }

        return $searchResult;
    }

    /**
     * set conditions, from, fields, group and order from $filter
     *
     * @param array $filter
     * @return array
     */
    function getSqlItems(&$model, $filter) {
        $this->initVars($filter);
        $s = $this->startQuote;
        $e = $this->endQuote;

        // build joins using cake
        if (!empty($filter['joins'])) {
            $this->addJoins($filter['joins']);
        }

        $beObject = ClassRegistry::init("BEObject");
        $bool = array('and', 'or', 'not');
        // #CUSTOM QUERY -- all class methods
        foreach ($this->filter as $key => $val) {

            // if key is in $bool, conditions will be built by cake
            if (in_array(strtolower($key), $bool)) {
                // wrong data type passed, skip it
                if (!is_array($val)) {
                    continue;
                }
                if (empty($this->conditions[$key])) {
                    $this->conditions[$key] = array();
                }
                $this->conditions[$key] += $val;
            } else {

                $filterExtension = explode(".", $key);
                $filterClassName = null;
                $filterMethodName = null;
                if (count($filterExtension) > 1) {
                    $filterClassName = $filterExtension[0];
                    $filterMethodName = $filterExtension[1];
                }

                // if exists $filterClassNameBehavior::$filterMethodName()
                if ($filterClassName && App::import('Behavior', $filterClassName)
                        && is_subclass_of($filterClassName . 'Behavior', get_class($this))
                        && method_exists($filterClassName . 'Behavior', $filterMethodName)) {

                    // attach behavior
                    if (!$this->model->Behaviors->attached($filterClassName)) {
                        $this->model->Behaviors->attach($filterClassName);
                    }

                    $items = $this->model->Behaviors->$filterClassName->$filterMethodName($val);
                    if (!empty($items["fields"])) {
                        $this->fields .= ", " . $items["fields"];
                    }
                    if (!empty($items["fromStart"])) {
                        $this->from = $items["fromStart"] . $this->from;
                    }
                    if (!empty($items["fromEnd"])) {
                        $this->from .= ", " . $items["fromEnd"];
                    }
                    if (!empty($items["conditions"])) {
                        $this->conditions = array_merge($this->conditions, $items["conditions"]);
                    }
                    if (!empty($items["group"])) {
                        $this->group .= $items["group"];
                    }
                    if (!empty($items["order"])) {
                        $this->order .= $items["order"];
                    }

                // else if exists specific method
                } elseif (method_exists($this, $key . "Filter")) {
                    $this->{$key . "Filter"}($s, $e, $val);

                } else {

                    $op = null;
                    if (preg_match('/(.+)\s+(<=|>=|<>|<|>|like|LIKE)$/', $key, $matches)) {
                        $key = $matches[1];
                        $op = ' ' . $matches[2];
                    }

                    // else if $key is a BEobject field or if it is in the form Model.field
                    if ($beObject->hasField($key) || strstr($key, '.')) {

                        if ($beObject->hasField($key)) {
                            $this->fields .= ', ' . "{$s}BEObject{$e}." . $s . $key . $e;
                            $key = 'BEObject.' . $key;
                        } else {

                            if (strstr($key, '.*')) {
                                $mod = str_replace('.*', '', $key);
                                $f = $this->model->fieldsString($mod);
                                $this->group .= ',' . $f;
                                //$key = $s . $mod . $e . ".*";
                                $this->fields .= ', ' . $f;
                            } else {
                                $f = $s . str_replace('.', "{$e}.{$s}", $key) . $e;
                                $this->group .= ',' . $f;
                                $this->fields .= ', ' . $f;
                            }
                        }

                        //$this->fields .= ", " . $key;

                        // if there is an operator in the $key defers to cake the build conditions
                        if ($op !== null) {
                            $this->conditions[$key . $op] = $val;
                        } else {

                            // if $val is array then it build 'IN' or 'NOT IN' condition
                            if (is_array($val)) {
                                $firstKey = strtolower(key($val));
                                if ($firstKey == 'not') {
                                    if (empty($this->conditions['NOT'])) {
                                        $this->conditions['NOT'] = array();
                                    }
                                    $this->conditions['NOT'] += array($key => $val['NOT']);
                                } else {
                                    $this->conditions[$key] = $val;
                                }
                            } elseif ($val !== '' && $val !== null) {
                                $this->conditions[$key] = $val;
                            }
                        }

                        $arr = explode('.', $key);
                        if (count($arr) == 2) {
                            $modelName = str_replace(array($s, $e), '', $arr[0]);
                            if (!strstr($modelName, 'BEObject') && $modelName != 'Content') {
                                $model = ClassRegistry::init($modelName);
                                if ($model) {
                                    $f_str = $s. $model->useTable . $e. ' as ' . $s. $model->alias . $e;
                                    // create join with BEObject
                                    if (empty($this->from) || !strstr($this->from, $f_str)) {
                                        $this->from .= ', ' . $f_str;
                                        if (empty($model->hasOne['BEObject']) && $model->hasField('object_id') && $model->alias != 'ObjectRelation') {
                                            $this->conditions[] = "{$s}BEObject{$e}.{$s}id{$e} = {$s}" . $model->alias . "{$e}.{$s}object_id{$e}";
                                        } else {
                                            $this->conditions[] = "{$s}BEObject{$e}.{$s}id{$e} = {$s}" . $model->alias . "{$e}.{$s}id{$e}";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return array(
            'fields' => $this->fields,
            'from' => $this->from,
            'joins' => $this->joins,
            'conditions' => $this->conditions,
            'group' => $this->group,
            'order' => $this->order,
            'useGroupBy' => $this->useGroupBy
        );
    }

    /**
     * initialize sql items and some filter
     *
     * @param array $filter
     */
    private function initVars(array $filter) {
        $this->fields = '';
        $this->from = '';
        $this->joins = '';
        $this->conditions = array();
        $this->group = '';
        $this->order = '';
        $this->useGroupBy = false;

        foreach ($filter as $key => $value) {
            if (array_key_exists($key, $this->map)) {
                $filter[$this->map[$key]] = $value;
                unset($filter[$key]);
            }
        }

        if (array_key_exists("ref_object_details", $filter)) {
            $mod = $filter["ref_object_details"];
            $found = false;
            foreach ($filter as $k => $v) {
                if (strstr($k, $mod . ".")) {
                    $found = true;
                }
            }
            if (!$found) {
                $filter[$mod . ".*"] = "";
            }
        }
        $this->filter = $filter;
    }

    /**
     * Add joins statements
     *
     * $joins must be an array as
     *
     * ```
     * array(
     *     array(
     *         'table' => 'table_name',
     *         'alias' => 'TableName',
     *         'type' => 'INNER',
     *         'conditions' => array(
     *             'TableName.key_one = JoinToTable.key_two'
     *         )
     *     ),
     *     array(
     *         ...
     *     ),
     * )
     * ```
     *
     * where every internal array describe the join in CakePHP way
     *
     * @param array $joins
     * @return void
     */
    protected function addJoins(array $joins) {
        foreach ($joins as $key => $join) {
            if (!is_numeric($key)) {
                continue;
            }
            $this->joins .= ' ' . $this->dataSource->buildJoinStatement($join);
        }
    }

    /**
     * filter to get user_id (as obj_userid) joined to an object through object_users table
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed $value, if not empty to get user_id joined to an object with $value relation (object_users.swicth)
     */
    protected function object_userFilter($s, $e, $value = null) {
        //$this->fields .= ", {$s}UserOU{$e}.{$s}userid{$e} AS obj_userid";
        $this->fields .= ", {$s}ObjectUser{$e}.{$s}user_id{$e} AS obj_userid";
        $from = " LEFT OUTER JOIN {$s}object_users{$e} AS {$s}ObjectUser{$e} ON {$s}BEObject{$e}.{$s}id{$e}={$s}ObjectUser{$e}.{$s}object_id{$e}";
        if(!empty($value)) {
            $value = Sanitize::escape($value);
            $from .= " AND {$s}ObjectUser{$e}.{$s}switch{$e} = '$value'";
        }
        //$from .= " LEFT OUTER JOIN {$s}users{$e} AS {$s}UserOU{$e} ON {$s}ObjectUser{$e}.{$s}user_id{$e}={$s}UserOU{$e}.{$s}id{$e}";
        $this->from = $from . $this->from;
        //$this->group .= ", {$s}ObjectUser{$e}.object_id, {$s}UserOU{$e}.{$s}userid{$e}";
        $this->group .= ", obj_userid";
    }

    /**
     * add a count of Annotation objects as Comment, EditoreNote, etc...
     * If 'object_type_id' specified in BuildFilter::filter then get annotations for that object type/s
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed $value, annotation model or an array of annotation models
     *          Example:
     *
     *          $value = "Comment" => add count of comment as 'num_of_comment'
     *          $value = array("Comment", "EditorNote") => add count of comments as 'num_of_comment'
     *                                                     and count of editor notes as 'num_of_editor_note'
     */
    protected function count_annotationFilter($s, $e, $value) {
        if (!is_array($value)) {
            $value = array($value);
        }

        if (!empty($this->filter["object_type_id"])) {
            $object_type_id = $this->filter["object_type_id"];
        } elseif (!empty($this->filter["BEObject.object_type_id"])) {
            $object_type_id = $this->filter["BEObject.object_type_id"];
        }

        foreach ($value as $key => $annotationType) {
            $annotationModel = ClassRegistry::init($annotationType);
            $refObj_type_id = Configure::read("objectTypes." . Inflector::underscore($annotationModel->name) . ".id");
            $numOf = "num_of_" . Inflector::underscore($annotationModel->name);
            $this->fields .= ", " . $numOf;  // Same as issue #541.
            $from = " LEFT OUTER JOIN (
                        SELECT DISTINCT {$s}BEObject{$e}.{$s}id{$e}, COUNT({$s}" . $annotationModel->name . "{$e}.{$s}id{$e}) AS " . $numOf ."
                        FROM {$s}objects{$e} AS {$s}BEObject{$e}
                        LEFT OUTER JOIN {$s}annotations{$e} AS {$s}" . $annotationModel->name . "{$e} ON {$s}BEObject{$e}.{$s}id{$e}={$s}" . $annotationModel->name . "{$e}.{$s}object_id{$e}
                        RIGHT OUTER JOIN {$s}objects{$e} AS {$s}RefObj{$e} ON ({$s}RefObj{$e}.{$s}id{$e} = {$s}" . $annotationModel->name . "{$e}.{$s}id{$e} AND {$s}RefObj{$e}.{$s}object_type_id{$e}=" . $refObj_type_id . ")
                    ";
            if (!empty($object_type_id)) {
                $from .= (is_array($object_type_id))? "WHERE {$s}BEObject{$e}.{$s}object_type_id{$e} IN (" . implode(",", $object_type_id) . ")" : "WHERE {$s}BEObject{$e}.{$s}object_type_id{$e}=".$object_type_id;
            }

            $from .= " GROUP BY {$s}BEObject{$e}.{$s}id{$e}
                    ) AS {$s}".$annotationModel->name."{$e} ON {$s}".$annotationModel->name."{$e}.{$s}id{$e} = {$s}BEObject{$e}.{$s}id{$e}";

            $this->from = $from . $this->from;
        }

        $this->useGroupBy = true;
    }

    /**
     * get a category of an object naming it mediatype (used in Multimedia module)
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     */
    protected function mediatypeFilter($s, $e) {
        $this->fields .= ", {$s}Category{$e}.{$s}name{$e} AS mediatype";
        $this->from = " LEFT OUTER JOIN {$s}object_categories{$e} AS {$s}ObjectCategory{$e} ON {$s}BEObject{$e}.{$s}id{$e}={$s}ObjectCategory{$e}.{$s}object_id{$e}
                LEFT OUTER JOIN {$s}categories{$e} AS {$s}Category{$e} ON {$s}ObjectCategory{$e}.{$s}category_id{$e}={$s}Category{$e}.{$s}id{$e} AND {$s}Category{$e}.{$s}object_type_id{$e} IS NOT NULL"
                . $this->from;
        $this->conditions[] = "({$s}Category{$e}.{$s}name{$e} IS NOT NULL OR NOT EXISTS
            (SELECT {$s}ObjectCategory{$e}.{$s}object_id{$e}
                FROM {$s}object_categories{$e} AS {$s}ObjectCategory{$e}, {$s}categories{$e} AS {$s}Category{$e}
                WHERE {$s}BEObject{$e}.{$s}id{$e}={$s}ObjectCategory{$e}.{$s}object_id{$e}
                AND {$s}ObjectCategory{$e}.{$s}category_id{$e}={$s}Category{$e}.{$s}id{$e}
                AND {$s}Category{$e}.{$s}object_type_id{$e} IS NOT NULL ) )";
        $this->group .= ", {$s}Category{$e}.{$s}name{$e}";
    }

    /**
     * search text filter using fulltext or sql-like
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed $value, the string to search for (default $defaultConf['searchType'] used)
     *                      array(
     *                          'searchType' => 'like' or 'fulltext'
     *                          'searchString' => 'the string to search'
     *                      )
     */
    protected function queryFilter($s, $e, $value) {
        if (!is_array($value)) {
            $value = array('searchString' => $value);
        }
        $defaultConf = array('searchType' => 'like', 'searchString' => '');
        $queryConf = array_merge($defaultConf, $value);
        if (empty($queryConf['searchString'])) {
            return;
        }

        // #MYSQL
        App::import('Sanitize');
        $searchString = Sanitize::clean($queryConf['searchString'],
            array('escape' => false, 'encode' => false, 'remove_html' => true));
        $searchString = addslashes($searchString);

        $sType = $queryConf['searchType'];

        $orderPrefix = (!empty($this->order) ? ', ' : '');
        if ($sType == "fulltext") {
            if ($this->driver === "mysql") {
                // #MYSQL
                $this->fields .= ", SearchText.object_id AS oid, SUM( MATCH (SearchText.content) AGAINST ('" . $searchString . "') * SearchText.relevance ) AS points";
                $this->from .= ", search_texts AS SearchText";
                $this->conditions[] = "SearchText.object_id = BEObject.id AND SearchText.lang = BEObject.lang AND MATCH (SearchText.content) AGAINST ('" . $searchString . "')";
                $this->order .= $orderPrefix . "points DESC ";
            } elseif ($this->driver === "postgres") {
                $expr = explode(" ", $searchString);
                $ts = "";
                for($i = 0; $i < count($expr); $i++) {
                    if(!empty($expr[$i])) {
                        $ts .= (empty($ts) ? "" : " | ") . trim($expr[$i]);
                    }
                }
                // #POSTGRES
                $this->fields .= ", {$s}SearchText{$e}.{$s}object_id{$e} AS oid, SUM(ts_rank(to_tsvector({$s}SearchText{$e}.{$s}content{$e}), query) * {$s}SearchText{$e}.{$s}relevance{$e}) as points";
                $this->from .= ", {$s}search_texts{$s} AS {$s}SearchText{$e}, to_tsquery('" . $ts . "') query";
                $this->conditions[] = "{$s}SearchText{$e}.{$s}object_id{$e} = {$s}BEObject{$e}.{$s}id{$e} AND {$s}SearchText{$e}.{$s}lang{$e} = {$s}BEObject{$e}.{$s}lang{$e} AND {$s}SearchText{$e}.{$s}content{$e} @@ query ";
                $this->order .= $orderPrefix . "points DESC ";
                $this->group .= ", {$s}SearchText{$e}.{$s}object_id{$e}, query";
            }

        } elseif ($sType == "like") {
            $searchString = '%' . $searchString . '%';
            $this->fields .= ", {$s}SearchText{$e}.{$s}object_id{$e} AS oid, {$s}SearchText{$e}.{$s}relevance{$e}";
            $this->from .= ", {$s}search_texts{$e} AS {$s}SearchText{$e}";
            $this->conditions[] = "{$s}SearchText{$e}.{$s}object_id{$e} = {$s}BEObject{$e}.{$s}id{$e} AND " .
                "{$s}SearchText{$e}.{$s}lang{$e} = {$s}BEObject{$e}.{$s}lang{$e} AND " .
                "{$s}SearchText{$e}.{$s}content{$e} LIKE '". $searchString ."' AND {$s}SearchText{$e}.{$s}relevance{$e} > 5";
            $this->order .= $orderPrefix . "{$s}SearchText{$e}.{$s}relevance{$e} DESC ";
        }

        $this->useGroupBy = true;
    }


    /**
     * filter objects by categories
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed $value, id/name or array of ids/names
     * @return void
     */
    protected function categoryFilter($s, $e, $value) {
        if (array_key_exists('mediatype', $this->filter)) {
            return;
        }
        if (!is_array($value)) {
            $value = array($value);
        }

        $allNumeric = count(array_filter($value, 'is_numeric')) == count($value);
        $catField = $allNumeric ? 'Category.id' : 'Category.name';

        $this->addJoins(array(
            array(
                'table' => 'object_categories',
                'alias' => 'ObjectCategory',
                'type' => 'INNER',
                'conditions' => array(
                    'ObjectCategory.object_id = BEObject.id',
                ),
            ),
            array(
                'table' => 'categories',
                'alias' => 'Category',
                'type' => 'INNER',
                'conditions' => array(
                    'Category.id = ObjectCategory.category_id',
                    'NOT' => array(
                        'Category.object_type_id' => null
                    ),
                    $catField => $value
                ),
            )
        ));
    }

    /**
     * Alias for self::categoryFilter()
     *
     * @param string $s start quote sql
     * @param string $e end quote sql
     * @param mixed $value id/name or array of ids/names
     * @return void
     */
    protected function categoriesFilter($s, $e, $value) {
        $this->categoryFilter($s, $e, $value);
    }

    /**
     * filter objects by tags
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed  id/name or array of ids/names
     */
    protected function tagFilter($s, $e, $value) {
        if (!is_array($value)) {
            $value = array($value);
        }

        $allNumeric = count(array_filter($value, 'is_numeric')) == count($value);
        $catField = $allNumeric ? 'Tag.id' : 'Tag.name';

        $this->addJoins(array(
            array(
                'table' => 'object_categories',
                'alias' => 'ObjectTag',
                'type' => 'INNER',
                'conditions' => array(
                    'ObjectTag.object_id = BEObject.id',
                ),
            ),
            array(
                'table' => 'categories',
                'alias' => 'Tag',
                'type' => 'INNER',
                'conditions' => array(
                    'Tag.id = ObjectTag.category_id',
                    'Tag.object_type_id' => null,
                    $catField => $value
                ),
            )
        ));
    }

    /**
     * Alias for self::tagFilter()
     *
     * @param string $s start quote sql
     * @param string $e end quote sql
     * @param mixed $value id/name or array of ids/names
     * @return void
     */
    protected function tagsFilter($s, $e, $value) {
        $this->tagFilter($s, $e, $value);
    }

    /**
     * get RelatedObject fields
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param type $value [unused?? To verify]
     */
    protected function rel_detailFilter($s, $e, $value) {
        if (!empty($value)) {
            if (!isset($this->filter["ObjectRelation.switch"]))
                $this->filter["ObjectRelation.switch"] = "";
            $relFields = $this->model->fieldsString("BEObject", "RelatedObject");
            $this->fields .= ", " . $refFields;
            $this->from .= ", {$s}objects{$e} AS {$s}RelatedObject{$e}";
            $this->conditions[] = "{$s}ObjectRelation{$e}.{$s}object_id{$e}={$s}RelatedObject{$e}.{$s}id{$e}";
            $this->order .= ( (!empty($this->order))? "," : "" ) . "{$s}ObjectRelation{$e}.{$s}priority{$e}";
            $this->group .= ", " . $refFields;
        }
    }

    /**
     * get the object referenced from an Annotation object as Comment, EditorNote
     *
     * Example: getting a list of comment you can take also the object commented with
     *
     * $filter['object_type_id'] = Configure::read('objectTypes.comment.id'); // to filter comments
     * $filter['ref_object_details'] = 'Comment'; // to get also the object commented
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param string $value, the Annotation Model as Comment, EditorNote, ...
     */
    protected function ref_object_detailsFilter($s, $e, $value) {
        if (!empty($value)) {
            $refFields = $this->model->fieldsString("BEObject", "ReferenceObject");
            $this->fields .= ", $refFields";
            $this->from .= ", {$s}objects{$e} AS {$s}ReferenceObject{$e}";
            $this->conditions[] = $s . ClassRegistry::init($value)->alias . "{$e}.{$s}object_id{$s}={$s}ReferenceObject{$e}.{$s}id{$e}";
            $this->group .= "," . $refFields;
        }
    }

    /**
     * Filter Reference object types (used with ref_object_details filter for Annotations)
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param string $value, id or array id of of reference object types
     */
    protected function ref_object_typesFilter($s, $e, $value) {
        if (!empty($value)) {
            if(!is_array($value)) {
                $value = array($value);
            }
            $first = true;
            $in = "";
            foreach ($value as $v) {
                if(!$first) {
                    $in .= ", ";
                }
                $in .= Sanitize::escape($v);
                $first = false;
            }
            $this->conditions[] = "{$s}ReferenceObject{$e}.{$s}object_type_id{$e} IN ($in)";
        }
    }


    /**
     * filter the cards joined at a mail group (used in Newsletter module)
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param integer $value, mail_group_id
     */
    protected function mail_groupFilter($s, $e, $value) {
        $value = Sanitize::escape($value);
        $this->from .= ", {$s}mail_group_cards{$e} AS {$s}MailGroupCard{$e}";
        $this->conditions[] = "{$s}MailGroupCard{$e}.{$s}mail_group_id{$e}='" . $value . "'
                    AND {$s}MailGroupCard{$e}.{$s}card_id{$e}={$s}BEObject{$e}.{$s}id{$e}";
    }

    /**
     * get userid (username) and real name of user that has created the object
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     */
    protected function user_createdFilter($s, $e) {
        $locFields = ", {$s}User{$e}.{$s}userid{$e}, {$s}User{$e}.{$s}realname{$e}";
        $this->fields .= $locFields;
        $this->from .= ", {$s}users{$e} AS {$s}User{$e}";
        $this->conditions[] = "{$s}User{$e}.{$s}id{$e}={$s}BEObject{$e}.{$s}user_created{$e}";
        $this->group .= $locFields;
    }

    /**
     * count objects' permissions as num_of_permission
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed $value, if it's integer then count $value permission
     */
    protected function count_permissionFilter($s, $e, $value) {
        $this->fields .= ", COUNT({$s}Permission{$e}.{$s}id{$e}) AS num_of_permission";
        $from = " LEFT OUTER JOIN {$s}permissions{$e} as {$s}Permission{$e} ON {$s}Permission{$e}.{$s}object_id{$e} = {$s}BEObject{$e}.{$s}id{$e}";
        if (is_numeric($value)) {
            $from .= " AND {$s}Permission{$e}.{$s}flag{$e} = " . $value;
        }
        $this->from = $from . $this->from;
    }

    /**
     * custom property filter
     * get object_properties fields as ObjectProperty
     *
     * filter rules:
     *  - default join used is INNER JOIN. It can be overriden with 'join' key in $value
     *  - if $value is a string or a number set condition respectively to property_value or property_id
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed $value can be a string/number or an array with keys equal to object_properties table field
     */
    protected function custom_propertyFilter($s, $e, $value) {
        if (!is_array($value)) {
            $value = (is_numeric($value))? array('property_id' => $value) : array('property_value' => $value);
        }
        $defaultOptions = array('join' => 'INNER JOIN');
        $value = array_merge($defaultOptions, $value);
        $this->fields .= ", " . $this->model->fieldsString("ObjectProperty");
        $from = " " . $value['join'] . " {$s}object_properties{$e} AS {$s}ObjectProperty{$e} ON {$s}BEObject{$e}.{$s}id{$e}={$s}ObjectProperty{$e}.{$s}object_id{$e}";
        unset($value['join']);
        if (!empty($value)) {
            foreach ($value as $k => $v) {
                $v = Sanitize::escape($v);
                $from .= " AND {$s}ObjectProperty{$e}.{$s}{$k}{$e}='" . $v ."'";
            }
        }
        $this->from = $from . $this->from;
    }

    /**
     * date item filter
     * get date_items fields as DateItem
     *
     * filter rules:
     *  - default join used is INNER JOIN. It can be overriden with 'join' key in $value
     *  - if $value is a number set condition start_date
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed $value can be a number or an array with keys equal to date_items table field
     */
    protected function date_itemFilter($s, $e, $value) {
        if (!is_array($value)) {
            $value = array('start_date' => $value);
        }
        $defaultOptions = array('join' => 'INNER JOIN');
        $value = array_merge($defaultOptions, $value);
        $this->fields .= ", " . $this->model->fieldsString("DateItem");
        $from = " " . $value['join'] . "  {$s}date_items{$e} AS {$s}DateItem{$e} ON {$s}BEObject{$e}.{$s}id{$e}={$s}DateItem{$e}.{$s}object_id{$e}";
        unset($value['join']);
        if (!empty($value)) {
            foreach ($value as $k => $v) {
                $v = Sanitize::escape($v);
                $from .= " AND {$s}DateItem{$e}.{$s}{$k}{$e}='" . $v ."'";
            }
        }
        $this->from = $from . $this->from;
    }

    /**
     * count relation filter
     * If 'object_type_id' specified in BuildFilter::filter then get relations for that object type/s
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param mixed $value, relation name or array of relations (object_relations.switch field)
     *
     *      Example:
     *
     *          $value = "seealso" => add count of seealso relation as 'num_of_relations_seealso'
     *          $value = array("seealso", "download") => add count of seealso relation as 'num_of_relations_seealso'
     *                                                   and count of download relation as 'num_of_relations_download'
     */
    protected function count_relationsFilter($s, $e, $value) {
        if (!is_array($value)) {
            $value = array($value);
        }
        if (!empty($this->filter["object_type_id"])) {
            $object_type_id = $this->filter["object_type_id"];
        } elseif (!empty($this->filter["BEObject.object_type_id"])) {
            $object_type_id = $this->filter["BEObject.object_type_id"];
        }
        foreach ($value as $relation) {
            $relation = Sanitize::escape($relation);
            $numOf =  "num_of_relations_" . $relation;
            $alias = "Relation" . Inflector::camelize($relation);
            $this->fields .= ", " . $numOf;  // Issue #541.
            $from = " LEFT OUTER JOIN (
                        SELECT DISTINCT {$s}BEObject{$e}.{$s}id{$e}, COUNT({$s}{$alias}{$e}.{$s}id{$e}) AS " . $numOf ."
                        FROM {$s}objects{$e} AS {$s}BEObject{$e}
                        LEFT OUTER JOIN {$s}object_relations{$e} as {$s}{$alias}{$e} ON {$s}{$alias}{$e}.{$s}id{$e} = {$s}BEObject{$e}.{$s}id{$e} AND {$s}{$alias}{$e}.{$s}switch{$e} = '{$relation}'
                    ";

            if (!empty($object_type_id)) {
                $from .= (is_array($object_type_id))? "WHERE {$s}BEObject{$e}.{$s}object_type_id{$e} IN (" . implode(",", $object_type_id) . ")" : "WHERE {$s}BEObject{$e}.{$s}object_type_id{$e}=".$object_type_id;
            }

            $from .= " GROUP BY {$s}BEObject{$e}.{$s}id{$e}
                    ) AS {$s}{$alias}{$e} ON {$s}{$alias}{$e}.{$s}id{$e} = {$s}BEObject{$e}.{$s}id{$e}";


            $this->from = $from . $this->from;
        }
    }

    /**
     * filter objects allowed to user
     *
     * @param string $s, start quote sql
     * @param string $e, end quote sql
     * @param  string $userid userid (users.userid field)
     */
    protected function allowed_to_userFilter($s, $e, $userid) {
        $user = ClassRegistry::init('User')->find('first', array(
            'conditions' => array('User.userid' => $userid),
            'contain' => array('Group')
        ));
        $userGroups = Set::combine($user, 'Group.{n}.name', 'Group.{n}.id');

        if (!empty($userGroups) && !in_array('administrator', array_keys($userGroups))) {
            $backendPrivatePerms = Configure::read('objectPermissions.backend_private');
            $permission = ClassRegistry::init('Permission');
            $allowedObjIds = $permission->find('list', array(
                'fields' => array('object_id'),
                'conditions' => array(
                    'Permission.flag' => $backendPrivatePerms,
                    'Permission.ugid' => $userGroups,
                    'Permission.switch' => 'group'
                )
            ));

            $permission->bindModel(array(
                'belongsTo' => array('BEObject' => array('className' => 'BEObject', 'foreignKey' => 'object_id'))
            ));
            // forbidden objects on which user can't access
            $forbiddenObjects = $permission->find('all', array(
                'fields' => array('object_id', 'BEObject.object_type_id'),
                'conditions' => array(
                    'Permission.switch' => 'group',
                    'Permission.flag' => $backendPrivatePerms,
                    'NOT' => array('Permission.object_id' => $allowedObjIds)
                ),
                'contain' => array('BEObject')
            ));

            if (!empty($forbiddenObjects)) {
                $forbiddenObjectsIds = array();
                $forbiddenPub = array();
                $forbiddenSection = array();
                $sectionTypeId = Configure::read('objectTypes.section.id');
                $pubTypeId = Configure::read('objectTypes.area.id');
                foreach ($forbiddenObjects as $item) {
                    $forbiddenObjectsIds[] = $item['BEObject']['id'];
                    if ($item['BEObject']['object_type_id'] == $sectionTypeId) {
                        $forbiddenSection[] = $item['BEObject']['id'];
                    } elseif ($item['BEObject']['object_type_id'] == $pubTypeId) {
                        $forbiddenPub[] = $item['BEObject']['id'];
                    }
                }

                if (!empty($forbiddenPub) || !empty($forbiddenSection)) {

                    $query = "SELECT {$s}Tree{$e}.{$s}id{$e}
                             FROM {$s}trees{$e} AS {$s}Tree{$e}
                             WHERE {$s}BEObject{$e}.{$s}id{$e} = {$s}Tree{$e}.{$s}id{$e}";

                    if (!empty($forbiddenPub)) {
                        $forbiddenPubList = implode(',', $forbiddenPub);
                        $query .= " AND {$s}Tree{$e}.{$s}area_id{$e} NOT IN (" . $forbiddenPubList .")";
                    }

                    if (!empty($forbiddenSection)) {
                        $forbiddenSectionCondition = "";
                        foreach ($forbiddenSection as $key => $forbiddenId) {
                            if ($key > 0) {
                                $forbiddenSectionCondition .= " AND ";
                            }
                            $forbiddenSectionCondition .= "{$s}Tree{$e}.{$s}object_path{$e} NOT LIKE '%/$forbiddenId/%'";
                        }
                        $query .= " AND (" . $forbiddenSectionCondition . ")";
                    }

                    // get only objects not in tree
                    // or objects inside no private publication and/or inside no private section
                    $this->conditions[] = "(
                        (
                            NOT EXISTS (
                                SELECT {$s}Tree{$e}.{$s}id{$e}
                                FROM {$s}trees{$e} AS {$s}Tree{$e}
                                WHERE {$s}Tree{$e}.{$s}id{$e} = {$s}BEObject{$e}.{$s}id{$e}
                            )
                        )
                        OR
                        (
                            EXISTS (" . $query . ")
                        )
                    )";

                }

                // get only objects not forbidden
                $forbiddenObjectsList = implode(',', $forbiddenObjectsIds);
                $this->conditions[] = "{$s}BEObject{$e}.{$s}id{$e} NOT IN (" . $forbiddenObjectsList .")";
            }
        }
    }

    /**
     * Filter editorial contents.
     *
     * If `$value` is true it filters by `objects.publisher` not null and not empty
     * If `$value` is false it filters by `objects.publisher` null or empty
     *
     * @param string $s Start quote sql
     * @param string $e End quote sql
     * @param  string $value The filter
     * @return void
     */
    protected function editorialFilter($s, $e, $value) {
        $field = "{$s}BEObject{$e}.{$s}publisher{$e}";
        if (((bool)$value && $value !== 'false')) {
            $this->conditions[] = "($field IS NOT NULL AND $field <> '')"; 
        } else {
            $this->conditions[] = "($field IS NULL || $field = '')";;
        }
    }
}
