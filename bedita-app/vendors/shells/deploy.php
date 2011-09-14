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
	const DEFAULT_SVN_URL 	= 'https://svn.channelweb.it/bedita/trunk' ;
	
	var $tasks = array('Cleanup');
	
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
		
		$svnUrl = $this->in("SVN url, [" . self::DEFAULT_SVN_URL . "]");
		if(empty($svnUrl)) {
			$svnUrl = self::DEFAULT_SVN_URL;
		}
       	$this->out("Using SVN url: $svnUrl");
		$svnUser = $this->in("SVN username: ");
		$svnPassword = $this->in("SVN password: ");
		$releaseDir = $this->in("release dir: ");
		if(!file_exists($releaseDir)) {
	        $this->out("release dir not found: $releaseDir");
			return;
		}
		
    	$svnExport = "svn export --non-interactive --username ". 
    			$svnUser . " --password " . $svnPassword . 
    			" " . $svnUrl . " " . $exportPath;
		$this->out("Svn command: $svnExport");
    	$res = system($svnExport);
		if(empty($res)) {
	        $this->out("Error in svn export. Bye.");
			return;
		}
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
		
		$releaseFile = $releaseDir. DS . $rel["releaseBaseName"] . "-" . $releaseName . ".tar";
		
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

    public function svnUpdate() {
    	
		$sel = array();
		$this->out("1. BEdita core/backend");
		$sel[1] = ROOT;
		$folder = new Folder(BEDITA_FRONTENDS_PATH);
		$ls = $folder->read();
		$count = 1;
		foreach ($ls[0] as $dir) {
			if($dir[0] !== '.' ) {
				$count++;
				$this->out("$count. $dir (frontend)");
				$sel[$count] = BEDITA_FRONTENDS_PATH. DS .$dir;
			}
		}
		
		$frontEndCount = $count;
		if(file_exists(BEDITA_ADDONS_PATH)) {
			$folder = new Folder(BEDITA_ADDONS_PATH);
			$ls = $folder->ls();
			foreach ($ls[0] as $dir) {
				if($dir[0] !== '.' ) {
					$count++;
					$this->out("$count. addons - ($dir)");
					$sel[$count] = BEDITA_ADDONS_PATH. DS .$dir;
				}
			}
		}
				
		$folder = new Folder(BEDITA_MODULES_PATH);
		$ls = $folder->read();
		foreach ($ls[0] as $dir) {
			if($dir[0] !== '.' ) {
				$count++;
				$this->out("$count. $dir (plugin module)");
				$sel[$count] = BEDITA_MODULES_PATH. DS .$dir;
			}
		}
		$count++;
		$this->out("$count. quit");
		$this->hr();
		
    	$selected = $sel[1];
		$res = $this->in("select item: [1]");
		if(!empty($res)) {
			if($res >=  1 && $res < $count) {
				$selected = $sel[$res];
			} else if($res == $count){
				$this->out("Bye");
				return;
			} else {
				$this->out("wrong item $res , choose between 1 and $count");
				return;
			}
		}
		$svnCmd = "svn update $selected";
		$this->out("Svn command: $svnCmd");
    	$svnRes = system($svnCmd);
    	if($svnRes === false) {
			$this->out("Svn command failed");
    	}
    	$this->loadTasks();
    	if($res == 1) {
    		$this->updateVersion();
    	} else if($res <= $frontEndCount) {
    		$this->Cleanup->params["frontend"] = $selected;
    	}
    	$this->Cleanup->execute();
		$this->out("Done");
		$this->svnUpdate();
    }

    function help() {
        $this->out('Available functions:');
        $this->out('1. release: creates bedita release from svn');
  		$this->out(' ');
  		$this->out('   Usage: release -script <release-config-script.php>');
        $this->out(' ');
        $this->out('2. updateVersion: updates version number from svn local info [if present]');
  		$this->out(' ');
        $this->out('3. svnUpdate: updates from svn backend or frontends, with cleanup, version update...');
  		$this->out(' ');
    }
}

?>
