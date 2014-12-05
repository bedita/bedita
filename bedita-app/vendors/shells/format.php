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
class FormatShell extends BeditaBaseShell {

	private $logLevels = array(
		'ERROR' => 0,
		'WARN' => 1,
		'INFO' => 2,
		'DEBUG' => 3
	);
	private $options = array(
		'import' => array(
			'logLevel' => 3,
			'saveMode' => 1
		),
		'export' => array(
			'logLevel' => 3 // debug level | can be 0 (ERROR), 1 (WARN), 2 (INFO), 3 (DEBUG)
		)
	);

	public function import() {

		$this->hr();

		if (empty($this->params['f'])) {
			$this->trackInfo('Missing filename parameter');
			$this->help();
			return;
    	}

		// !. reading file
    	$inputData = @file_get_contents($this->params['f']);
    	if (!$inputData) {
    		$this->trackInfo('File "' . $this->params['f'] . '" not found');
			return;
    	}

		$this->trackInfo('::: import start :::');

		if (isset($this->params['ll'])) {
			if (!in_array($this->params['ll'], array_values($this->logLevels))) {
				$this->trackInfo('Log level "' . $this->params['ll'] . '" not valid; logLevel can be 0 (ERROR), 1 (WARN), 2 (INFO), 3 (DEBUG)');
				return;
			}
			$this->options['import']['logLevel'] = $this->params['ll'];
		}
		
		// 2. do import
		$beFormat = ClassRegistry::init('BEFormat');
		$result = $beFormat->import($inputData, $this->options['import']);

		// 3. end
		$this->trackInfo('');
		$this->trackInfo('::: import end :::');
	}

	public function export() {

		$this->hr();

		if (empty($this->params['f'])) {
			$this->trackInfo('Missing filename parameter');
			$this->help();
			return;
    	}

		if (empty($this->params['rootId'])) {
			$this->trackInfo('Missing root parameter');
			$this->help();
			return;
    	}

		$this->trackInfo('::: export start :::');

		if (isset($this->params['ll'])) {
			if (!in_array($this->params['ll'], array_values($this->logLevels))) {
				$this->trackInfo('Log level "' . $this->params['ll'] . '" not valid; logLevel can be 0 (ERROR), 1 (WARN), 2 (INFO), 3 (DEBUG)');
				return;
			}
			$this->options['import']['logLevel'] = $this->params['ll'];
		}

		// 1. get data for rootId
		$rootId = $this->params['rootId'];
		$beObject = ClassRegistry::init('BEObject');
		if (
			!(
				$o = $beObject->find(
						'first',
						array(
							'conditions' => array(
								'BEObject.id' => $rootId,
								'BEObject.object_type_id' => array(
									Configure::read('objectTypes.area.id'),
									Configure::read('objectTypes.section.id')
								)
							)
						)
					)
				)
			) {
			$this->trackInfo('Error during root search, for rootId ' . $rootId);
			return;
		}

		if (empty($o)) {
			$this->trackInfo('Area or publication with id ' . $rootId . ' not found');
		}

		// TODO: fill object arrays, ecc.
		// 'tree' / 'objects' / 'relations'

		// 2. do export
		$objects = array(
			0 => $o
		);
		$beFormat = ClassRegistry::init('BEFormat');
		$result = $beFormat->export($objects, $this->options['export']);

		// 3. save data to file
		// TODO: implement

		// 4. end
		$this->trackInfo('');
		$this->trackInfo('::: export end :::');
	}

	public function help() {
		$this->hr();
		$this->out('format script shell usage:');
		$this->out('');
		$this->out('./cake.sh format import -f <filename> [-ll <loglevel>]');
		$this->out('./cake.sh format export -rootId <rootId> -f <filename> [-ll <loglevel>]');
		$this->out('');
		$this->out('Note: logLevel can be 0 (ERROR), 1 (WARN), 2 (INFO), 3 (DEBUG)');		 
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

	// private function viewResult($result, $logLevel) {
	// 	$this->trackInfo( '::: result :::' );
	// 	debug($result['log']);exit;
	// 	foreach ($result['log'] as $key => $log) {
	// 		debug($this->logLevels[$key]);
	// 		if (array_key_exists($key,$this->logLevels) && $this->logLevels[$key] <= $logLevel) {
	// 			$this->hr();
	// 			$this->out($key);
	// 			$this->hr();
	// 			foreach ($log as $msg) {
	// 				$this->out($msg);
	// 			}
	// 		}
	// 		$this->hr();
	// 	}
	// }
}
?>