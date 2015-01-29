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
            'saveMode' => 1
            // 'sourceMediaRoot' => ''
            // 'preservePaths'
        ),
        'export' => array(
            // 'destMediaRoot' => ''
        )
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
        }
        if (isset($this->params['v'])) {
            $this->options['import']['logDebug'] = true;
        }

        // debug: uncomment to test import from array 
        //$inputData = json_decode($inputData,true);
        
        // 2. do import
        $dataTransfer = ClassRegistry::init('DataTransfer');
        $result = $dataTransfer->import($inputData, $this->options['import']);

        // 3. end
        $this->trackInfo('');
        $this->trackInfo('Import end');
    }

    public function export() {

        $this->hr();

        $this->trackInfo('Export start');

        // prepare export
        $objects = array();
        if (isset($this->params['id'])) {
            $objects[] = $this->params['id'];
        } else {
            $objects[] = 1; // default: object with id 1 - test
        }

        if (isset($this->params['f'])) {
            $this->options['export']['filename'] = $this->params['f'];
        }

        if (isset($this->params['m'])) {
            $this->options['export']['destMediaRoot'] = $this->params['m'];
        }

        if (isset($this->params['t'])) {
            $this->options['export']['returnType'] = $this->params['t'];
        }

        if (isset($this->params['v'])) {
            $this->options['export']['logDebug'] = true;
        }

        // do export
        $dataTransfer = ClassRegistry::init('DataTransfer');
        $result = $dataTransfer->export($objects, $this->options['export']);

        // end
        $this->trackInfo('');
        $this->trackInfo('Export end');
    }

    public function help() {
        $this->hr();
        $this->out('format script shell usage:');
        $this->out('');
        $this->out('./cake.sh data import -f <filename> [-m <sourceMediaRoot>] [-v]');
        $this->out('./cake.sh data export [-id <objectId>] [-f <filename>] [-m <destMediaRoot>] [-t <returnType> JSON|FILE|ARRAY] [-v]');
        $this->out('');
    }

    public function test() {
        $allRelations = BeLib::getObject('BeConfigure')->mergeAllRelations();
        debug($allRelations);

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