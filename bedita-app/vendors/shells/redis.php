<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2015 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

require_once APP . DS . 'vendors' . DS . 'shells'. DS . 'bedita_base.php';

class RedisShell extends BeditaBaseShell {

    protected $skipKeysPrefix = array(
        'objects_nickname-'
    );

    public function check() {
        $this->out('----------------------------------');
        $this->out('Check integrity keys');
        $this->out('----------------------------------');
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
        $it = NULL;
        $redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        $count = 0;
        $check = 0;
        while ($arrKeys = $redis->scan($it)) {
            foreach ($arrKeys as $key) {
                $count++;
                if (!$this->skipKey($key) && $this->isObjectKey($key) && !$this->isIndexKey($key)) {
                    $id = $this->getIdFromKey($key);
                    $indexKey = 'objects_' . $id . '_index';
                    $val = $redis->get($indexKey);
                    $keyToSearch = substr($key,8);
                    if (!stripos($val,$keyToSearch)) {
                        $this->out("\nchecking key: $key, id: $id, index: $indexKey - key $keyToSearch not found in $indexKey value");
                        $this->out($val);
                        $check++;
                    }
                }
                if ($count % 10000 === 0) {
                    $ans = $this->in("$count keys analyzed. Continue? [y/n]");
                    if ($ans != 'y') {
                        $this->out("$check keys found with integrity problems");
                        exit;
                    }
                }
            }
        }
        $this->out("$count keys analyzed. $check keys found with integrity problems");
        $this->out('----------------------------------');
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

    private function getIdFromKey($key) {
        $start = 8;
        if (stripos($key,'objects_path') === 0) {
            $start = 13;
        }
        $tmp = substr($key,$start);
        
        $len = 0;
        if (stripos($tmp,'_')) {
            $len = stripos($tmp,'_');
        } elseif (stripos($tmp,'-')) {
            $len = stripos($tmp,'-');
        }
        return substr($key,$start,$len);
    }

    public function help() {
        $this->hr();
        $this->out('redis script shell usage:');
        $this->out('');
        $this->out('./cake.sh redis check');
        $this->out('');
    }
}
?>