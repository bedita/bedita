<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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

if (!class_exists('BeditaException')) {
	if (defined('BEDITA_CORE_PATH')) {
		require_once BEDITA_CORE_PATH . DS . 'bedita_exception.php';
	}
	require_once ROOT . DS . 'bedita-app' . DS . 'bedita_exception.php';
}

/**
 * BeSystem class handle BEdita system utility methods
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeSystem {

    /**
     * System user ID, created on setup.
     */
    const SYSTEM_USER_ID = 1;

	/**
	 * Check whether directory $dirPath exists
	 * 
	 * @param string $dirPath
	 * @return boolean
	 */
	public function checkAppDirPresence($dirPath) {
		return (is_dir($dirPath));
	}

	/**
	 * Check whether file $filePath exists
	 * 
	 * @param string $filePath
	 * @return boolean
	 */
	public function checkAppFilePresence($filePath) {
		return file_exists($filePath);
	}

	/**
	 * Check permissions for directory
	 * 
	 * @param string $dirPath
	 * @param int $mask
	 * @return string
	 */
	public function checkAppDirPerms($dirPath,$mask = 511) {
		return sprintf("%o",(fileperms($dirPath) & $mask));
	}

	/**
	 * Create file $filePath from $filePath.sample
	 * 
	 * @param string $filePath
	 * @return boolean
	 */
	public function createFileFromSample($filePath) {
		$sampleFile = $filePath.'.sample';
		return @copy($sampleFile, $filePath);
	}

	/**
	 * Check whether file $filename is writable
	 * 
	 * @param string $filename
	 */
	public function checkWritable($filename) {
		return is_writable($filename);
	}
	
	/**
	 * delete log files
	 * 
	 * @param string $filename, filename to delete i.e. error.log
	 * @param string $basePath, path to file (default is logs path)
	 * @return array, it contains
	 *				'failed' => array of errors, empty if no errors occured
	 *				'success'  => array of file deleted
	 */
	public function emptyLogs($filename = null, $basePath = LOGS) {
		$results = array('success' => array(), 'failed' => array());
		$logFiles = array();
		if (!empty($filename)) {
			$f = new File($basePath . $filename);
			if (!$f->delete()) {
				$results['failed'][] = array('error' => __("Error deleting log file", true) . " " . $file);
			} else {
				$results['success'][] = $file . " " . __('deleted', true);
			}
		} else {
			$results = $this->cleanUpDir($basePath);
		}
		return $results;
	}
	
	/**
	 * cleanup cached files
	 * 
	 * @param string $basePath, base path on which build cake cache dir and smarty cache dir
	 * @param bool $frontendsToo, true (default) to delete also all frontends cached files
	 * @param bool $cleanAll, false (default) clean all folders in tmp/cache not just 'models', 'persistent' and 'views'
	 * @return array, it contains
	 *				'failed' => array of errors, empty if no errors occured
	 *				'success'  => array deleted files
	 */
	public function cleanupCache($basePath = TMP, $frontendsToo = true, $cleanAll = false) {
		$cakeCacheDir = $basePath . 'cache';
		$smartyCacheDir = $basePath . 'smarty';
		
        $results = array();
        if ($cleanAll) {
            $results = $this->cleanUpDir($cakeCacheDir, true);
        } else {
            $results = $this->cleanUpDir($cakeCacheDir, false, false);
            $defaultDirs = array('models', 'persistent', 'views');
            foreach ($defaultDirs as $dir) {
                $tmpDir = $cakeCacheDir . DS . $dir;
                $resTmp = $this->cleanUpDir($tmpDir, true);
                foreach ($results as $key => $val) {
                    $results[$key] = array_merge($results[$key], $resTmp[$key]);
                }
            }
        }
		
		if (file_exists($smartyCacheDir)) {
			$resSmarty = $this->cleanUpDir($smartyCacheDir, true);
			foreach ($results as $key => $val) {
				$results[$key] = array_merge($results[$key], $resSmarty[$key]);
			}
		}
		
        if ($cleanAll) {
            $cacheKeys = Cache::configured();
            foreach ($cacheKeys as $ck) {
                Cache::clear(false, $ck);
            }
        } else {
            Cache::clear();
            Cache::clear(false, '_cleanup');
        }

		if ($frontendsToo) {
			$folder= new Folder(BEDITA_FRONTENDS_PATH);
            $dirs = $folder->read(true, true, true);
            foreach ($dirs[0] as $d) {
                if($d[0] !== '.') {
                    $cakeCacheDir = $d . DS . 'tmp' . DS . 'cache';
                    if ($cleanAll) {
                        $resCake = $this->cleanUpDir($cakeCacheDir, true);
                    } else {
                        $resCake = $this->cleanUpDir($cakeCacheDir, false, false);
                        $defaultDirs = array('models', 'persistent', 'views');
                        foreach ($defaultDirs as $dir) {
                            $tmpDir = $cakeCacheDir . DS . $dir;
                            $resTmp = $this->cleanUpDir($tmpDir, true);
                            foreach ($resCake as $key => $val) {
                                $resCake[$key] = array_merge($resCake[$key], $resTmp[$key]);
                            }
                        }
                    }
					if (file_exists($d . DS . "tmp" . DS . "smarty")) {
						$resSmarty = $this->cleanUpDir($d . DS . "tmp" . DS . "smarty", true);
					}
					foreach ($results as $key => $val) {
						$results[$key] = array_merge($results[$key], $resCake[$key], $resSmarty[$key]);
					}
	        	}
	        }
		}
		return $results;
	}
	
	/**
	 * clean directory deleting files and eventully subdirs
	 * 
	 * @param string $basePath, the starting directory path
	 * @param boolean $recursive, true to deleting files recursively on subdirs
	 * @param boolean $removeDirs, true to remove all subdirs too
	 * @return array, it contains
	 *				'failed' => array of errors, empty if no errors occured
	 *				'success'  => array of file deleted
	 */
	public function cleanUpDir($basePath, $recursive = false, $removeDirs = false) {
		if (!file_exists($basePath)) {
			throw new BeditaException(__("Directory", true) . " " . $basePath . __("not found", true));
		}
		$results = array('success' => array(), 'failed' => array());
	    App::import('Core', 'Folder');
		$folder = new Folder($basePath);
		$list = $folder->read(true, true, true);
		// delete files
		App::import('Core', 'File');
		foreach ($list[1] as $file) {
			$f = new File($file);
			if ($f->name != "empty") {
				if (!$f->delete()) {
                    CakeLog::write('warn', 'cleanup - unable to delete file ' . $file);
					$results['failed'][] = array('error' => __("Error deleting file", true) . " " . $file);
				} else {
                    CakeLog::write('cleanup', 'file ' . $file . ' removed');
				    $results['success'][] = $file . " " . __('deleted', true);
				}
			}
		}
		// delete dirs
		if ($removeDirs) {
			foreach ($list[0] as $d) {
				if ($d[0] != '.') { // don't delete hidden dirs (.svn,...)
					if (!$folder->delete($d)) {
                        CakeLog::write('warn', 'cleanup - unable to delete folder ' . $d);
					    $results['failed'][] = __("Error deleting dir", true) . " " . $d;
					} else {
                        CakeLog::write('cleanup', 'dir' . $d . ' removed');
					}
				}
			}
		// delete files inside dirs recursively
		} elseif ($recursive) {
			foreach ($list[0] as $d) {
				if ($d[0] != '.') { // don't delete hidden dirs (.svn,...)
					$resultsSubdir = $this->cleanUpDir($d, $recursive, $removeDirs);
					foreach ($results as $key => $val) {
						$results[$key] = array_merge($results[$key], $resultsSubdir[$key]);
					}
				}
			}
		}
		return $results;
	}
	
	
	/**
	 * edit cakephp config file
	 * 
	 * @param type $filepath
	 * @param array $dataToWrite (see BeSystem::writePHPfile)
	 * @param boolean $insertNewVar, if 'true' write new var (if not already in config file)
	 * @return mixed true on success 
	 */
	public function writeConfigFile($filepath, array $dataToWrite, $insertNewVar=false) {
		if (empty($dataToWrite["config"])) {
			$dataToWrite = array("config" => $dataToWrite);
		}
		return $this->writePHPfile($filepath, $dataToWrite, $insertNewVar);
	}

	/**
	 * edit an existing php file writing data from $dataToWrite array
	 * 
	 * @param string $filepath
	 * @param array $dataToWrite
	 *		example of $dataToWrite
	 *		1)
	 *			"config" => array(
	 *				"beditaUrl" => "http://localhost/workspace/bedita",
	 *				'smtpOptions'=> array(
	 *					'port' => '25',
	 *					'timeout' => '30',
	 *					'host' => 'your.smtp.server',
	 *					'username' => 'your_smtp_username',
	 *					'password' => 'your_smtp_password'
	 *				),
	 *				...
	 *			)
	 * 
	 *			will be generate var as
	 *			$config['beditaUrl'] = 'http://localhost/workspace/bedita';
	 *			...
	 * 
	 *		2)
	 *			"myvar" => "this is my var"
	 * 
	 *			will be generate var as
	 *			$myvar = 'this is my var';
	 * 
	 * @param boolean $insertNewVar true to permitt writing new var (not already present in original file)
	 * @return mixed true on success 
	 */
	public function writePHPfile($filepath, array $dataToWrite, $insertNewVar=true) {
		if (!file_exists($filepath)){
			throw new BeditaException(basename($filepath) . " " . __("file doesn't exist", true), array("filepath" => $filepath));
		}
		if (!$this->checkWritable($filepath)) {
			throw new BeditaException(basename($filepath) . " " . __("is not writable", true), array("filepath" => $filepath));
		}
		
		$fileLines = file($filepath);
		
		if ($fileLines === false) {
			throw new BeditaException(__("Error using file() function", true));
		}
		
		// backup old file
		$backupFile = TMP . basename($filepath) . ".backup";
		if (!copy($filepath, $backupFile)) {
			throw new BeditaException(__("Failure on backup of", true) . " " . basename($filepath), array("filepath" => $filepath, "backupfile" => $backupFile));
		}
		
		
		foreach ($dataToWrite as $dataKey => $value) {
			
			// array of line to write into $filepath
			$fileData = array();
			$commentVar = false;
			$loopVar = false;
			$varPlaced = false;
			
			$patternComment = "/^(\/\/|\/\*)/";
			
			// if $value is an assoicative array
			if (is_array($value) && array_keys($value) !== range(0, count($value) - 1)) {
				foreach ($value as $varName => $varValue) {
					$params = array(
						"varName" => $dataKey,
						"keyName" => $varName,
						"varValue" => $varValue,
						"insertNewVar" => $insertNewVar
					);
					$this->parsePHPFile($fileLines, $params);
				}
			// else if it's string or numeric array
			} else {
				$params = array(
					"varName" => $dataKey,
					"varValue" => $value,
					"insertNewVar" => $insertNewVar
				);
				$this->parsePHPFile($fileLines, $params);
			}
		}
				
		if (!empty($fileLines)) {
			// check php validity
			$checkResponse = $this->checkCodeSyntax($fileLines);
			if ($checkResponse !== true) {
				throw new BeditaException(
					__("Wrong PHP code generated", true),
					array(__('See exception.log for more info', true))
				);
			}
			// write to file
			if (file_put_contents($filepath, $fileLines, LOCK_EX) === false) {
				// restore backupped file
				if (!copy($backupFile, $filepath)) {
					throw new BeditaException(__("Failure on restore backupped file of", true) . " " . basename($filepath), array("filepath" => $filepath, "backupfile" => $backupFile));
				}
				throw new BeditaException(__("Error saving file", true) . " " . $filepath);
			}
		}
		
		return true;
	}
	
	/**
	 * Check for syntax error in file
	 *
	 * To check syntax it uses 'php -l' command (cli)
	 *
	 * @param string $fileName the complete file name
	 * @return boolean true on check success
	 */
	public function checkFileSyntax($fileName) {
		$cli = PHP_BINDIR . DS . 'php';
		$cmd = $cli . ' -l ' . $fileName .' 2>&1';
		exec($cmd, $outputAndErrors, $return);
		if ($return === 0) {
			return true;
		} else {
			CakeLog::write('error', 'Syntax error checking ' . $fileName);
			CakeLog::write('exception', 'BeSystem::checkFileSyntax(): ' . print_r($outputAndErrors, true));
			return false;
		}
	}

	/**
	 * Return true if no syntax errors are present in $sourceCode.
	 *
	 * To check syntax it uses BeSystem::checkFileSyntax()
	 * Since 'php -l' works against files a temporary file is created in (bedita-app/tmp) and removed only if the check is correct.
	 * If BeSystem::checkFileSyntax() or file creation fails the method returns false
	 *
	 * @see BeSystem::checkFileSyntax()
	 * @param array|string $sourceCode the source code to test
	 * @param boolean $addPHPtag true to add '<?php' at the begin of $sourceCode
	 * @return boolean
	 */
	public function checkCodeSyntax($sourceCode, $addPHPtag = false) {
		if ($addPHPtag) {
			if (is_array($sourceCode)) {
				array_unshift($sourceCode, '<?php');
			} else {
				$sourceCode = '<?php ' .$sourceCode;
			}
		}
		$tmpFileName = (is_array($sourceCode)) ? md5(implode('', $sourceCode)) : md5($sourceCode);
		$tmpFile = TMP . $tmpFileName . '.php';
		if (file_put_contents($tmpFile, $sourceCode, LOCK_EX) === false) {
			CakeLog::write('error', 'Fail to create temporary file to lint');
			return false;
		}
		$checkResponse = $this->checkFileSyntax($tmpFile);
		if ($checkResponse === true) {
			// remove temporary file
			if (!unlink($tmpFile)) {
				CakeLog::write('warn', 'Error removing temporary file ' . $tmpFile);
			}
		}
		return $checkResponse;
	}

	/**
	 * parse file as array (returned from file() php function) and replace/insert var defined in $params
	 * 
	 * @param array $fileLines returned from file() php function
	 * @param array $params can contain
	 *		"varName" => name of php variable (without $) that has to be searched in $fileLines [required]
	 *		"keyName" => key name of $varName array. Empty if $varName isn't an associative array
	 *		"varValue" => value of $varName[$keyName] or $varName
	 *		"patternVar" => custom regexp pattern to search var. If empty is built automatically
	 *		"insertNewVar" => boolean, it says if add or less variable not already in $fileLines [default is true]
	 * 
	 * @return void 
	 */
	private function parsePHPFile(array &$fileLines, array $params) {
				
		if (empty($params["varName"])) {
			// throw exception
			return false;
		}
		
		$paramsDefault = array(
			"keyName" => "",
			"varValue" => "",
			"patternVar" => "",
			"insertNewVar" => true
		);
		
		$params = array_merge($paramsDefault, $params);
	
		// define patternVar if it's not passed
		if (empty($params["patternVar"])) {
			// If keyName is not empty search for "$varName ="
			if (empty($params["keyName"]) && $params["keyName"] !== 0) {
				$params["patternVar"] = "/\\$" . $params["varName"] . "\s*=/";
			// else search for "$varName[$keyName] ="
			} else {
				$params["patternVar"] = "/\\$" . $params["varName"] . "\[\s*[\"|']" . $params["keyName"] . "[\"|']\s*\]\s*=/";
			}
		}
		
		// array to fill with modified $fileLines
		$fileData = array();
		// when it's true loop on a variable that is on multiple $fileLines lines
		$loopVar = false;
		// true when loop is on var commented
		$commentVar = false;
		// true if var was been found inside $fileLines
		$varPlaced = false;
		// pattern to search php comments
		$patternComment = "/^(\/\/|\/\*)/";
		
		foreach ($fileLines as $lineNum => $line) {
				
			$ltrimLine = ltrim($line);
			$rtrimLine = rtrim($line);

			// loop on var that is on multiple lines
			if ($loopVar) {
				
				// add commented line
				if ($commentVar) {
					$fileData[] = $line;
				}

				if (preg_match($endLoopVar, $rtrimLine)) {
					if (!$varPlaced) {
						$fileData[] = $this->generateVarCodeLine($params);;
						$varPlaced = true;
					}
					$loopVar = false;
					$commentVar = false;
				}

			} else {

				// search for var
				if (preg_match($params["patternVar"], $ltrimLine, $matches)) {
					
					// set the end marker
					$endLoopVar = "/;$/";

					// match comment (comment symbol captured $matches[1])
					if (preg_match($patternComment, $ltrimLine, $matchesComment)) {
						$fileData[] = $line;
						$commentVar = true;
						// set the end marker according to whether is multi lines comment
						if ($matchesComment[1] == "/*") {
							$endLoopVar = "/\*\/$/";
						}

					}
					
					if (!$varPlaced) {
						if (preg_match($endLoopVar, $rtrimLine)) {		
							$newline = "$" . $params["varName"];
							$fileData[] = $this->generateVarCodeLine($params);;
							$varPlaced = true;
							$commentVar = false;
						} else {
							$loopVar = true;
						}
					} elseif (!preg_match($endLoopVar, $rtrimLine)) {
						$loopVar = true;
					}
					
				} else {
					$fileData[] = $line;
				}

			}

		}
		
		// if last line is php close tag check if not present var has to be inserted in file
		if (strpos($fileData[count($fileData)-1], "?>") !== false) {
			
			if (!$varPlaced && $params["insertNewVar"]) {
				$phpCloseTag = array_pop($fileData);
				$fileData[] = PHP_EOL;
				$fileData[] = $this->generateVarCodeLine($params);
				$fileData[] = $phpCloseTag;
			}
		}
				
		$fileLines = $fileData;
	}
	
	/**
	 * generate a php var code line according to $params
	 * 
	 * @param array $params
	 *		"varName" => name of php var without $
	 *		"keyName" => key name of varName array (can be empty)
	 *		"varValue" => value of $varName or $varName[$keyName]
	 * 
	 * @return string 
	 */
	private function generateVarCodeLine(array $params) {
		$newline = "$" . $params["varName"];
		if (!empty($params["keyName"])) {
			$newline .= "['" . $params["keyName"] . "']";
		}
		$newline .= " = " . var_export($params["varValue"], true) . ";" . PHP_EOL;
		return $newline;
	}

    /**
     * Get system user ID.
     *
     * @return int
     * @see BeSystem::SYSTEM_USER_ID
     */
    public function systemUserId () {
        return self::SYSTEM_USER_ID;
    }
}
