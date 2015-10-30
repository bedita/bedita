<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2014-2015 ChannelWeb Srl, Chialab Srl
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
 * ApiFormatter class
 *
 * Format data to be consumed by client or to be saved
 *
 */
class ApiFormatterComponent extends Object {

    /**
     * Controller instance
     *
     * @var Controller
     */
    public $controller = null;

    /**
     * Components used
     *
     * @var array
     */
    public $components = array('ApiValidator');

    /**
     * Fields that must be removed from object/s
     *
     * @var array
     */
    protected $objectFieldsToRemove = array(
        'UserCreated',
        'ObjectProperty',
        'ObjectType',
        'RelatedObject',
        'bindings',
        'fixed',
        'valid',
        'ip_created',
        'pathSection',
        // areas
        'stats_code',
        'stats_provider',
        'stats_provider_url',
        // sections
        'syndicate',
        'priority_order',
        'last_modified',
        'map_priority',
        'map_changefreq',
        // trees fields
        'area_id',
        'object_path',
        'priority',
        'menu',
        'Category' => array(
            'id',
            'object_type_id',
            'area_id',
            'status',
            'priority',
            'parent_id',
            'parent_path',
            'url_label'
        ),
        'Tag' => array(
            'id',
            'object_type_id',
            'area_id',
            'status',
            'priority',
            'parent_id',
            'parent_path',
            'url_label'
        ),
        'DateItem' => array(
            'object_id',
            'params'
        )
    );

    /**
     * Contain fields transformation as `field` => `type`
     * It used for cast string in other types.
     * Some special types act on fields instead of values. See below.
     *
     * Possible types values are:
     *
     * - `date`
     * - `datetime`
     * - `integer`
     * - `float`
     * - `boolean`
     *
     * Special types:
     *
     * - `underscoreField` underscorize field. Note that the value of field remains unchanged
     * - `integerArray` cast to integer all array values
     *
     *
     * The `object` key contains transformation merged with all BEdita objects
     *
     * @see self::transformObject(), self::transformItem()
     * @var array
     */
    protected $transformers = array(
        'object' => array(
            'publication_date' => 'datetime',
            'customProperties' => 'underscoreField',
            'canonicalPath' => 'underscoreField',
            'parentAuthorized' => 'underscoreField'
        )
    );

    protected $urlParams = array();

    /**
     * Initialize function
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller, array $settings = array()) {
        $this->controller = &$controller;
        if (isset($settings['objectFieldsToRemove']) && is_array($settings['objectFieldsToRemove'])) {
            $this->objectFieldsToRemove($settings['objectFieldsToRemove'], true);
        }

        $confFields = Configure::read('api.formatting.fields');
        if (!empty($confFields)) {
            $this->objectFieldsToRemove($confFields);
        }
    }

    /**
     * Call without parameters to return the acutal self::objectFieldsToRemove
     * Pass paramteres to setup new self::objectFieldsToRemove and return it
     *
     * If $override is true $confFields replaces self::objectFieldsToRemove
     *
     * If $override is false (default) then $confFields has to be an array like
     *
     * ```
     * array(
     *     'remove' => array(
     *         'fieldToRemove_1',
     *         'fieldToRemove_2',
     *         'fieldOnWhichRemoveFields' => array(
     *             'fieldToRemove_3',
     *             'fieldToRemove_4'
     *         )
     *     ),
     *     'keep' => array(
     *         'fieldToKeep_1',
     *         'fieldToKeep_2',
     *         'fieldOnWhichKeepFields' => array(
     *             'fieldToKeep_3'
     *         )
     *     )
     * )
     * ```
     *
     * All fields in 'remove' will be added to self::objectFieldsToRemove
     * All fields in 'keep' will be removed from self::objectFieldsToRemove
     *
     * @param array $confFields
     * @param boolean $override
     * @return array
     */
    public function objectFieldsToRemove(array $confFields = array(), $override = false) {
        if ($override) {
            $this->objectFieldsToRemove = $confFields;
            return $this->objectFieldsToRemove;
        }

        if (empty($confFields)) {
            return $this->objectFieldsToRemove;
        }

        // add fields to remove
        if (isset($confFields['remove']) && is_array($confFields['remove'])) {
            foreach ($confFields['remove'] as $key => $field) {
                if (is_array($field)) {
                    if (is_string($key)) {
                        if (isset($this->objectFieldsToRemove[$key])) {
                            $this->objectFieldsToRemove[$key] = array_unique(array_merge($this->objectFieldsToRemove[$key], $field));
                        } else {
                            $this->objectFieldsToRemove[$key] = $field;
                        }
                    }
                } else {
                    if (isset($this->objectFieldsToRemove[$field])) {
                        unset($this->objectFieldsToRemove[$field]);
                    }
                    if (!in_array($field, $this->objectFieldsToRemove)) {
                        $this->objectFieldsToRemove[] = $field;
                    }
                }
            }
        }

        // keep fields
        if (isset($confFields['keep']) && is_array($confFields['keep'])) {
            foreach ($confFields['keep'] as $key => $field) {
                if (is_array($field)) {
                    if (is_string($key)) {
                        if (isset($this->objectFieldsToRemove[$key])) {
                            $this->objectFieldsToRemove[$key] = array_values(
                                array_diff($this->objectFieldsToRemove[$key], $field)
                            );
                        }
                    }
                } else {
                    $found = array_search($field, $this->objectFieldsToRemove);
                    if ($found !== false) {
                        unset($this->objectFieldsToRemove[$found]);
                    } elseif (isset($this->objectFieldsToRemove[$field])) {
                        unset($this->objectFieldsToRemove[$field]);
                    }
                }
            }
        }

        return $this->objectFieldsToRemove;
    }

