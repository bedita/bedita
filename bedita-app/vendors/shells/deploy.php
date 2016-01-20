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
		if($res === false) {
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
		if ($res === false) {
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

        // add frontends to package
        $this->out('');
        $this->out('Adding frontends:');
        $frontendsBasePath = $exportPath . DS . 'frontends' . DS;
        foreach ($rel['frontends'] as $frontendName => $frontendUrl) {
            $frontendPath = $frontendsBasePath . $frontendName;
            $gitClone = "git clone $frontendUrl $frontendPath";
            $this->out('');
            $this->out("Git clone command:\n> $gitClone");
            $res = system($gitClone);
            if ($res === false) {
                $this->out("Error cloning $frontendName. Bye.");
                return;
            }
            $folder->delete($frontendPath . DS . '.git');
            $this->out('Frontend ' . $frontendName . ' added.');
        }

        // Rename some files.
        foreach ($rel['renameFiles'] as $from => $to) {
            $p1 = $exportPath . DS . $from;
            $p2 = $exportPath . DS . $to;
            $this->out("rename: {$p1} => {$p2}");
            if (!rename($p1, $p2)) {
                throw new Exception("Error renaming {$p1} to {$p2}");
            }
        }
        
		// create version file
		// release name is : base name + major version (like 3.0.beta1) + codename + git abbreviated sha1 checksum
		$codeName = empty($rel["releaseCodeName"]) ? "" : $rel["releaseCodeName"];
		$releaseName = Configure::read('version') . "." . $codeName . "." . $gitRelease;
		$versionFileContent="<?php\n\$config['Bedita.version'] = '". $releaseName . "';\n?>";
		$handle = fopen($exportPath.DS.$rel["versionFileName"], 'w');
		fwrite($handle, $versionFileContent);
		fclose($handle);
		
		$releaseFile = $releaseDir. DS . $rel["releaseBaseName"] . "-" . $releaseName . ".tar";
		
    	if (file_exists($releaseFile)) {
			$res = $this->in("$releaseFile exists, overwrite? [y/n]");
			if ($res == "y") {
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
		$beditaVersion = Configure::read('version') . "." . $revision;
		$handle = fopen($versionFile, 'w');
		fwrite($handle, "<?php\n\$config['Bedita.version'] = '".$beditaVersion. "';\n?>");
		fclose($handle);
		$this->out("Updated to: $beditaVersion");
    }

    /**
     * Perform update for BEdita core, a frontend, a module or an addon.
     *
     * @param string $type Either `core`, `frontend`, `module` or `addon`.
     * @param string|null $name The name of the frontend, module or addon.
     * @param string $path The full path to the resource to be updated.
     * @return boolean Success.
     */
    protected function performUpdate($type, $name, $path) {
        /** Detect VCS. */
        if (file_exists($path . DS . '.svn')) {
            $this->out('SVN repository detected');
            $updateCmd = "svn update {$path}";
        } elseif (file_exists($path . DS . '.git')) {
            $this->out('Git repository detected');
            $branch = $this->getGitBranch($path);
            if ($branch === false) {
                $this->err('Could not read current Git branch!');
                return false;
            }
            $updateCmd = "cd {$path}; git fetch origin; git merge origin/{$branch};";
        } else {
            $this->err('No VCS detected, aborting!');
            return false;
        }

        /** Run update command. */
        $this->out('Update command: ' . $updateCmd);
        $updateRes = system($updateCmd);
        if ($updateRes === false) {
            $this->err('Update command exited with a non-zero code!');
            return false;
        }

        /** Run post-update tasks. */
        $this->loadTasks();
        switch ($type) {
            case 'core':
                $this->updateVersion();
                break;

            case 'frontend':
                $this->Cleanup->params['frontend'] = $path;
                break;

            case 'addon':
                if ($name == 'vendors') {
                    break;
                }
                $Addon = ClassRegistry::init('Addon');
                $enabledFolder = $Addon->getEnabledFolderByType($name);
                $folder->cd($enabledFolder);
                $list = $folder->read();
                foreach ($list[1] ?: array() as $addonFile) {
                    $Addon->update($addonFile, $name);
                }
        }
        $this->Cleanup->execute();

        return true;
    }

    /**
     * Update BEdita core, a frontend, or a module.
     */
    public function up() {
        static $pluggable = array(
            'frontend' => BEDITA_FRONTENDS_PATH,
            'module' => BEDITA_MODULES_PATH,
            'addon' => BEDITA_ADDONS_PATH,
        );

        /** Read input via CLI arguments. */
        $cliInput = array('type' => null, 'name' => null);
        if (!empty($this->params['-core']) || (!empty($this->params['-type']) && $this->params['-type'] == 'core')) {
            $cliInput['type'] = 'core';
            $cliInput['type'] = 'core';
        } elseif (!empty($this->params['-frontend'])) {
            $cliInput['type'] = 'frontend';
            $cliInput['name'] = $this->params['-frontend'];
        } elseif (!empty($this->params['-module'])) {
            $cliInput['type'] = 'module';
            $cliInput['name'] = $this->params['-module'];
        } elseif (!empty($this->params['-addon'])) {
            $cliInput['type'] = 'addon';
            $cliInput['name'] = $this->params['-addon'];
        } elseif (!empty($this->params['-type']) && !empty($this->params['-name'])) {
            $cliInput['type'] = $this->params['-type'];
            $cliInput['name'] = $this->params['-name'];
        }

        /** Perform update via CLI arguments. */
        if (!empty($cliInput['type'])) {
            $type = $cliInput['type'];
            $name = Inflector::underscore($cliInput['name']);
            $path = ($type == 'core') ? ROOT : ($pluggable[$type] . DS . $name);

            if (!file_exists($path)) {
                $this->error('Invalid arguments', 'The resource you wanted to update does not exists');
                return;
            }

            $ok = $this->performUpdate($type, $name, $path);

            if (!$ok) {
                $this->error('Update failed', 'The update has failed. Check for output in stderr for details');
                return;
            }

            $this->out('Done. Bye!');
            return;
        }

        /** Prepare list of choices. */
        $choices = array(
            array('type' => 'core', 'name' => null, 'path' => ROOT, 'desc' => 'BEdita core / backend'),
        );
        foreach ($pluggable as $type => $dir) {
            if (!file_exists($dir)) {
                continue;
            }

            $folder = new Folder($dir);
            $ls = $folder->read();
            foreach ($ls[0] as $path) {
                if ($path[0] == '.') {
                    continue;
                }

                $name = $path;
                $path = $dir . DS . $path;
                $desc = "{$name} ({$type})";

                array_push($choices, compact('type', 'name', 'path', 'desc'));
            }
        }

        /** Output list of choices. */
        foreach ($choices as $i => $choice) {
            $this->out(sprintf('%3d : %s', $i + 1, $choice['desc']));
        }
        $this->out('  q : quit');
        $this->hr();

        /** Read input. */
        while (true) {
            $choice = $this->in('Select item: ', null, 'q');

            if (strtolower($choice[0]) == 'q') {
                $this->out('Quitting. Bye!');
                return;
            }
            if (is_numeric($choice) && array_key_exists($choice - 1, $choices)) {
                $choice = (int)$choice - 1;
                break;
            }
            $this->err('Invalid selection!');
        }

        $chosen = $choices[$choice];
        $ok = $this->performUpdate($chosen['type'], $chosen['name'], $chosen['path']);

        if (!$ok) {
            $this->err('Update failed!');
        } else {
            $this->out('Done');
        }

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
    
    /**
     * generate changelog file with markdown syntax
     * file is placed in tmp/logs dir
     */
    public function changelog() {
        $this->out();
        $this->out("##############################################");
        $this->out("CHANGELOG FILE BUILDER");
        $this->out("##############################################");
        $this->out();

        $changes = array('user' => array(), 'frontend' => array(), 'developer' => array(), 'other' => array());
        $labelsMap = array('topic - ui' => 'user', 'topic - frontend' => 'frontend');

        $githubUser = $this->in('Github user: ');
        if (empty($githubUser)) {
            $this->out('Missing Github user. Exit... bye');
            return false;
        }

        $githubPassword = $this->in('Github password: ');
        if (empty($githubPassword)) {
            $this->out('Missing Github password. Exit... bye');
            return false;
        }

        $restClient = ClassRegistry::init('RestClientModel');
        $restClient->setup();
        if (!$restClient->useCurl) {
            $this->out('Sorry, Github api are handled with curl. Install curl and retry. Exit... bye');
            return false;
        }
        $options = array(
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$githubUser:$githubPassword",
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30
        );

        $restClient->setOptions($options);
        $response = $restClient->get('https://api.github.com/rate_limit');

        $rateLimitResponse = $this->githubResponse($response);

        // if it's finished rate limit (60 call for hour) skip unit test
        if (empty($rateLimitResponse['headers']) || $rateLimitResponse['headers']['Status'] != '200 OK') {
            $this->out("Github connection refused.\nPlease retry. Bye");
            return false;
        }
        if (empty($rateLimitResponse['headers']['X-RateLimit-Remaining'])) {
            $this->out("Your're requests for hour are terminated.\nRetry later. Bye");
            return false;
        }

        $this->out("Connection to Github done");

        $tagList = $this->getGitTags();
        if (empty($tagList)) {
            $this->out('No tags found. Exit... bye');
            return false;
        }
        $lastTagName = $this->lastTagName();

        $this->out($this->nl(1) . 'TAGS:');
        foreach ($tagList as $key => $tagName) {
            $this->out($key + 1 . ". " . $tagName);
             if ($tagName == $lastTagName) {
                $defaultTagNumber = $key + 1;
            }
        }
        $res = $this->in("Choose the tag from which to start (default last tag named '$lastTagName')", null, $defaultTagNumber);
        if (empty($tagList[$res-1])) {
            $this->out('No tag selected. Exit... bye');
            return false;
        }

        $tag = $tagList[$res-1];
        $this->out($this->nl(1) . 'Tag ' . $tag . ' selected, proceed...' . $this->nl(2));

        $currentBranch = $this->getGitBranch(ROOT);
        $branchesList = $this->getGitBranches();
        $this->out('BRANCHES:');
        foreach ($branchesList as $key => $branchName) {
            $this->out($key + 1 . ". " . $branchName);
            if ($branchName == $currentBranch) {
                $defaultNumber = $key + 1;
            }
        }
        $res = $this->in("Choose the branch on which build changelog (default is current '$currentBranch' branch)", null, $defaultNumber);
        if (empty($branchesList[$res-1])) {
            $this->out('No branch selected. Exit... bye');
            return false;
        }

        $branch = $branchesList[$res-1];
        $this->out($this->nl(1) . 'Branch ' . $branch . ' selected, proceed...' . $this->nl(2));

        $startDelimiter = '<####';
        $endDelimiter = '####>';
        $cmd = "git log $tag..$branch --pretty=format:\"$startDelimiter%B$endDelimiter\"";
        $this->out('Execute ' . $cmd . ' and prepare data');
        exec($cmd, $log);
        // pr($log);
        if (empty($log)) {
            $this->out('No log entry found. Exit... bye');
            return false;
        }

        $mapIssues = array();
        $startAndEndCommitRow = "/^" . $startDelimiter . "(.*)" . $endDelimiter . "$/";
        $startCommitRow = "/^" . $startDelimiter . "(.*)/";
        $endCommitRow = "/(.*)" . $endDelimiter . "$/";
        $patternIssueNumber = "/.*#(\d+).*/";
        $commitMessage = '';
        $commitMessageBuilt = false;

        foreach ($log as $key => $row) {
            $this->out('.', 0);
            // first row of a commit contain start and end delimiter
            if (preg_match($startAndEndCommitRow , $row, $matches)) {
                $commitMessage = trim($matches[1]);
                $commitMessageBuilt = true;
            // first row of a commit message
            } elseif (preg_match($startCommitRow , $row, $matches)) {
                $commitMessage = trim($matches[1]);
            // last row of a commit message
            } elseif (preg_match($endCommitRow , $row, $matches)) {
                $m = trim($matches[1]);
                if (strlen($m) > 0) {
                    $commitMessage .= "\n" . $m;
                }
                $commitMessageBuilt = true;
            // generic row of a commit message
            } else {
                $row = trim($row);
                if (strlen($row) > 0) {
                    $commitMessage .= "\n" . preg_replace("/^\s?[-|*]/", " * ", $row);
                }
            }

            if ($commitMessageBuilt == true) {
                // $this->out('> ' . $commitMessage);
                // check issue number
                $issue = false;
                if (preg_match($patternIssueNumber, $commitMessage, $matches)) {
                    // $this->out('issue: ' . $issue);
                    $issue = $matches[1];
                    if (empty($mapIssues[$issue])) {
                        // get issue data from github
                        $response = $restClient->get('https://api.github.com/repos/bedita/bedita/issues/' . $issue);
                        $issueResponse = $this->githubResponse($response);
                        if (empty($issueResponse['headers']) || $issueResponse['headers']['Status'] != '200 OK' || empty($issueResponse['headers']['X-RateLimit-Remaining'])) {
                            $this->out('Something goes wrong reading issue data from github :(');
                            $this->out('This is github response:');
                            $this->out($response);
                            $this->out('Changelog aborted... bye');
                            return false;
                        }
                        $mapIssues[$issue] = $issueResponse['content'];
                    }

                    $issueDetail = $mapIssues[$issue];
                    $markdownIssue = "[#$issue](" . $issueDetail['html_url'] . ")";
                    // if issue closed put issue title in the changelog
                    if ($issueDetail['state'] == 'closed') {
                        $commitMessage = $markdownIssue . " " . $issueDetail['title'];
                    // replace #issue with markdown issue url
                    } else {
                        $commitMessage = str_replace("#$issue", $markdownIssue, $commitMessage);
                    }
                    $issueLabels = Set::extract('/labels/name', $issueDetail);

                    // user changelog
                    if (empty($changes['user']["#$issue"]) && in_array('Topic - UI', $issueLabels)) {
                        $changes['user']["#$issue"] = $commitMessage;
                    // frontend changelog
                    } elseif (empty($changes['frontend']["#$issue"]) && in_array('Topic - Frontend', $issueLabels)) {
                        $changes['frontend']["#$issue"] = $commitMessage;
                    // developer changelog
                    } elseif (empty($changes['developer']["#$issue"])) {
                        $changes['developer']["#$issue"] = $commitMessage;
                    }

                // if not issue number is found in $commitMessage then add message to 'other' changelog section
                } else {
                    $changes['other'][] = $commitMessage;
                }

                $commitMessage = '';
                // restart building commitMessage
                $commitMessageBuilt = false;
            }
        }

        // pr($changes);

        // write to file
        $beLib = BeLib::getInstance();
        $fileName = 'changelog-tag_' . $beLib->friendlyUrlString($tag) . '-branch_' . $beLib->friendlyUrlString($branch) . '.md';
        $filePath = ROOT . DS . 'bedita-app' . DS . 'tmp' . DS . 'logs' . DS . $fileName;
        $this->out($this->nl(1) . 'Change log will be placed in ' . $filePath);

        $file = new File($filePath, true);
        $version = '## Version ' . Configure::read('version') . ' - ' . Configure::read('codenameVersion') . "\n";
        $file->write($version);
        foreach ($changes as $group => $changesGroup) {
            switch ($group) {
                case 'user':
                    $intro = "\n### User-visible changes\n\n";
                    break;

                case 'frontend':
                    $intro = "\n### Frontend changes\n\n";
                    break;

                case 'developer':
                    $intro = "\n### Developer-visible changes\n\n";
                    break;

                default:
                    $intro = "\n### Uncategorized changes\n\n";
                    break;
            }

            $file->append($intro);

            foreach ($changesGroup as $key => $message) {
                $file->append('* ' . $message . "\n");
            }
        }
        $file->close();
        $this->out('File ' . $filePath . ' written.');
        $this->out('Check and correct it, then prepend content to ' . ROOT . DS . 'CHANGES.md');
        $this->out('Bye');
    }

    /**
     * return array of git tags
     * @return array
     */
    protected function getGitTags() {
        $cmd = "git tag -l";
        exec($cmd, $tags);
        return $tags;
    }

    /**
     * return array of git branches
     * @return array
     */
    protected function getGitBranches() {
        $cmd = "git branch";
        exec($cmd, $branches);
        if (!empty($branches)) {
            foreach($branches as &$b) {
                $b = preg_replace('/\s?\*?\s+/', '', $b);
            }
        }
        return $branches;
    }

    /**
     * Show the most recent tag that is reachable from last commit of current branch
     * @return string the tag name
     */
    protected function lastTagName() {
        $tagName = "";
        $cmd = "git describe --abbrev=0";
        exec($cmd, $tagName);
        if (!empty($tagName)) {
            $tagName = $tagName[0];
        }
        return $tagName;
    }

    private function githubResponse($originalResponse) {
        $response = array('headers' => array(), 'jsonContent' => '', 'content' => '');
        $res = explode("\r\n", $originalResponse);
        if (count($res) > 1) {
            $response['headers']['httpHeader'] = array_shift($res);
            $response['jsonContent'] = array_pop($res);
            if (!empty($res)) {
                foreach ($res as $row) {
                    $matches = array();
                    if (preg_match("/(.+):\s(.+)/", $row, $matches)) {
                        $response['headers'][$matches[1]] = $matches[2];
                    }
                }
            }
        } else {
            $response['jsonContent'] = $res[0];
        }
        $response['content'] = json_decode($response['jsonContent'], true);
        return $response;
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
        $this->out();
        $this->out('   Usage: up [--core|--frontend <frontend>|--module <module>|--addon <addon>] [--type core|frontend|module|addon] [--name <name>]');
        $this->out();
        $this->out('4. upgradeDb: upgrade bedita database to newest version');
  		$this->out(' ');
        $this->out(' ');
        $this->out('5. changelog: generate markdown changelog file');
        $this->out(' ');
    }
}

?>
