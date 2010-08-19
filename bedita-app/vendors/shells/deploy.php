<?php 
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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

require_once 'bedita_base.php';

/**
 * Deploy shell: methods to createa releases, version update, 
 * (TODO) update from SVN or from tarball ,
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class DeployShell extends BeditaBaseShell {
	
	const DEFAULT_RELEASE_FILE 	= 'release.cfg.php' ;
	
    public function release() {
    	$scr = self::DEFAULT_RELEASE_FILE;
    	if(!empty($this->params['script'])) {
    		$scr = $this->params['script'];
    	}
		$this->out("Using script: $scr");
		require_once($scr);
    	if(empty($rel) || !is_array($rel)) {
	        $this->out("no parameters in script $scr");
			return;
    	}
    	$this->check_sys_get_temp_dir();
		$tmpBasePath = $this->setupTempDir();
       	$this->out("Using temp dir: $tmpBasePath");
		$exportPath = $tmpBasePath . "bedita";
		
		
    	$svnExport = "svn export --non-interactive --username ". 
    			$rel['user'] . " --password " . $rel['password'] . 
    			" " . $rel['url'] . " " . $exportPath;
		$this->out("Svn command: $svnExport");
    	$res = system($svnExport);
		$this->out("Result: $res");
		$s = split(" ", $res);
		$svnRelease = $s[count($s)-1];
		$this->out("Svn release: $svnRelease");
		
		foreach ($rel["removeFiles"] as $f) {
			$fp = $exportPath.DS.$f;
			$this->out("remove: $fp");
			if(!unlink($fp)) {
	        	throw new Exception("Error deleting file " . $fp);
			}
		}

		$folder= new Folder($exportPath);
		foreach ($rel["removeDirs"] as $d) {
			$p = $exportPath.DS.$d;
			$this->out("remove: $p");
			if(!$folder->delete($p)) {
	        	throw new Exception("Error deleting dir " . $p);
			}
		}
		
		foreach ($rel["createDirs"] as $d) {
			$p = $exportPath.DS.$d;
			$this->out("create dirs: " . $p);
			if (!$folder->create($p)) {
				throw new Exception("Error creating dir " . $p);
			}
			$this->out("create empty file in: " . $p);
			if (!$fileObj = new File($p . DS . "empty", true)) {
				throw new Exception("Error creating empty file in " . $p);
			}
		}
		
		foreach ($rel["renameFiles"] as $from => $to) {
			$p1 = $exportPath.DS.$from;
			$p2 = $exportPath.DS.$to;
			$this->out("rename: $p1 => $p2");
			if(!rename($p1, $p2)) {
	        	throw new Exception("Error renaming " . $p1. " to " .$p2);
			};
		}
		
    	foreach ($rel["createFiles"] as $f) {
			$p = $exportPath.DS.$f;
			$this->out("create file: " . $p);
			if (!$fileObj = new File($p, true)) {
				throw new Exception("Error creating empty file " . $p);
			}
		}

		foreach ($rel["moveDirs"] as $from => $to) {
			$pathFrom = $exportPath.DS.$from;
			$pathTo = $exportPath.DS.$to;
			$this->out("move dir: $pathFrom => $pathTo");
			if(!$folder->move(array("to" => $pathTo, "from" => $pathFrom))) {
	        	throw new Exception("Error moving " . $pathFrom. " to " .$pathTo);
			}
		}
		
		// create version file
		// release name is : base name + majorversione (like 3.0.beta1) + svn revision
		$codeName = empty($rel["releaseCodeName"]) ? "" : $rel["releaseCodeName"];
		$releaseName = Configure::read("majorVersion") . "." . $svnRelease . $codeName;
		$versionFileContent="<?php\n\$config['Bedita.version'] = '". $releaseName . "';\n?>";
		$handle = fopen($exportPath.DS.$rel["versionFileName"], 'w');
		fwrite($handle, $versionFileContent);
		fclose($handle);
		
		$releaseFile = $rel["releaseDir"]. DS . $rel["releaseBaseName"] . "-" . $releaseName . ".tar";
		
    	if(file_exists($releaseFile)) {
			$res = $this->in("$releaseFile exists, overwrite? [y/n]");
			if($res == "y") {
				if(!unlink($releaseFile)){
					throw new Exception("Error deleting $releaseFile");
				}
			} else {
				$this->out("Export aborted. Bye.");
				return;
			}
		}
		$this->out("Creating: $releaseFile");
		
		$command = "cd " . $tmpBasePath . " && " . "tar cfp " . $releaseFile . " bedita";
       	$this->out("Executing shell command: " . $command);
       	$this->out(shell_exec($command));
       	
       	$this->cleanTempDir();
        $this->out("$releaseFile created");
    }
    
    
    public function updateVersion() {
    	
    	chdir(APP);
    	exec("svnversion", $res, $retval);
    	if($retval !== 0) {
			$this->out("Error executing 'svnversion', bye.");
			return;
		}
		$s = split(":", $res[0]);
		$svnRevision = $s[count($s)-1];
		$versionFile = APP . 'config' . DS . 'bedita.version.php';
		$beditaVersion = Configure::read("majorVersion") . "." . $svnRevision;
		$handle = fopen($versionFile, 'w');
		fwrite($handle, "<?php\n\$config['Bedita.version'] = '".$beditaVersion. "';\n?>");
		fclose($handle);
		$this->out("Updated to: $beditaVersion");
		
    }


    function help() {
        $this->out('Available functions:');
        $this->out('1. release: creates bedita release from svn');
  		$this->out(' ');
  		$this->out('   Usage: release -script <release-config-script.php>');
        $this->out(' ');
        $this->out('2. updateVersion: updates version number from svn local info [if present]');
  		$this->out(' ');
	}
}

?>
