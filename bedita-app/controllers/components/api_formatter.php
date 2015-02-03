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
        'RelatedObject',
        'bindings',
        'fixed'
    );

    /**
     * Contain field transformation
     *
     * @var array
     */
    protected $transformers = array(
        'object' => array(
            'publication_date' => 'datetime',
            'customProperties' => array() // do nothing set to array to underscore/pluralize field
        )
    );

    /**
     * Initialize function
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(Controller $controller) {
        $this->controller = $controller;
    }

    /**
     * Transform the item passed using a transformer
     * The transformer must be an array of 'fields' => 'type' or 'key' => array('field1' => 'type') for example
     *
     * ```
     * array(
     *     'id' => 'integer',
     *     'start_date' => 'datetime',
     *     'Category' => array(
     *         'id' => 'integer',
     *         ...
     *     )
     * )
     * ```
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
                        $this->transformItem($transformer[$field], $item[$newField]);
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
                        }
                    }
                }
            }
        }
    }

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
        debug($Object->BEObject->getColumnTypes());exit;
        $modelName = $Object->name;
        $transformer = array();
        if (!isset($this->transformers[$modelName])) {
            // try to load from cache
            $cacheName = 'apiTransformer' . $modelName;
            $transformer = Cache::read($cacheName);
            if (empty($transformer)) {
                $transformer = $Object->apiTransformer();
                $transformer = array_merge($this->transformers['object'], $transformer);
                Cache::write($cacheName, $transformer);
            }
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
        $this->transformObject($object);
        $this->cleanObject($object);
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
            'totalPages' => (int) $toolbar['pages'],
            'total' => (int) $toolbar['size'],
            'limit' => (!empty($toolbar['dim'])) ? (int) $toolbar['dim'] : null
        );
        return $paging;
    }

    /**
     * Clean BEdita object array from useless fields
     * Use self::objectFieldsToRemove()
     *
     * @param array &$object [description]
     * @return void
     */
    public function cleanObject(array &$object) {
        $object = array_diff_key($object, array_flip($this->objectFieldsToRemove));
    }

}
