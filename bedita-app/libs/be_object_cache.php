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
 * BeObjectCache class
 *
 * Class to read/write object data using Cake Cache
 */
class BeObjectCache {

    /**
     * Base path for objects cache on filesystem
     */
    private $baseCachePath = null;

    /**
     * Default cache config
     * It's overriden in constructor if exists a cache conf named 'objects'
     */
    private $cacheConfig = array(
        'engine' => 'File',
        'prefix' => '',
        'duration' => '+24 hours'
    );

    /**
     * Constructor
     * Initialize cache config
     *
     * @param array $settings
     */
    public function __construct() {
        // init cache
        $cacheConf = Cache::settings('objects');
        if (empty($cacheConf)) {
            // default cache path if not configured
            $this->cacheConfig['path'] = BEDITA_CORE_PATH . DS . 'tmp' . DS . 'cache' . DS . 'objects';
        }
        $this->cacheConfig = $cacheConf + $this->cacheConfig;
        if (!empty($this->cacheConfig['path'])) {
            $this->baseCachePath = $this->cacheConfig['path'];
        }
    }

    public function getPathById($id) {
        $strId = "{$id}";
        if ($id < 100) {
            $strId = str_pad("{$id}", 3, '0', STR_PAD_LEFT);
        }
        return $this->baseCachePath . DS . substr($strId, strlen($strId) - 3, 3);
    }

    private function setCacheOptions($id) {
        if (!empty($this->cacheConfig['path'])) {
            $path = $this->getPathById($id);
            if (!file_exists($path)) {
                mkdir($path);
                chmod($path, 0775);
            }
            $this->cacheConfig['path'] = $path;
        }
        Cache::set($this->cacheConfig);
    }

    private function cacheName($id, array &$options, $label = null) {
        if (!empty($options['bindings_list'])) {
            $strOpt = implode('', $options['bindings_list']);
        } elseif (!empty($options)) {
            $strOpt = print_r($options, true);
        }
        $label = empty($label) ? '' : '-' . $label;
        $strOpt = (!empty($strOpt)) ? '-' . md5($strOpt) : '';
        return $id . $label . $strOpt;
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
        // use cache config if not using 'File' engine
        if ($this->cacheConfig['engine'] !== 'File') {
            $res = Cache::read($cacheName, 'objects');
        } else {
            $this->setCacheOptions($id);
            $res = Cache::read($cacheName);
        }
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
        $res = false;
        // store index cache
        if ($this->cacheConfig['engine'] !== 'File') {
            $cacheIdxKey = $id . '_index';
            $cacheIdx = Cache::read($cacheIdxKey, 'objects');
            if (empty($cacheIdx)) {
                $cacheIdx = array();
            }
            if (!in_array($cacheName, $cacheIdx)) {
                $cacheIdx[] = $cacheName;
                Cache::write($cacheIdxKey, $cacheIdx, 'objects');
            }
            $res = Cache::write($cacheName, $data, 'objects');
        } else {
            $this->setCacheOptions($id);
            $res = Cache::write($cacheName, $data);
        }
        return $res;
    }

    /**
     * Delete objects from cache
     *
     * @param  integer $id objectId
     */
    public function delete($id, array $options = null) {
        if ($this->cacheConfig['engine'] == 'File') {
            $cachePath = $this->getPathById($id);
            $wildCard = $cachePath . DS . $this->cacheConfig['prefix'] . $id . '-*';
            $toDelete = glob($wildCard);
            if (!empty($toDelete)) {
                array_map('unlink', $toDelete);
            }
        } else {
            $cacheIdxKey = $id . '_index';
            $cacheIdx = Cache::read($cacheIdxKey, 'objects');
            if (!empty($cacheIdx)) {
                foreach ($cacheIdx as $cacheName) {
                    Cache::delete($cacheName, 'objects');
                }
            }
            Cache::delete($cacheIdxKey, 'objects');
        }
    }

}
