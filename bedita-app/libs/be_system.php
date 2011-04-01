<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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

	public function checkAppDirPresence($dirPath) {
		return (is_dir($dirPath));
	}

	public function checkAppFilePresence($filePath) {
		return file_exists($filePath);
	}

	public function checkAppDirPerms($dirPath,$mask = 511) {
		return sprintf("%o",(fileperms($dirPath) & $mask));
	}

	public function createFileFromSample($filePath) {
		$sampleFile = $filePath.'.sample';
		return @copy($sampleFile, $filePath);
	}

	public function checkWritable($filename) {
		return is_writable($filename);
	}
	
	
	/**
	 * edit cakephp config file
	 * 
	 * @param type $filepath
	 * @param array $dataToWrite (see BeSystem::writePHPfile)
	 * @param boolean $insertNewVar true to permitt writing new var (not already present in original file)
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
			//eval php code: join array in php string without php opening/closing tags
			$code = array_slice($fileLines, 1, count($fileLines)-2);
			// remove define() [constant definition] to avoid notice because they can be already defined
			foreach ($code as $k => $c) {
				if (strpos($c, "define(") !== false) {
					unset($code[$k]);
				}
			}
			
			$codeString = implode("", $code);
			
			if (strpos($fileLines[0], "<?php") === false || strpos($fileLines[count($fileLines)-1], "?>") === false || eval($codeString) === false) {
				throw new BeditaException(__("Wrong PHP code generated", true), $fileLines);
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
				$params["patternVar"] = "/\\$" . $params["varName"] . "\[\s*[\"|']" . $params["keyName"] . "[\"|']\s*\]/";
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
}
?>