<?php 
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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
	const DEFAULT_GIT_URL 	= 'https://github.com/bedita/bedita.git';
	
	var $tasks = array('Cleanup');
	
    public function release() {
    	$this->out();
    	$this->out("##############################################");
    	$this->out("CREATE RELEASE PACKAGE");
    	$this->out("##############################################");
    	$this->out();
    	$this->readInputArgs();
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
		$gitClonePath = $tmpBasePath . "bedita-clone";
		$exportPath = $tmpBasePath . "bedita";
		
    	if(!empty($this->params['gitUrl'])) {
    		$gitUrl = $this->params['gitUrl'];
    	} else {
			$gitUrl = $this->in("GIT url, [" . self::DEFAULT_GIT_URL . "]");
			if(empty($gitUrl)) {
				$gitUrl = self::DEFAULT_GIT_URL;
			}
    	}
       	$this->out("Using GIT url: $gitUrl");

       	if(!empty($this->params['releaseDir'])) {
    		$releaseDir = $this->params['releaseDir'];
    	} else {
       		$releaseDir = $this->in("\nrelease dir: ");
    	}

    	if(!file_exists($releaseDir)) {
	        $this->out("release dir not found: $releaseDir");
			return;
		}

    	// list remote branch
    	exec("git branch -r", $remoteBranchesList);
    	$remoteBranchesList = array_map('trim',$remoteBranchesList);
    	if (empty($remoteBranchesList)) {
    		$this->out("Failed to get remote branches list. Abort. Bye.");
    		return;
    	}
    	$this->out();
    	$this->hr();
    	$this->out("Choose the remote branch on which build the release:");
    	foreach ($remoteBranchesList as $key => $branch) {
    		$this->out($key . ". " . $branch);
    	}
    	$this->hr();
    	$branchKey = $this->in("Select remote branch:", array_keys($remoteBranchesList));

    	if (empty($remoteBranchesList[$branchKey])) {
    		$this->out("No remote branch choosed. Abort. Bye.");
    		return;
    	}

    	$remoteBranch = $remoteBranchesList[$branchKey];
    	$this->hr();
    	$this->out("Remote branch " . $remoteBranch . " choosed, proceed.");

    	// local current branch
    	$gitBranch = $this->getGitBranch();
    	$this->out("\nLocal git branch: " . $gitBranch);

    	$remoteBranchAbbrev = str_replace("origin/", "", $remoteBranch);

    	if ($gitBranch !== $remoteBranchAbbrev) {
    		$this->out("\nWARNING!!!");
    		$res = $this->in("You are about to create a release package based on $remoteBranch branch but you are in $gitBranch branch.
    			\nDo you want to continue?", array("y", "n"), "n"
    		);
    		if ($res == "n") {
    			$this->out("\nPackage creation aborted. Bye.");
    			return;
    		}
    	}

    	$this->out();
    	$this->out("##############################################");
    	$this->out("CREATION BEGIN");
    	$this->out("##############################################");
    	$this->out();

		// git clone branch
		$gitClone = "git clone https://github.com/bedita/bedita.git -b $remoteBranchAbbrev $gitClonePath";

		$this->out("Git clone command:\n> $gitClone");
    	$res = system($gitClone);
		if(empty($res)) {
	        $this->out("Error in git clone. Bye.");
			return;
		}

		$gitRelease = $this->getGitRevision();
		if ($gitRelease === false) {
			$this->out("Failed to get revision number. Bye.");
			return;
		}
    	
		$this->out("\nLast git revision: $gitRelease");

		$gitExport = "git checkout-index -f -a --prefix=$exportPath/";
		$this->out("Export command: $gitExport");

		$res = system($gitExport);
		if (!empty($res)) {
			$this->out("Failed to export git code. Bye.");
			return;
		}

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
		$releaseName = Configure::read("majorVersion") . "." . $gitRelease . "." . $codeName;
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
		
		$command = "rm -fr " . $exportPath . DS . "bedita-app" . DS . "tmp" . DS . "*";
       	$this->out("Executing shell command: " . $command);
       	$this->out(shell_exec($command));

       	$writable = array("tmp", "config", "webroot" . DS . "files");
       	foreach ($writable as $wrDir) {
			$command = "chmod 777 " . $exportPath . DS . "bedita-app" . DS . $wrDir;
	       	$this->out("Executing shell command: " . $command);
	       	$this->out(shell_exec($command));
       	}
       	
       	$command = "cd " . $tmpBasePath . " && " . "tar cfp " . $releaseFile . " bedita";
       	$this->out("Executing shell command: " . $command);
       	$this->out(shell_exec($command));
       	
       	$this->cleanTempDir();
        $this->out("$releaseFile created");
    }
    
    public function getGitRevision() {
    	exec("git log -1 --pretty=format:'%h'", $revision);
    	$revision = (!empty($revision[0]))? $revision[0] : false;
		return $revision;
    }
    
    public function getGitBranch($folder = null) {
    	$cmd = "";
    	if ($folder) {
    		$cmd .= "cd $folder; ";
    	}
    	$cmd .= "git rev-parse --abbrev-ref HEAD";
    	exec($cmd, $branch);
    	$branch = (!empty($branch[0]))? $branch[0] : false;
    	return $branch;
    }

    public function updateVersion() {
    	// git repository
    	if (file_exists(ROOT . DS . ".git")) {
    		$revision = $this->getGitRevision();
    		if ($revision === false) {
    			$this->out("Failed to update version number");
    			return;
    		}
    	// svn repository
    	} else {
    		exec("svnversion", $res, $retval);
	    	if($retval !== 0) {
				$this->out("Error executing 'svnversion', bye.");
				return;
			}
			$s = split(":", $res[0]);
			$revision = $s[count($s)-1];
    	}
    	chdir(APP);
		$versionFile = APP . 'config' . DS . 'bedita.version.php';
    	Configure::load("bedita.ini"); // reload new bedita.ini, may be changed by svn up
		$beditaVersion = Configure::read("majorVersion") . "." . $revision;
		$handle = fopen($versionFile, 'w');
		fwrite($handle, "<?php\n\$config['Bedita.version'] = '".$beditaVersion. "';\n?>");
		fclose($handle);
		$this->out("Updated to: $beditaVersion");
    }

    public function up() {
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
			$ls = $folder->read();
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
		$this->out("$count. or 'q' quit");
		$this->hr();
		
    	$selected = $sel[1];
		$res = $this->in("select item: [1]");
		if(!empty($res)) {
			if($res >=  1 && $res < $count) {
				$selected = $sel[$res];
			} else if($res == $count || $res === 'q'){
				$this->out("Bye");
				return;
			} else {
				$this->out("wrong item $res , choose between 1 and $count");
				return;
			}
		}
		// svn repository
		if (file_exists($selected . DS . ".svn")) {
			$updateCmd = "svn update $selected";
		// git repository
		} else {
			$currentBranch = $this->getGitBranch($selected);
			if ($currentBranch === false) {
				$this->out("Failed retrieve current git branch");
			}
			$updateCmd = "cd $selected; git fetch origin; git merge origin/$currentBranch;";
		}

		$this->out("Update command: $updateCmd");
    	$updateRes = system($updateCmd);
    	if($updateRes === false) {
			$this->out("Update command failed");
    	}

		// update enabled addons
		if (strstr($selected, BEDITA_ADDONS_PATH)) {
			$type = trim(substr($selected, strlen(BEDITA_ADDONS_PATH)), DS);
			if ($type != "vendors") {
				$Addon = ClassRegistry::init("Addon");
				$enabledFolder = $Addon->getEnabledFolderByType($type);
				$folder->cd($enabledFolder);
				$list = $folder->read();
				if (!empty($list[1])) {
					foreach ($list[1] as $addonFile) {
						$Addon->update($addonFile, $type);
					}
				}
			}
		}
		
    	$this->loadTasks();
    	if($res == 1) {
    		$this->updateVersion();
    	} else if($res <= $frontEndCount) {
    		$this->Cleanup->params["frontend"] = $selected;
    	}
    	$this->Cleanup->execute();
		$this->out("Done");
		$this->up();
    }
    
    /**
     * @deprecated
     */
    public function svnUpdate() {
    	$this->up();
    }

    public function upgradeDb() {

    	$this->out("BEdita upgrade Db available only from 3.1 to 3.2");
		$this->updateVersion();
		include APP . 'config' . DS . 'bedita.version.php';
		$this->out("Current version: " . $config['Bedita.version']);
		$version = substr($config['Bedita.version'], 0, 3);
		if($version !== '3.2') {
			$this->out("Not able to upgrade to '" . $version . "'. Bye.");
			return;
		}
		
		$oldVersion = "3.1";
		
		$dbCfg = 'default';
		$db = ConnectionManager::getDataSource($dbCfg);
    	$this->out("Updating bedita db config: " . $db->config['driver'] . 
    		" [host=". $db->config['host']. ", database=" . $db->config['database'] . "]");
		$res = $this->in("ACHTUNG! Database " . $db->config['database'] . " will be upgraded, proceed? [y/n]");
		if($res != "y") {
       		$this->out("Bye");
			return;
		}
		$this->hr();

		App::import('Component', 'Transaction');
		$transaction = new TransactionComponent($dbCfg);
		$transaction->begin();
        
		$upSqlPath = APP . 'config' . DS . 'sql' . DS . 'upgrade' . DS;
		$sqlData = $upSqlPath . "0-" . $oldVersion . "-to-" . $version . "-data.sql";
		$sqlSchema = $upSqlPath . "1-" . $oldVersion . "-to-" . $version . "-schema.sql";
		
		App::import('Model', 'BeSchema');
		$beSchema = new BeSchema();
		$this->out("Update data from $sqlData");
		$beSchema->executeQuery($db, $sqlData);
		$this->out("Update schema from $sqlSchema");
		$beSchema->executeQuery($db, $sqlSchema);
		
		$this->out("$dbCfg database updated");
		$transaction->commit();
		
    	$this->loadTasks();
    	$this->Cleanup->execute();
    }
    
    
    function help() {
        $this->out('Available functions:');
        $this->out('1. release: creates bedita release from svn, use custom script with -script, input args in ini file through -input');
  		$this->out(' ');
  		$this->out('   Usage: release [-script <release-config-script.php>] [-input <ini-file-params.php>');
        $this->out(' ');
        $this->out('2. updateVersion: updates version number from svn local info [if present]');
  		$this->out(' ');
        $this->out('3. up: updates from git/svn backend or frontends, with cleanup, version update...');
  		$this->out(' ');
        $this->out('4. upgradeDb: upgrade bedita database to newest version');
  		$this->out(' ');
    }
}

?>
