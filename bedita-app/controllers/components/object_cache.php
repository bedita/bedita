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
 * ObjectCacheComponent class
 *
 * Component to read/write object data using Cake Cache
 */
class ObjectCacheComponent extends Object {

    /**
     * the controller that use this component
     * @var Controller
     */
    public $controller = null;


    /**
     * Base path for objects cache
     */
    private $baseCachePath = null;

    /**
     * Default cache config
     */
    private $cacheConfig = null;
    
    /**
     * Initialize component
     *
     * @param  Controller $controller
     * @param  array  $settings
     */
    public function initialize($controller) {
        $this->controller = $controller;
        // init cache
        $cacheConf = Cache::settings('objects');
        if (empty($cacheConf)) {
            // default cache path and settings if not configured
            $this->baseCachePath = BEDITA_CORE_PATH . DS . 'tmp' . DS . 'cache' . DS . 'objects';
            $this->cacheConfig = array(
                'engine' => 'File',
                'duration' => '+2 hours',
                'path' => $this->baseCachePath
            );
        } else {
            $this->cacheConfig = $cacheConf['objects'];
            if (!empty($this->cacheConfig['path'])) {
                $this->baseCachePath = $this->cacheConfig['path'];
            }
        }
    }


    private function setCacheOptions($id) {
        if (!empty($this->cacheConfig['path'])) {
            $strId = "{$id}";
            if ($id < 100) {
                $strId = str_pad("{$id}", 3, '0', STR_PAD_LEFT);
            }
            $path = $this->baseCachePath . DS . substr($strId, strlen($strId) - 3, 3);
            if (!file_exists($path)) {
                mkdir($path);
            }
            $this->cacheConfig['path'] = $path;
        }
        Cache::set($this->cacheConfig);
    }

    private function cacheName($id, array &$options, $label = null) {
        if (!empty($options['bindings_list'])) {
            $strOpt = implode('', $options['bindings_list']);
        } else {
            $strOpt = print_r($options, true);
        }
        $label = empty($label) ? '' : $label . '-';
        return $id . '-' . $label . md5($strOpt);
    }

    /**
     * Read object from cache
     *
     * @param  int $id
     * @param  array $options
     * @return data array or false if no cache is found
     */
    public function read($id, array &$options, $label = null) {
        $res = false;
        $cacheName = $this->cacheName($id, $options, $label);
        $this->setCacheOptions($id);
        $res = Cache::read($cacheName);
        return $res;
    }

    /**
     * Write object data to cache
     *
     * @param  string $key
     * @return array
     */
    public function write($id, array &$options, array &$data, $label = null) {
        $cacheName = $this->cacheName($id, $options, $label);
        $this->setCacheOptions($id);
        return Cache::write($cacheName, $data);
    }

    /**
     * Delete object from cache
     *
     * @param  string $key
     * @return array
     */
    public function delete($id, array $options = null) {
        $cacheName = $this->cacheName($id, $options);
        $this->setCacheOptions($id);
        return Cache::delete($cacheName);
    }

}