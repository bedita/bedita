<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
/**
 * Cleanup task
 */
class CleanupTask extends BeditaBaseShell {

	function startup() {
	}
	
	public function execute() {
		$basePath = TMP;
		if (isset($this->params['frontend'])) {
			$basePath = $this->params['frontend'].DS."tmp".DS;
			if(!file_exists($basePath)) {
				$this->out("Directory $basePath not found");
				return;
			}
			$this->out('Cleaning dir: '.$basePath);
			$this->__clean($basePath . 'cache', false);
		}
		if (isset($this->params['logs'])) {
			$this->__clean($basePath . 'logs');
			$this->out('Logs cleaned.');
		}
		Cache::clear();
		$this->__clean($basePath . 'cache' . DS . 'models');
		$this->__clean($basePath . 'cache' . DS . 'persistent');        
		$this->__clean($basePath . 'cache' . DS . 'views');        
		$this->out('Cache cleaned.');
		$this->__clean($basePath . 'smarty' . DS . 'compile');
		$this->__clean($basePath . 'smarty' . DS . 'cache');
		$this->out('Smarty compiled/cache cleaned.');

		if (isset($this->params['media'])) {
			$this->removeMediaFiles();
		}
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
	
	
	private function removeMediaFiles() {
		$mediaRoot = Configure::read("mediaRoot");
		$folder= new Folder($mediaRoot);
		$dirs = $folder->read();
		foreach ($dirs[0] as $d) {
			$folder->delete($mediaRoot . DS. $d);
		}
		$this->out('Media files cleaned.');
	}

}
?>