    /**
     * Transform the item passed using a transformer
     * The transformer must be an array of 'fields' => 'type' or 'key' => array('field1' => 'type') for example
     *
     * ```
     * array(
     *     'id' => 'integer',
     *     'start_date' => 'datetime',
     *     'GeoTag' => array(
     *         'id' => 'integer',
     *         ...
     *     )
     * )
     * ```
     *
     * The keys that correspond to array as `GeoTag` will be underscorized and pluralized.
     * So `GeoTag` become `geo_tags` in the $item array
     *
     * @see self::transformers comments to all 'type' possibility
     * @param array $transformer the transformer array
     * @param array &$item the item to transform
     * @return void
     */
    protected function transformItem(array $transformer, array &$item) {
        if (!empty($item[0]) && is_array($item[0])) {
            foreach ($item as &$i) {
                $this->transformItem($transformer, $i);
            }
        } else {
            foreach ($transformer as $field => $type) {
                if (isset($item[$field])) {
                    if (is_array($type)) {
                        // underscore and pluralize $field
                        $newField = Inflector::pluralize(Inflector::underscore($field));
                        if ($newField != $field) {
                            $item[$newField] = $item[$field];
                            unset($item[$field]);
                        }
                        if (is_array($item[$newField])) {
                            $this->transformItem($transformer[$field], $item[$newField]);
                        }
                    } else {
                        switch ($type) {
                            case 'integer':
                                $item[$field] = (int) $item[$field];
                                break;

                            case 'float':
                                $item[$field] = (float) $item[$field];
                                break;

                            case 'boolean':
                                $item[$field] = (bool) $item[$field];
                                break;

                            case 'date':
                            case 'datetime':
                                if (!empty($item[$field])) {
                                    $item[$field] = $this->dateFromDb($item[$field]);
                                }
                                break;

                            case 'underscoreField':
                                $newField = Inflector::underscore($field);
                                $item[$newField] = $item[$field];
                                unset($item[$field]);
                                break;

                            case 'integerArray':
                                if (is_array($item[$field])) {
                                    $item[$field] = array_map('intval', $item[$field]);
                                }
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Convert a date from db to ISO-8601 format
     * Use DateTime::ATOM format i.e. 2005-08-15T15:52:01+00:00
     *
     * @param string $date the date string to convert
     * @return string
     */
    public function dateFromDb($date) {
        $dateTime = new DateTime($date);
        return $dateTime->format(DateTime::ATOM);
    }

    /**
     * Convert a date from ISO-8601 to $dbFormat
     * The format supported are:
     * - 2005-08-15T15:52:01+02:00
     * - 2005-08-15T13:52:01.467Z (js Date().toISOString())
     *
     * @param string $date the ISO-8601 date string
     * @param string $dbFormat the db format (default 'datetime' db type)
     * @return string
     */
    public function dateToDb($date, $dbFormat = 'Y-m-d H:i:s') {
        $dateTime = $this->ApiValidator->checkDate($date);
        return $dateTime->format($dbFormat);
    }

    /**
     * Transform an item using a transformer from self::transformers
     *
     * @param string $subject the transformer name to use
     * @param array &$item the item to transform
     * @return void
     */
    public function transform($subject, array &$item) {
        if (!empty($this->transformers[$subject])) {
            $this->transformItem($this->transformers[$subject], $item);
        }
    }

    /**
     * Return the BEdita object transformer
     * Used to know the fields to cast and the type
     *
     * @param array $object the BEdita object
     * @return array
     */
    public function getObjectTransformer(array $object) {
        $objectType = !empty($object['object_type']) ? $object['object_type'] : $object['object_type_id'];
        $modelName = Configure::read('objectTypes.' . $objectType . '.model');
        $Object = ClassRegistry::init($modelName);
        $modelName = $Object->name;
        $transformer = array();
        if (!isset($this->transformers[$modelName])) {
            $cacheName = 'apiTransformer' . $modelName;
            $debugMode = Configure::read('debug');
            if (!$debugMode) {
                $transformer = Cache::read($cacheName);
            }
            if (empty($transformer)) {
                $transformer = $Object->apiTransformer();
                $transformer = array_merge($this->transformers['object'], $transformer);
                if (!$debugMode) {
                    Cache::write($cacheName, $transformer);
                }
            }
            // add transformer to self::transformers to reuse it in case
            $this->transformers[$modelName] = $transformer;
        } else {
            $transformer = $this->transformers[$modelName];
        }
        return $transformer;
    }

    /**
     * Transform a BEdita object type casting fields to the right type
     * Use BEAppObjectModel::apiTransformer() to get the transformer and merge it with self::transformers['object']
     *
     * The transformer is cached
     *
     * @param array &$object
     * @return void
     */
    public function transformObject(array &$object) {
        $transformer = $this->getObjectTransformer($object);
        $this->transformItem($transformer, $object);
    }

    /**
     * Prepare self::$transformer['object'] adding 'custom_properties' formatting info
     * It is expected that $object contains the 'ObjectProperty' with custom properties details
     *
     * @param array $object the object on which prepare the custom properties transformer
     */
    public function setCustomPropertiesTransformer(array $object) {
        $this->transformers['object']['custom_properties'] = array();
        if (!empty($object['ObjectProperty'])) {
            foreach ($object['ObjectProperty'] as $name => $customProp) {
                if ($customProp['property_type'] == 'number') {
                    $this->transformers['object']['custom_properties'][$name] = 'float';
                } elseif ($customProp['property_type'] == 'date') {
                    $this->transformers['object']['custom_properties'][$name] = 'date';
                }
            }
        }
    }

    /**
     * Count $object relations and return a formatted array as
     *
     * ```
     * array(
     *     'attach' => array(
     *         'count' => 8,
     *         'url' => 'https://example.com/api/v1/objects/1/relations/attach'
     *     ),
     *     'seealso' => array(
     *         'count' => 2,
     *         'url' => 'https://example.com/api/v1/objects/1/relations/seealso'
     *     )
     * )
     * ```
     *
     * @param array $object the object on which to count the relations
     * @return array
     */
    public function formatRelationsCount(array $object) {
        $relations = array();
        $objectRelation = ClassRegistry::init('ObjectRelation');
        // count all relations
        $countRel = $objectRelation->find('all', array(
            'fields' => array('COUNT(ObjectRelation.id) as count', 'ObjectRelation.switch'),
            'conditions' => array('ObjectRelation.id' => $object['id']),
            'group' => 'ObjectRelation.switch',
            'joins' => array(
                array(
                    'table' => 'objects',
                    'alias' => 'BEObject',
                    'type' => 'inner',
                    'conditions' => array(
                        'ObjectRelation.object_id = BEObject.id',
                        'BEObject.status' => $this->controller->getStatus()
                    )
                )
            )
        ));

        // count not accessible relations
        $permission = ClassRegistry::init('Permission');
        $user = $this->controller->ApiAuth->getUser();
        $countForbidden = $permission->relatedObjectsNotAccessibile(
            $object['id'],
            array(
                'count' => true,
                'status' => $this->controller->getStatus()
            ),
            $user
        );

        $url = $this->controller->baseUrl() . '/objects/' . $object['id']  . '/relations/';
        if (!empty($countRel)) {
            foreach ($countRel as $cDetail) {
                $count = $cDetail[0]['count'];
                $switch = $cDetail['ObjectRelation']['switch'];
                if (isset($countForbidden[$switch])) {
                    $count -= $countForbidden[$switch];
                }
                $relations[$switch] = array(
                    'count' => (int) $count,
                    'url' => $url . $switch
                );
            }
        }
        return $relations;
    }

    /**
     * Count $object children and return a formatted array as
     *
     * ```
     * array(
     *     'count' => 14, // total children
     *     'url' => 'https://example.com/api/v1/objects/1/children',
     *     'contents' => array(
     *         'count' => 12, // contents children
     *         'url' => 'https://example.com/api/v1/objects/1/contents'
     *     ),
     *     'sections' => array(
     *         'count' => 2, // sections children
     *         'url' => 'https://example.com/api/v1/objects/1/sections'
     *     )
     * )
     * ```
     *
     * @param array $object the object on which to count children
     * @return array
     */
    public function formatChildrenCount(array $object) {
        $tree = ClassRegistry::init('Tree');
        $options = array(
            'conditions' => array('BEObject.status' => $this->controller->getStatus()),
            'joins' => array()
        );
        $countContents = $tree->countChildrenContents($object['id'], $options);
        $countSections = $tree->countChildrenSections($object['id'], $options);

        $permissionJoin = array(
            'table' => 'permissions',
            'alias' => 'Permission',
            'type' => 'inner',
            'conditions' => array(
                'Permission.object_id = Tree.id',
                'Permission.flag' => Configure::read('objectPermissions.frontend_access_with_block'),
                'Permission.switch' => 'group',
            )
        );
        $options['joins'][] = $permissionJoin;
        $countContentsForbidden = $tree->countChildrenContents($object['id'], $options);
        $countSectionsForbidden = $tree->countChildrenSections($object['id'], $options);

        $user = $this->controller->ApiAuth->getUser();
        if (!empty($user)) {
            $permissionJoin['conditions']['NOT'] = array('Permission.ugid' => $user['groupsIds']);
            $countContentsForbidden -= $tree->countChildrenContents($object['id'], $options);
            $countSectionsForbidden -= $tree->countChildrenSections($object['id'], $options);
        }

        $countContents -= $countContentsForbidden;
        $countSections -= $countSectionsForbidden;
        $countChildren = $countContents + $countSections;
        $url = $this->controller->baseUrl() . '/objects/' . $object['id'] . '/';

        if ($countChildren == 0) {
            return array();
        }

        $result = array(
            'count' => (int) $countChildren,
            'url' => $url . 'children'
        );
        if ($countContents > 0) {
            $result['contents'] = array(
                'count' => (int) $countContents,
                'url' => $url . 'contents'
            );
        }
        if ($countSections > 0) {
            $result['sections'] = array(
                'count' => (int) $countSections,
                'url' => $url . 'sections'
            );
        }
        return $result;
    }

    /**
     * Given an object return the formatted data ready for api response
     *
     * The $result is normally located in 'data' key of api response
     * and it's in the form
     *
     * ```
     * 'object' => array(...) // object data
     * ```
     *
     * $options is used to personalize the object formatted.
     * Possible values are:
     *
     * - 'countRelations' (default false) to add a count of relations with url to reach them
     * - 'countChildren' (default false) to add a count of children with url to reach them
     *
     * @param array $object representation of a BEdita object
     * @param array $options
     * @return array
     */
    public function formatObject(array $object, $options = array()) {
        $options += array('countRelations' => false, 'countChildren' => false);
        $object['object_type'] = Configure::read('objectTypes.' . $object['object_type_id'] . '.name');
        // adjust 'uri' in multimedia objects
        $multimediaObjectTypeIds = Configure::read('objectTypes.multimedia.id');
        if (in_array($object['object_type_id'], $multimediaObjectTypeIds)) {
            if (!empty($object['uri']) && filter_var($object['uri'], FILTER_VALIDATE_URL) === false) {
                $object['uri'] = Configure::read('mediaUrl') . $object['uri'];
            }
        }
        // before clean prepare custom properties transformer
        $this->setCustomPropertiesTransformer($object);
        //debug($this->transformers);exit;
        $this->cleanObject($object);
        $this->transformObject($object);
        if ($options['countRelations']) {
            $object['relations'] = $this->formatRelationsCount($object);
        }
        if ($options['countChildren']) {
            $branches = array(
                Configure::read('objectTypes.area.id'),
                Configure::read('objectTypes.section.id')
            );
            if (in_array($object['object_type_id'], $branches)) {
                $object['children'] = $this->formatChildrenCount($object);
            }
        }
        return array('object' => $object);
    }

    /**
     * Given an array of objects return the formatted data ready for api response
     * Iteratively call self::formatObject() on every object of the list
     *
     * ```
     * 'objects' => array(...), // object data
     * ```
     *
     * $options is used to personalize the object formatted.
     *
     * @see self::formatObject()
     * @param array $objects array of BEdita objects
     * @param array $options
     * @return array
     */
    public function formatObjects(array $objects, $options = array()) {
        $result = array('objects' => array());
        foreach ($objects as $obj) {
            $objectFormatted = $this->formatObject($obj, $options);
            $result['objects'][] = $objectFormatted['object'];
        }
        return $result;
    }

    /**
     * Starting from BEdita toolbar it returns the paging item as
     *
     * ```
     * 'page' => int, // the current page
     * 'page_size' => int|null, // the maximum number of items in the response
     * 'page_count' => int, // the total number of items in the page
     * 'total' => int, // the total number of items
     * 'total_pages' => int // the total number of pages
     * ```
     *
     * @param array $toolbar
     * @return array
     */
    public function formatPaging(array $toolbar) {
        if (empty($toolbar)) {
            return array();
        }
        $pageCount = ($toolbar['end'] > 0) ? $toolbar['end'] - $toolbar['start'] + 1 : $toolbar['size'];
        $paging = array(
            'page' => (int) $toolbar['page'],
            'page_size' => (!empty($toolbar['dim'])) ? (int) $toolbar['dim'] : null,
            'page_count' => (int) $pageCount,
            'total' => (int) $toolbar['size'],
            'total_pages' => (int) $toolbar['pages']
        );
        return $paging;
    }

    /**
     * Clean BEdita object array from useless fields
     * Use self::objectFieldsToRemove
     *
     * @param array &$object
     * @return void
     */
    public function cleanObject(array &$object) {
        foreach ($this->objectFieldsToRemove as $key => $value) {
            if (is_array($value)) {
                $fieldsToRemove = array_flip($value);
                if (!empty($object[$key][0])) {
                    foreach ($object[$key] as &$detail) {
                        $detail = array_diff_key($detail, $fieldsToRemove);
                    }
                } elseif (isset($object[$key])) {
                    $object[$key] = array_diff_key($object[$key], $fieldsToRemove);
                }
            } elseif (array_key_exists($value, $object)) {
                unset($object[$value]);
            }
        }
    }

    /**
     * Arrange $object data to save
     *
     * - clean fields
     * - transform date ISO8601 in SQL format
     *
     * @param array $object the $object data to save
     * @return array
     */
    public function formatObjectForSave(array $object) {
        if (!empty($object['relations'])) {
            $object['RelatedObject'] = $this->formatRelationsForSave($object['relations']);
            unset($object['relations']);
        }
        if (!empty($object['categories'])) {
            $object['Category'] = $this->formatCategoriesForSave($object['categories'], $object['object_type_id']);
            unset($object['categories']);
        }
        if (!empty($object['tags'])) {
            $tags = $this->formatTagsForSave($object['tags']);
            $object['Category'] = (!empty($object['Category'])) ? array_merge($object['Category'], $tags) : $tags;
            unset($object['tags']);
        }
        if (!empty($object['geo_tags'])) {
            $object['GeoTag'] = $object['geo_tags'];
            unset($object['geo_tags']);
        }
        if (!empty($object['date_items'])) {
            $object['DateItem'] = $this->formatDateItemsForSave($object['date_items']);
            unset($object['date_items']);
        }

        $transformer = $this->getObjectTransformer($object);
        foreach ($object as $key => $value) {
            if (array_key_exists($key, $transformer)) {
                if ($transformer[$key] == 'date') {
                    $object[$key] = $this->dateToDb($value, 'Y-m-d');
                } elseif ($transformer[$key] == 'datetime') {
                    $object[$key] = $this->dateToDb($value, 'Y-m-d H:i:s');
                }
            }
        }
        return $object;
    }

    /**
     * Arrange relations data to save.
     * The data returned are suitable to saving an object
     *
     * The $relations array has to be in the form
     * ```
     * array(
     *     'attach' => array(
     *         array(
     *             'related_id' => 1,
     *             ...
     *         ),
     *         array(...)
     *     ),
     *     'seealso' => array(...)
     * )
     * ```
     *
     * @param array $relations array of relations
     * @return array
     */
    public function formatRelationsForSave(array $relations) {
        $relationsFormatted = array();
        foreach ($relations as $name => $relList) {
            $r = array(
                0 => array('switch' => $name)
            );
            foreach ($relList as $key => $relData) {
                $r[$relData['related_id']]['id'] = $relData['related_id'];
                $r[$relData['related_id']]['priority'] = empty($relData['priority']) ? $key + 1 : $relData['priority'];
                if (!empty($relData['params'])) {
                    $r[$relData['related_id']]['params'] = $relData['params'];
                }
            }
            $relationsFormatted[$name] = $r;
        }
        return $relationsFormatted;
    }

    /**
     * Arrange categories data for save.
     * The data returned are suitable to saving an object.
     * Return an array of ids
     *
     * @param array $categories an array of category names
     * @param int $objectTypeId the object type id
     * @return array
     */
    public function formatCategoriesForSave(array $categories, $objectTypeId = null) {
        $categoryModel = ClassRegistry::init('Category');
        $categoryModel->Behaviors->disable('CompactResult');
        $result = $categoryModel->find('list', array(
            'fields' => array('name', 'id'),
            'conditions' => array(
                'name' => $categories,
                'object_type_id' => $objectTypeId,
                'status' => $this->controller->getStatus()
            )
        ));
        $categoryModel->Behaviors->enable('CompactResult');
        return array_values($result);
    }

    /**
     * Arrange tags data for save.
     * The data returned are suitable to saving an object.
     * Return an array of ids
     *
     * @param array $tags an array of tag names
     * @return array
     */
    public function formatTagsForSave(array $tags) {
        return $this->formatCategoriesForSave($tags);
    }

    /**
     * Arrange date items for save:
     * - format 'start_date' and 'end_date'
     *
     */
    public function formatDateItemsForSave(array $dateItems) {
        foreach ($dateItems as &$item) {
            foreach ($item as $field => &$value) {
                if (($field == 'start_date' || $field == 'end_date') && !empty($value)) {
                    $value = $this->dateToDb($value, 'Y-m-d H:i:s');
                } elseif ($field == 'days') {
                    sort($value);
                }
            }
        }
        return $dateItems;
    }

    /**
     * Format $this->controller->params['url'] building array of values starting from $separator separated values.
     * By default $separator is ',' char and 'query' is excluded because it represents a full text search
     *
     * For example in a request as:
     *
     * https://example.com/objects?object_type=document,event&page=2
     *
     * the url params are formatted as
     *
     * ```
     * array(
     *     'object_type' => array('document', 'event'),
     *     'page' => 2
     * )
     * ```
     *
     * Once url params has been formatted that value is returned to every next call without parse again the url
     * unless $reset params is true
     *
     * @param string $separator the separator char that explode string in array
     * @param array $exclude the array of url params to exclude to the formatting
     * @param boolean $reset true if the url params have to be formatted again also if it had already been done
     * @return array
     */
    public function formatUrlParams($separator = ',', array $exclude = array('query'), $reset = false) {
        if (empty($this->urlParams) || $reset) {
            $this->urlParams = $this->controller->params['url'];
            array_shift($this->urlParams);
            if (!empty($this->urlParams)) {
                foreach ($this->urlParams as $name => &$value) {
                    if (is_array($value)) {
                        foreach ($value as $k => &$v) {
                            if (!in_array($k, $exclude)) {
                                $v = explode($separator, trim($v, $separator));
                            }
                        }
                    } else {
                        if (!in_array($name, $exclude)) {
                            $value = explode($separator, trim($value, $separator));
                        }
                    }
                }
            }
        }
        return $this->urlParams;
    }

}
