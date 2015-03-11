<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2014 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

require_once APP . DS . 'vendors' . DS . 'shells'. DS . 'bedita_base.php';

/** * format shell script */
class DataShell extends BeditaBaseShell {

    private $logLevels = array(
        'ERROR' => 0,
        'WARN' => 1,
        'INFO' => 2,
        'DEBUG' => 3
    );

    private $options = array(
        'import' => array(
            'logLevel' => 2, // INFO
            'saveMode' => 1
            // 'sourceMediaRoot' => ''
            // 'preservePaths'
        ),
        'export' => array(
            'logLevel' => 2 // INFO
            // 'destMediaRoot' => ''
        )
    );

    protected $saveModes = array(
        'MERGE' => 0, // merge relations (always for imported objects)
        'NEW' => 1, // create new object with new nickname
        'OVERRIDE' => 2, // remove object with same nickname
        'IGNORE' => 3, // ignore object
        'UPDATE' => 4 // merge relations and update data
    );

    public function import() {
        $this->hr();
        if (empty($this->params['f'])) {
            $this->trackInfo('Missing filename parameter');
            $this->help();
            return;
        }
        // 1. reading file
        $inputData = @file_get_contents($this->params['f']);
        if (!$inputData) {
            $this->trackInfo('File "' . $this->params['f'] . '" not found');
            return;
        }
        $this->trackInfo('Import start');
        if (isset($this->params['m'])) {
            $this->options['import']['sourceMediaRoot'] = $this->params['m'];
        } else {
            $this->options['import']['sourceMediaRoot'] = TMP . 'media-import';
        }
        $this->out('Using sourceMediaRoot: ' . $this->options['import']['sourceMediaRoot']);
        $this->checkDir($this->options['import']['sourceMediaRoot']);

        if (isset($this->params['v'])) {
            $this->options['import']['logDebug'] = true;
        }
        // setting log level - default INFO
        if (!empty($this->options['import']['logDebug'])) {
            if ($this->options['import']['logDebug'] === true) {
                $this->options['import']['logLevel'] = $this->logLevels['DEBUG']; // DEBUG
            }
        }
        $logLevel = $this->options['import']['logLevel'];
        $this->out('Using logLevel: ' . $logLevel . ' (' . array_search($logLevel, $this->logLevels) . ')');
        $this->out('Using saveMode: ' . $this->options['import']['saveMode'] . 
            ' (' . array_search($this->options['import']['saveMode'], $this->saveModes, true) . ')');
        $this->out('See bedita-app/tmp/logs/import.log for details');
        $this->out('');
        // debug: uncomment to test import from array 
        //$inputData = json_decode($inputData,true);
        // 2. do import
        $dataTransfer = ClassRegistry::init('DataTransfer');
        $result = $dataTransfer->import($inputData, $this->options['import']);
        if (!empty($result['log']['ERROR'])) {
            foreach ($result['log']['ERROR'] as $error) {
                $this->out($error);
            }
        }
        
        // 3. end
        $this->trackInfo('');
        $this->trackInfo('Import end');
    }

    public function export() {
        $this->hr();
        $this->trackInfo('Export start');
        // prepare export
        $objects = array();
        if (empty($this->params['f'])) {
            $this->trackInfo('Missing filename parameter');
            $this->help();
            return;
        } else {
            $filename = $this->params['f'];
            $this->options['export']['filename'] = $filename;
            $this->out('Using filename: "' . $filename . '"');
            $this->checkExportFile($filename);
        }
        if (isset($this->params['id'])) {
            $objects[] = $this->params['id'];
            $this->options['export']['all'] = false;
        } else {
            $this->options['export']['all'] = true;
        }
        if (isset($this->params['types'])) {
            $this->options['export']['types'] = $this->params['types'];
            $this->out('Using types: ' . $this->options['export']['types'] 
                . ' (' . $this->options['export']['types'] . ')');
        }
        if (isset($this->params['m'])) {
            $this->options['export']['destMediaRoot'] = $this->params['m'];
        } else {
            $this->options['export']['destMediaRoot'] = TMP . 'media-export';
        }
        $this->checkDir($this->options['export']['destMediaRoot']);
        $this->out('Using destMediaRoot: ' . $this->options['export']['destMediaRoot']);

        if (isset($this->params['t'])) {
            $this->options['export']['returnType'] = $this->params['t'];
            $this->out('Using returnType: ' . $this->options['export']['returnType'] 
                . ' (' . $this->options['export']['returnType'] . ')');
        }
        if (isset($this->params['v'])) {
            $this->options['export']['logDebug'] = true;
        }
        // setting log level - default INFO
        if (!empty($this->options['export']['logDebug'])) {
            if ($this->options['export']['logDebug'] === true) {
                $this->options['export']['logLevel'] = $this->logLevels['DEBUG']; // DEBUG
            }
        }
        $logLevel = $this->options['export']['logLevel'];
        $this->out('Using logLevel: ' . $logLevel . ' (' . array_search($logLevel, $this->logLevels) . ')');
        $this->out('See bedita-app/tmp/logs/export.log for details');
        $this->out('');
                // do export
        $dataTransfer = ClassRegistry::init('DataTransfer');
        $result = $dataTransfer->export($objects, $this->options['export']);
        if (!empty($result['log']['ERROR'])) {
            foreach ($result['log']['ERROR'] as $error) {
                $this->out($error);
            }
        }
        // end
        $this->trackInfo('');
        $this->trackInfo('Export end');
    }

    public function help() {
        $this->hr();
        $this->out('data script shell usage:');
        $this->out('');
        $this->out('./cake.sh data import -f <filename> [-m <sourceMediaRoot>] [-v]');
        $this->out('./cake.sh data export -f <filename> [-all] [-types <type1,type2,...>] [-id <objectId>] [-m <destMediaRoot>] [-t <returnType> JSON|FILE|ARRAY] [-v]');
        $this->out('');
    }

    private function trackInfo($s, $param = null) {
        $this->out($s);
        if($param != null) {
            pr($param);
            $this->hr();
        }
        $this->hr();
    }
}
?>