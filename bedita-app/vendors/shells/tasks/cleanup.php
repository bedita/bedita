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
 * Cleanup task
 */
class CleanupTask extends BeditaBaseShell {

	function startup() {
	}
	
	public function execute() {
		$this->hr();
        $this->out('BEdita core cleanup: ' . TMP);     
		$this->hr();
        $this->cleanUpTmpDir(TMP);
        if (isset($this->params['frontend'])) {
       		$this->out("BEdita frontends cleanup: " . $this->params['frontend']);     
			$this->cleanUpTmpDir($this->params['frontend'].DS."tmp".DS);
		} else {
			// cycle through frontends
        	$this->out("BEdita frontends cleanup: " . BEDITA_FRONTENDS_PATH);     
			$folder= new Folder(BEDITA_FRONTENDS_PATH);
	        $dirs = $folder->read();
	        foreach ($dirs[0] as $d) {
	        	if($d[0] !== ".") {
	            	$this->cleanUpTmpDir(BEDITA_FRONTENDS_PATH . DS . $d . DS . "tmp" . DS);
	        	}
	        }
		}
		$this->hr();
        $this->out("Done");        
	}

	private function cleanUpTmpDir($basePath) {
		$Utility = ClassRegistry::init('Utility');
		if ($basePath !== TMP) {
			if( !file_exists($basePath)) {
				$this->out("Directory $basePath not found");
				return;
			}
		}
		$this->out('Cleaning dir: '.$basePath);
		$cleanAll = isset($this->params['all']);
        if ($cleanAll) {
            $this->out('Cleaning all folders in '. $basePath . 'cache !!');
        }
		$options = array('basePath' => $basePath, 'frontendsToo' => false, 
		    'cleanAll' => $cleanAll);
		$res = $Utility->call('cleanupCache', $options);
		if (!$this->outputCleaningErrors($res)) {
			$this->out('Cache cleaned.');
			$this->out('Smarty compiled/cache cleaned.');
		}
		if (isset($this->params['logs'])) {
			$res = $Utility->call('emptyLogs', array('basePath' => $basePath . 'logs'));
			if (!$this->outputCleaningErrors($res)) {
				$this->out('Logs cleaned.');
			}
		}
	}
	
	/**
	 * output errors
	 * @param array $data
	 * @return boolean, true if errors are output
	 *					false if there aren't errors
	 */
	private function outputCleaningErrors($data) {
		if (empty($data['failed'])) {
			return false;
		}
		$this->hr();
		$this->out('Some errors occured');
		$this->hr();
		foreach ($data['failed'] as $f) {
			$this->out($f['error']);
		}
		return true;
	}
	
	/**
	 * clean PHP files from:
	 * 1) leading spaces
	 * 2) trailing spaces
	 * 3) php tag closed and reopened sequentially
	 * 
	 * @param string $item file or dir path
	 * @param bool $recursive if it follows subdirectories
	 */
	public function cleanPHPFiles($item, $recursive = true) {
		clearstatcache();
		// is file
		if (file_exists($item) && is_file($item)) {
			$ext = pathinfo($item, PATHINFO_EXTENSION);
			if ($ext == "php") {
				$this->removeSpaces($item);
			} else {
				$this->out($item . " is not a php file.");
			}
		// is dir
		} elseif (is_dir($item)) {
			$folder = new Folder($item);
			$phpfiles = $folder->findRecursive(".*\.php");
			if (!empty($phpfiles)) {
				foreach ($phpfiles as $f) {
					$this->removeSpaces($f);
				}
			}
		} else {
			$this->out("Error: " . $item . " is not a valid directory or php file.");
		}
	}
	
	private function removeSpaces($file) {
		$filename = basename($file);
		if (!is_readable($file)) {
			$this->out($filename . " is not readable.");
			return;
		}
		
		//Regex Express to test leading and trailing spaces
		$regExpPre = "/^[\n\r|\n\r|\n|\r|\s]+<\?php/";
		$regExpPost = "/\?>[\n\r|\n\r|\n|\r|\s]+$/";
		$regExpIn = "/^\?>.*<\?php/s";
		
		$data = $originalData = file_get_contents($file);
		
		$data = preg_replace($regExpPre, "<?php", $data);
		$data = preg_replace($regExpPost, "?>", $data);
		$data = preg_replace($regExpIn, "", $data);
		
		if ($data !== $originalData) {
			if (!is_writable($file)) {
				$this->out($filename . "has leading or trailing spaces but it is not writable.");
				return;
			}
			$this->out("Found trailing/leading spaces in " . $filename);
			if (file_put_contents($file, $data)) {
				$this->out($filename . " cleaned");
			} else {
				$this->out("ERROR: error writing " . $filename);
			}
			
		}
	}

}
?>