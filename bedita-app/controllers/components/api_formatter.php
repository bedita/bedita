<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2014 ChannelWeb Srl, Chialab Srl
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
 * Format data to be consumed by client
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
        'stats_code',
        'stats_provider',
        'stats_provider_url',
        'ip_created',
        'Category' => array(
            'object_type_id',
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
            'canonicalPath' => 'underscoreField'
        )
    );

    /**
     * Initialize function
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller, array $settings = array()) {
        $this->controller = $controller;
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
                        $item[$newField] = $item[$field];
                        unset($item[$field]);
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
                                    $datetime = new DateTime($item[$field]);
                                    $item[$field] = $datetime->format(DateTime::ISO8601);
                                }
                                break;

                            case 'underscoreField':
                                $newField = Inflector::underscore($field);
                                $item[$newField] = $item[$field];
                                unset($item[$field]);
                                break;
                        }
                    }
                }
            }
        }
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
     * Transform a BEdita object type casting fields to the right type
     * Use BEAppObjectModel::apiTransformer() to get the transformer and merge it with self::transformers['object']
     *
     * The transformer is cached
     *
     * @param array &$object
     * @return void
     */
    public function transformObject(array &$object) {
        $Object = ClassRegistry::init($object['object_type']);
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
        $this->transformItem($transformer, $object);
    }

    /**
     * Given an object return the formatted data ready for api response
     *
     * The $result must be located in 'data' key of api response.
     * It's in the form
     *
     * ```
     * 'object' => array(...), // object data
     * 'related' => array(...) // related object data
     * ```
     *
     * @param array $object representation of a BEdita object
     * @param array $options
     * @return array
     */
    public function formatObject(array $object, $options = array()) {
        $this->cleanObject($object);
        $this->transformObject($object);
        $result = array('object' => $object, 'related' => array());
        if (!empty($object['relations'])) {
            foreach ($object['relations'] as $relation => $relatedObjects) {
                $result['object']['relations'][$relation] = array();
                foreach ($relatedObjects as $relObj) {
                    $result['object']['relations'][$relation][] = array(
                        'id_right' => (int) $relObj['id'],
                        'params' => $relObj['params'],
                        'priority' => (int) $relObj['priority']
                    );
                    $relObjFormatted = $this->formatObject($relObj, $options);
                    $result['related'][$relObj['id']] = $relObjFormatted['object'];
                    if (!empty($relObjFormatted['related'])) {
                        $result['related'] += $relObjFormatted['related'];
                    }
                }
            }
        }

        // format children
        if (!empty($object['children'])) {
            $result['object']['children'] = array(
                'contents' => array(),
                'sections' => array()
            );

            foreach (array('contents', 'sections') as $type) {
                $typeKey = 'child' . ucfirst($type);
                if (!empty($object['children'][$typeKey])) {
                    foreach ($object['children'][$typeKey] as $child) {
                        $result['object']['children'][$type][] = (int) $child['id'];
                        $typeFormatted = $this->formatObject($child, $options);
                        $result['related'][$child['id']] = $typeFormatted['object'];
                        if (!empty($typeFormatted['related'])) {
                            $result['related'] += $typeFormatted['related'];
                        }
                    }
                    unset($object['children'][$typeKey]);
                }
            }
        }
        return $result;
    }

    /**
     * Given an array of objects return the formatted data ready for api response
     * Iteratively call self::formatObject() on every object of the list
     *
     * ```
     * 'objects' => array(...), // object data
     * 'related' => array(...) // related object data
     * ```
     *
     * @see self::formatObject()
     * @param array $objects array of BEdita objects
     * @param array $options
     * @return array
     */
    public function formatObjects(array $objects, $options = array()) {
        $result = array('objects' => array(), 'related' => array());
        foreach ($objects as $obj) {
            $objectFormatted = $this->formatObject($obj, $options);
            $result['objects'][] = $objectFormatted['object'];
            $result['related'] += $objectFormatted['related'];
        }
        return $result;
    }

    /**
     * Starting from BEdita toolbar it returns the paging item as
     *
     * ```
     * 'page' => int, // the current page
     * 'totalPages' => int, // the total number of pages
     * 'total' => int, // the total number of items
     * 'limit' => int|null // the maximum number of items in the response
     * ```
     *
     * @param array $toolbar
     * @return array
     */
    public function formatPaging(array $toolbar) {
        if (empty($toolbar)) {
            return array();
        }
        $paging = array(
            'page' => (int) $toolbar['page'],
            'total_pages' => (int) $toolbar['pages'],
            'total' => (int) $toolbar['size'],
            'limit' => (!empty($toolbar['dim'])) ? (int) $toolbar['dim'] : null
        );
        return $paging;
    }

    /**
     * Clean BEdita object array from useless fields
     * Use self::objectFieldsToRemove()
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
            } elseif (isset($object[$value])) {
                unset($object[$value]);
            }
        }
    }

}
