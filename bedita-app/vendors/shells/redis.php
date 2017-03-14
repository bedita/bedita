<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2017 ChannelWeb Srl, Chialab Srl
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

require_once APP . DS . 'vendors' . DS . 'shells'. DS . 'bedita_base.php';

/**
 * Redis shell - manage redis cache data
 */
class RedisShell extends BeditaBaseShell {

    protected $skipKeysPrefix = array(
        'objects_nickname-'
    );

    /**
     * List cache keys
     * Usage: ./cake.sh redis ls
     * It requires phpredis >= 2.2.5
     *
     * @return void
     */
    public function ls() {
        $this->out('----------------------------------');
        $this->out('List keys (grouped by type)');
        $this->out('----------------------------------');
        $redis = $this->getRedisConn();
        $it = NULL;
        $redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        $count = array(
            'index' => 0,
            'type' => 0,
            'path' => 0,
            'perms' => 0,
            'parent' => 0,
            'children' => 0,
            'nickname' => 0,
            'hash' => 0
        );
        $sizes = array(
            'index' => 0,
            'type' => 0,
            'path' => 0,
            'perms' => 0,
            'parent' => 0,
            'children' => 0,
            'nickname' => 0,
            'hash' => 0
        );
        while ($arrKeys = $redis->scan($it)) {
            foreach ($arrKeys as $key) {
                $managedKey = $this->managedKey($key);
                if (!empty($managedKey)) {
                    $val = $redis->get($key);
                    $s = strlen($val);
                    $count[$managedKey]++;
                    $sizes[$managedKey]+= $s;
                } else {
                    $this->out($key);
                }
            }
        }
        $out = '';
        foreach ($count as $k => $v) {
            $avg = ($v != 0) ? $sizes[$k] / $v : 0;
            $out.= "\n\t$k: $v | size: " . $sizes[$k] . ' | average size: ' . $avg;
        }
        $this->out("keys: $out");
        $this->out('----------------------------------');
    }

    /**
     * Check integrity keys, scanning redis data
     * Usage: ./cake.sh redis check
     * It requires phpredis >= 2.2.5
     *
     * @return void
     */
    public function check() {
        $this->out('----------------------------------');
        $this->out('Check integrity keys');
        $this->out('----------------------------------');
        $redis = $this->getRedisConn();
        $it = NULL;
        $redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        $count = 0;
        $check = 0;
        $del = 0;
        while ($arrKeys = $redis->scan($it)) {
            foreach ($arrKeys as $key) {
                $count++;
                if (!$this->isObjectKey($key)) {
                    $this->out("key skipped: $key");
                } else {
                    if (!$this->skipKey($key) && !$this->isIndexKey($key)) {
                        $id = $this->getIdFromKey($key);
                        if (empty($id)) {
                            $this->out("id not valid of key $key");
                        } else {
                            $indexKey = 'objects_' . $id . '_index';
                            $val = $redis->get($indexKey);
                            if (empty($val)) {
                                $this->out("index not found $indexKey for key $key");
                                $this->deleteKey($redis, $key);
                                $del++;
                            } else {
                                $keyToSearch = substr($key,8);
                                if (!stripos($val,$keyToSearch)) {
                                    $this->out("\nchecking key: $key, id: $id, index: $indexKey - key $keyToSearch not found in $indexKey value");
                                    $this->out($val);
                                    $check++;
                                    $this->deleteKey($redis, $key);
                                    $del++;
                                }
                            }
                        }
                    }
                }
                if ($count % 10000 === 0) {
                    $this->out("$count keys analyzed");
                }
            }
        }
        $this->out("$count keys analyzed. $check keys found with integrity problems. $del keys deleted.");
        $this->out('----------------------------------');
    }

