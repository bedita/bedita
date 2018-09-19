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
     * Base path for objects cache on filesystem.
     *
     * @var string|null
     */
    private $baseCachePath = null;

    /**
     * Default cache config.
     *
     * It's overriden in constructor if exists a cache conf named 'objects'.
     *
     * @var array
     */
    private $cacheConfig = array(
        'engine' => 'File',
        'prefix' => '',
        'duration' => '+24 hours'
    );

    /**
     * Blacklist option items on which avoid to create cache.
     *
     * @var array
     */
    private $blacklistOptions = array('user_created', 'user_modified');

    /**
     * Constructor
     * Initialize cache config
     */
    public function __construct() {
        // init cache
        $cacheConf = Cache::settings('objects');
        if (empty($cacheConf)) {
            // default cache path if not configured
            $this->cacheConfig['path'] = BEDITA_CORE_PATH . DS . 'tmp' . DS . 'cache' . DS . 'objects';
            Cache::config('objects', $this->cacheConfig);
            Cache::config('default');
        }
        $this->cacheConfig = $cacheConf + $this->cacheConfig;
        if (!empty($this->cacheConfig['path'])) {
            $this->baseCachePath = $this->cacheConfig['path'];
        }
    }

    /**
     * Get prefix for all cache keys relative to an object.
     *
     * @param int $id Object ID.
     * @return string
     */
    private function cachePrefix($id) {
        if ($this->hasFileEngine()) {
            return sprintf('%03d/%d-', $id % 1000, $id);
        }

        return sprintf('%d/', $id);
    }

    /**
     * Get cache name for an object.
     *
     * @param int $id Object ID.
     * @param array $options Additional caching options.
     * @param string|null $label Additional label.
     * @return string
     */
    private function cacheName($id, array $options, $label = null) {
        if (!empty($options['bindings_list'])) {
            $options = $options['bindings_list'];
        }
        $options = sha1(serialize($options));

        return sprintf('%s%s-%s', $this->cachePrefix($id), $label ?: 'nolabel', $options);
    }

    /**
     * Get cache name for a nickname.
     *
     * @param string $nickname Nickname.
     * @return string
     */
    private function cacheNickname($nickname) {
        return sprintf('nickname-%s', $nickname);
    }

    /**
     * Returns true if cache engine type is 'File'
     *
     * @return boolean
     */
    public function hasFileEngine() {
        return ($this->cacheConfig['engine'] === 'File');
    }

    /**
     * Read id from nickname using cache
     *
     * @param  string $nickname object nickname
     * @return int object id on success, null if $nickname is not found
     */
    public function readIdFromNickname($nickname) {
        if ($this->hasFileEngine()) {
            return null;
        }

        return Cache::read($this->cacheNickname($nickname), 'objects');
    }

    /**
     * Writes $nickname => $id key-value pair in object cache
     *
     * @param  string $nickname object nickname
     * @param  int $id object id
     * @return boolean true on success, false on failure
     */
    public function writeNicknameId($nickname, $id) {
        if ($this->hasFileEngine()) {
            return false;
        }

        return Cache::write($this->cacheNickname($nickname), $id, 'objects');
    }

    /**
     * Deletes cache for a nickname.
     *
     * @param string $nickname Nickname.
     * @return bool
     */
    public function deleteNicknameCache($nickname) {
        if ($this->hasFileEngine()) {
            return true;
        }

        return Cache::delete($this->cacheNickname($nickname), 'objects');
    }

    /**
     * Return true if the object `$id` with `$options` is cacheable.
     *
     * @param int $id The object id
     * @param array $options The options used for build the cache
     * @return boolean
     */
    public function isCacheable($id, array $options) {
        if (empty($options) || array_key_exists('bindings_list', $options)) {
            return true;
        }

        $flatOptions = Set::flatten($options);
        $isCacheable = true;
        foreach (array_keys($flatOptions) as $optionKey) {
            if (is_numeric($optionKey)) {
                continue;
            }

            foreach ($this->blacklistOptions as $itemPattern) {
                if (preg_match("/$itemPattern/", $optionKey)) {
                    $isCacheable = false;
                    break;
                }
            }

            if (!$isCacheable) {
                break;
            }
        }

        return $isCacheable;
    }

    /**
     * Read object from cache.
     *
     * It returns the data cached or false if the object is not cacheable or the cache was not found
     *
     * @param int $id
     * @param array $options
     * @return array|false
     */
    public function read($id, array $options, $label = null) {
        if (!$this->isCacheable($id, $options)) {
            return false;
        }

        $cacheName = $this->cacheName($id, $options, $label);

        return Cache::read($cacheName, 'objects');
    }

    /**
     * Write object data to cache.
     *
     * It returns true if data was successfully cached
     * or false if the data was not cacheable or the write fails
     *
     * @param string $key
     * @return bool
     */
    public function write($id, array $options, $data, $label = null) {
        if (!$this->isCacheable($id, $options)) {
            return false;
        }

        $cacheName = $this->cacheName($id, $options, $label);

        return Cache::write($cacheName, $data, 'objects');
    }

    /**
     * Delete objects from cache
     *
     * @param  integer $id objectId
     */
    public function delete($id) {
        return Cache::delete(sprintf('%s*', $this->cachePrefix($id)), 'objects');
    }

    /**
     * Read path cache for an object.
     *
     * @param int $id Object ID.
     * @param string $statuses Allowed object statuses.
     * @param int $publicationId The publication id
     * @return array|null
     */
    public function readPathCache($id, array $statuses = array(), $publicationId = null) {
        if ($this->hasFileEngine()) {
            return null;
        }

        $status = 'on';
        if (in_array('draft', $statuses)) {
            $status = 'draft';
        }
        if (in_array('off', $statuses)) {
            $status = 'off';
        }

        $publicationId = ($publicationId === null) ? '' : (string)$publicationId;

        return Cache::read(sprintf('%spath-%s-%s', $this->cachePrefix($id), $status, $publicationId), 'objects');
    }

    /**
     * Write path cache for an object.
     *
     * @param int $id Object ID.
     * @param array $path Object path.
     * @param string $statuses Allowed object statuses.
     * @param int $publicationId The publication id
     * @return bool
     */
    public function writePathCache($id, array $path, array $statuses = array(), $publicationId = null) {
        if ($this->hasFileEngine()) {
            return false;
        }

        $status = 'on';
        if (in_array('draft', $statuses)) {
            $status = 'draft';
        }
        if (in_array('off', $statuses)) {
            $status = 'off';
        }

        $publicationId = ($publicationId === null) ? '' : (string)$publicationId;

        return Cache::write(sprintf('%spath-%s-%s', $this->cachePrefix($id), $status, $publicationId), $path, 'objects');
    }

    /**
     * Delete path cache for an object and all its descendants.
     *
     * @param int $id Object ID.
     * @param int[] $descendants Array of descendant IDs.
     * @return bool
     */
    public function deletePathCache($id, array $descendants) {
        if ($this->hasFileEngine()) {
            return false;
        }

        $success = true;
        $descendants = array_merge(array($id), $descendants);
        foreach ($descendants as $descId) {
            $success = $this->delete($descId) && $success;
        }

        return $success;
    }
}
