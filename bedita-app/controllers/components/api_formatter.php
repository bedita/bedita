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
        'bindings'
    );

    /**
     * Initialize function
     *
     * @param Controller $controller
     * @return void
     */
    public function initialize(&$controller) {
        $this->controller = $controller;
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
        $result = array('object' => $object, 'related' => array());
        if (!empty($object['relations'])) {
            foreach ($object['relations'] as $relation => $relatedObjects) {
                $result['object']['relations'][$relation] = array();
                foreach ($relatedObjects as $relObj) {
                    $result['object']['relations'][$relation][] = array(
                        'idRight' => (int) $relObj['id'],
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