    /**
     * Show cached data by id
     * Param -id is required
     * Usage: ./cake.sh redis show -id <objectId>
     * It requires phpredis >= 2.2.5
     *
     * @return void
     */
    public function show() {
        $this->out('----------------------------------');
        $this->out('Show keys by id');
        $this->out('----------------------------------');
        if (empty($this->params['id'])) {
            $this->out('Param -id missing. Usage:');
            $this->out('./cake.sh redis show -id <objectId>');
            exit;
        }
        $redis = $this->getRedisConn();
        $redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        $arrKeys1 = $redis->keys('objects_' . $this->params['id'] . '_*');
        $arrKeys2 = $redis->keys('objects_' . $this->params['id'] . '-*');
        $arrKeys = array_merge($arrKeys1, $arrKeys2);
        foreach ($arrKeys as $key) {
            if ($this->isIndexKey($key)) {
                $this->out("$key: " . $redis->get($key));
            } else {
                $this->out($key);
            }
        }
    }

    /**
     * Count keys by id
     * Usage: ./cake.sh redis countById [-v (verbose) -log <logFile>]
     * It requires phpredis >= 2.2.5
     *
     * @return void
     */
    public function countById() {
        $this->out('----------------------------------');
        $this->out('Count keys by id');
        $this->out('----------------------------------');
        $verbose = isset($this->params['v']);
        if ($verbose) {
            $this->out('verbose mode on');
        }
        $log = isset($this->params['log']);
        if ($log) {
            $this->out('writing details to log ' . $this->params['log']);
        }
        $counter = 10;
        if (isset($this->params['limit'])) {
            $counter = $this->params['limit'];
        }
        $redis = $this->getRedisConn();
        $redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        $it = NULL;
        $ids = array();
        while ($counter > 0 && $arrKeys = $redis->scan($it)) {            
            foreach ($arrKeys as $key) {
                if ($counter > 0) {
                    if (!$this->isObjectKey($key)) {
                        $this->out("key skipped: $key");
                    } else {
                        if (!$this->skipKey($key) && !$this->isIndexKey($key)) {
                            $id = $this->getIdFromKey($key);
                            if (empty($id)) {
                                $this->out("id not valid of key $key");
                            } else {
                                $counter--;
                                $ids[] = $id;
                            }
                        }
                    }
                }
            }
        }
        foreach ($ids as $id) {
            $arrKeys1 = $redis->keys('objects_' . $id . '_*');
            $arrKeys2 = $redis->keys('objects_' . $id . '-*');
            $arrKeys = array_merge($arrKeys1, $arrKeys2);
            $out = "$id -> " . count($arrKeys) . " keys";
            if ($verbose) {
                $out.= " (" . implode(' ',$arrKeys) . ")";
            }
            if ($log) {
                $this->log($out, $this->params['log']);
            } else {
                $this->out($out);
            }
        }
    }

    /**
     * Count cache data by prefix
     * Argument 0 is required (=> prefix)
     * Usage: ./cake.sh redis countByPrefix <prefix>
     *
     * @return void
     */
    public function countByPrefix(){
        if (empty($this->args[0])) {
            return array();
        }
        $prefix = $this->args[0];
        $redis = $this->getRedisConn();
        $keys = $redis->keys($prefix.'*');
        $redis->close();
        $this->out(count($keys) . ' keys found with prefix "' . $prefix . '"');
    }

    /**
     * Clear cache data by prefix
     * Argument 0 is required (=> prefix)
     * Usage: ./cake.sh redis clearByPrefix <prefix>
     *
     * @return void
     */
    public function clearByPrefix(){
        if (empty($this->args[0])) {
            return array();
        }
        $prefix = $this->args[0];
        $redis = $this->getRedisConn();
        $keys = $redis->keys($prefix.'*');
        if (!empty($keys)) {
            $response = $this->in(count($keys) . ' keys will be deleted. Continue?', array('y', 'n'), 'n');
            if ($response == 'n') {
                $redis->close();
                $this->out('Bye');
                return;
            }
            foreach ($keys as $key) {
                $redis->delete($key);
            }
        }
        $redis->close();
        $this->out(count($keys) . ' keys deleted');
    }

