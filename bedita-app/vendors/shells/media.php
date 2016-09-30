<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2016 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

require_once APP . DS . 'vendors' . DS . 'shells'. DS . 'bedita_base.php';

class MediaShell extends BeditaBaseShell {

    public function help() {
        $this->hr();
        $this->out('media script shell usage:');
        $this->out('');
        $this->out('./cake.sh media fixMissing -f <replacementBase>');
        $this->out('');
    }

    public function fixMissing() {
        if (!isset($this->params['f'])) {
            $this->out("Missing -f parameter");
            return;
        }
        $mediaRoot = Configure::read('mediaRoot');
        $replacement = $this->params['f'];
        $media = $this->missingMedia();
        foreach ($media as $f) {
            $pos = strrpos($f, '.');
            $input = $replacement;
            $output = $mediaRoot . $f;
            $this->out('Saving file ' . $input . ' to ' . $output);
            if ($pos > 0) {
                $ext = substr($f, $pos+1);
                if (file_exists($replacement . '.' . $ext)) {
                    $input = $replacement . '.' . $ext;
                } else {
                    $input = $replacement;
                }
            }
            if (!file_exists(dirname($output))) {
                mkdir(dirname($output), 0777, true);
            }
            file_put_contents($output, file_get_contents($input));
        }
        $stillMissing = $this->missingMedia();
        if (!empty($stillMissing)) {
            $this->out('Media still missing in media root ' . $mediaRoot);
            foreach ($stillMissing as $f) {
                $this->out($f);
            }
        }
    }

    private function missingMedia() {
        $missing = array();
        $mediaRoot = Configure::read('mediaRoot');
        $stream = ClassRegistry::init('Stream');
        $streams = $stream->find('all');
        foreach ($streams as $stream) {
            $uri = $stream['Stream']['uri'];
            if((stripos($uri, "/") === 0) && !file_exists($mediaRoot.$uri)) {
                $missing[] = $uri;
            }
        }
        return $missing;
    }
}
?>