    /**
     * Help for redis shell script
     *
     * @return void
     */
    public function help() {
        $this->hr();
        $this->out('redis script shell usage:');
        $this->out('');
        $this->out('./cake.sh redis check // consistency check for entire objects db');
        $this->out('./cake.sh redis clearByPrefix <prefix> // remove cache keys starting with <prefix> string');
        $this->out('./cake.sh redis countByPrefix <prefix> // count cache keys starting with <prefix> string');
        $this->out('./cake.sh redis countById [-v (verbose mode|default off) -limit <n> (default 10) -log <logFile>] // count keys occurences by ids');
        $this->out('./cake.sh redis ls // list keys group by type');
        $this->out('./cake.sh redis show -id <objectId> // show keys for specified id');
        $this->out('');
    }

    /*********************/
    /* private functions */
    /*********************/

    private function deleteKey($redis, $key) {
        $redis->delete($key);
        $this->out("Key $key deleted");
    }

    private function getRedisConn() {
        $settings = Cache::settings('objects');
        try {
            $redis = new Redis();
            $persistentId = $settings['port'] . $settings['timeout'] . $settings['database'];
            $res = $redis->pconnect($settings['server'], $settings['port'], $settings['timeout'], $persistentId);
        } catch (Exception $e) {
            $this->out('Error connecting to redis: ' . $e->getMessage());
            return -1;
        }
        if (!$res) {
            $this->out('Error connecting to redis');
            return -1;
        }
        if ($settings['password'] && ! $redis->auth($settings['password'])) {
            return -1;
        }
        $redis->select($settings['database']);
        return $redis;
    }

    private function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    private function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    private function skipKey($key) {
        foreach ($this->skipKeysPrefix as $prefix) {
            if ($this->startsWith($key, $prefix)) {
                return true;
            }
        }
        return false;
    }

    private function isObjectKey($key) {
        return $this->startsWith($key, 'objects_');
    }

    private function isIndexKey($key) {
        return $this->endsWith($key, '_index');
    }

    private function isTypeKey($key) {
        return $this->endsWith($key, '-type');
    }

    private function isPermsKey($key) {
        return $this->endsWith($key, '-perms');
    }

    private function isChildrenKey($key) {
        return (strpos($key, '-children-') !== false);
    }

    private function isNicknameKey($key) {
        return (strpos($key, '_nickname-') !== false);
    }

    private function isPathKey($key) {
        return (strpos($key, '_path-') !== false);
    }

    private function isParentKey($key) {
        return (strpos($key, '-parent-') !== false);
    }

    private function isHash($key) {
        if ($this->isObjectKey($key)) {
            $idx = strrpos($key, '-') + 1;
            $hash = substr($key,$idx);
            return (strlen($hash) === 32);
        }
        return false;
    }

    private function getIdFromKey($key) {
        $start = 8;
        if (stripos($key,'objects_path') === 0) {
            $start = 13;
        }
        $tmp = substr($key,$start);
        $len = 0;
        $len1 = (stripos($tmp,'_')) ? stripos($tmp,'_') : null;
        $len2 = (stripos($tmp,'-')) ? stripos($tmp,'-') : null;
        if ($len1 != null && $len2 != null) {
            $len = min(array($len1,$len2));    
        } else if ($len1 !=null) {
            $len = $len1;
        } else {
            $len = $len2;
        }
        return substr($key,$start,$len);
    }

    private function managedKey($key) {
        if ($this->isIndexKey($key)) {
            return 'index'; 
        } else if ($this->isTypeKey($key)) {
            return 'type';
        } else if ($this->isPathKey($key)) {
            return 'path'; 
        } else if ($this->isPermsKey($key)) {
            return 'perms'; 
        } else if ($this->isParentKey($key)) {
            return 'parent'; 
        } else if ($this->isChildrenKey($key)) {
            return 'children'; 
        } else if ($this->isNicknameKey($key)) {
            return 'nickname'; 
        } else if ($this->isHash($key)) {
            return 'hash'; 
        }
        return null;
    }
}
