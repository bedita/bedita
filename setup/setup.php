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
/**
 * BeditaInstallationWizard class
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

	if(!defined('ROOT')) {
		die;
	}

	define('WIZ_INFO','INFO');
	define('WIZ_WARN','WARN');
	define('WIZ_ERR','ERROR');

	define('BE_APP', ROOT . DS . APP_DIR . DS);
	
	require ROOT . DS . 'vendors' . DS . 'smarty' . DS . 'libs' . DS . 'Smarty.class.php';
	require BE_APP . 'libs' . DS . 'be_system.php';

	if(file_exists(BE_APP . 'config' . DS. 'bedita.cfg.php')) {
		die;
	}
	
	
	class BeditaInstallationWizard {

		var $smarty;
		var $check_arr = array();
		var $besys;
		var $steps = array(
			'Filesystem',
			'Database',
			'Admin',
			'Finish'
		);

		const PAGE_FILESYS = "1";
		const PAGE_DBCONN = "2";
		const PAGE_BEADMIN = "3";
		const PAGE_FINISH = "4";
		const PAGE_ENDINSTALL = "5";
		
		public function BeditaInstallationWizard() {
			$this->besys = new BeSystem();
		}

		public function start($page = null) {
			$wizard_finished = false;
			if($page != null) {
				$p = $page;
			} else {
				$p = (empty($_POST['page'])) ? "1" : $_POST['page'];
			}
			switch ($p) {
				default: case self::PAGE_FILESYS:
					$this->page_filesys();
					break;
				case self::PAGE_DBCONN:
					$this->page_dbconn();
					break;
				case self::PAGE_BEADMIN:
					$this->page_beadmin();
					break;
				case self::PAGE_FINISH:
					$this->page_finish();
					break;
				case self::PAGE_ENDINSTALL:
					$this->page_endinstall();
					$wizard_finished = true;
					break;
			}
			return $wizard_finished;
		}

		private function initSmarty() {
			$this->smarty = new Smarty();
			$this->smarty->template_dir = ROOT . DS . "setup" . DS. 'views';
			$this->smarty->compile_dir = BE_APP . 'tmp' . DS . 'smarty' . DS . 'compile';
			$this->smarty->cache_dir = BE_APP . 'tmp' . DS . 'smarty' . DS . 'cache';
		}

		private function page_filesys() {
			if($this->_checkFileSystem()) {
				$this->start(self::PAGE_DBCONN);
			} else {
				$out = file_get_contents(ROOT . DS . "setup" . DS. "views" . DS . "filesys.html");
				$css = file_get_contents(ROOT . DS . "setup" . DS. "css" . DS . "setup.css");
				$out = 	str_replace("{\$css}", $css, $out);	
				$errors = "";
				foreach($this->check_arr['fileSys'] as $k => $v) {
					if($v['severity'] !== 'INFO') {
						$errors .= '<p><span class="' . $v['severity'] . '">[' . $v['severity'] . ']</span>: <code>' . $v['label'] . '</code>: <span class="' . $v['severity'] . '">' . $v['description'] . '</span></p>';
					}
				}
				$out = 	str_replace("{\$errors}", $errors, $out);			
				echo $out;				
			}
			
		}

		private function _checkFileSystem() {
			$this->check_arr['fileSys'] = array();
			$result = true;
			// check /tmp dir
			$tmpDir = BE_APP . 'tmp';
			if(!$this->_checkdirwriteable('fileSys','Check directory: '.$tmpDir, $tmpDir)) {
				$result = false;
			} else { 
				// check or create subdirs of /tmp
				$subdirs = array("cache", "logs", "sessions", "smarty", "tests");
				if(!$this->_checkSubDirsExistWritable($subdirs, $tmpDir)) {
					$result = false;
				}
				
				// check or create subdirs of /tmp/cache
				$subdirs = array("models", "persistent", "views");
				if(!$this->_checkSubDirsExistWritable($subdirs, $tmpDir .DS . "cache")) {
					$result = false;
				}
				
				// check or create subdirs of /tmp/smarty
				$subdirs = array("cache", "compile");
				if(!$this->_checkSubDirsExistWritable($subdirs, $tmpDir .DS . "smarty")) {
					$result = false;
				}
				
			}

			// check /webroot/files dir
			$fDir = BE_APP . 'webroot' . DS . 'files';
			if(!$this->_checkdirwriteable('fileSys','Check directory: '.$fDir, $fDir)) {
				$result = false;
			}
			
			// check /config dir
			$confDir = BE_APP . 'config';
			if(!$this->_checkdirwriteable('fileSys','Check directory: '.$confDir, $confDir)) {
				$result = false;
			}

			// checking config file /config/database.php
			$confFile = $confDir.DS."database.php";
			if(!$this->_checkfileconfig('fileSys','Check config file: '.$confFile, $confFile, $confDir)) {
				$result = false;
			}
			
			// checking config file /config/database.php
			$confFile = $confDir.DS."core.php";
			if(!$this->_checkfileconfig('fileSys','Check config file: '.$confFile, $confFile, $confDir)) {
				$result = false;
			}

			return $result;			
		}

		private function _checkSubDirsExistWritable(array $subdirs, $basePath) {
			$result = true;
			foreach ($subdirs as $d) {
				$path = $basePath . DS . $d;
				$label = 'Check directory: '.$path;
				if(!$this->besys->checkAppDirPresence($path)) {
					if(!mkdir($path)) {
						$result = false;
						$this->check_arr['fileSys'][] = array('label' => $label, 'result' => $result, 
							'severity' => WIZ_ERR, 'description' => 'unable to create directory');
					} 
				} else {
					$this->_checkdirwriteable('fileSys', $label, $path);
				}
			}
			return $result;
		}
		
		private function page_dbconn() {
			$this->initSmarty();
			$filename = ROOT . DS . APP_DIR . DS ."config" . DS . "database.php";
			require_once(CORE_PATH . 'cake' . DS . 'bootstrap.php');
			if(!empty($_POST['dbconfig_modify']) && ($_POST['dbconfig_modify']=="y")) {
				if($this->besys->checkWritable($filename)) {
					if($_POST['data']['database']['password'] !== $_POST['data']['database']['cpassword']) {
						$this->smarty->assign('cpassworderr',true);
					} else {
						if($this->applyDatabaseConfiguration($filename,$_POST['data']['database'])) {
							$this->smarty->assign('dbconfigupdated',true);
						}
					}
				}
			}
			$this->smarty->assign('steps',$this->steps);
			Configure::write('debug', 0);
			App::import('ConnectionManager');
			$db = ConnectionManager::getDataSource('default');
			$db->cacheSources = false;
			
			if(!empty($_POST['action']) && ($_POST['action'] == 'initdb')) {
				$this->check_arr['dbinit'] = array();
				if(!$this->_initdb($db)) {
					$this->check_arr['dbinit'][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_ERR, 'description' => 'error launching init db script');
				} else {
					$this->check_arr['dbinit'][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_INFO, 'description' => 'db init successfull');
					$dbInitOk = true;
				}
				$this->smarty->assign('initdb_results',$this->check_arr['dbinit']);
			}
			if(!empty($db)) {
				$database_config = $db->config;
				if(empty($_POST['dbconfig_modify']) && empty($_POST['action'])) {
					unset($database_config["port"]);
					unset($database_config["connect"]);
				}
				$this->smarty->assign('database_config',$database_config);
				$this->smarty->assign('is_connected', $db->isConnected() ? "y" : "n");
				$this->smarty->assign('database_sources', $db->listSources());
			}
			$this->smarty->assign('dbfile',$filename);
			$this->smarty->assign('dbfile_writable',$this->besys->checkWritable($filename) ? "y" : "n");
			$this->smarty->display('database.tpl');
		}

		private function page_beadmin() {
			require_once(CORE_PATH . 'cake' . DS . 'bootstrap.php');
			Configure::write('debug', 1);
			$this->initSmarty();
        	$this->smarty->assign('existingUser', ClassRegistry::init('User')->field('userid', array('id' => 1)));
        	$this->smarty->assign('defaultPassword', (ClassRegistry::init('User')->field('passwd', array('id' => 1)) == md5('bedita')));
			$this->smarty->assign('steps', $this->steps);
			$config = Configure::getInstance();
			$baseUrl = $config->read('App.baseUrl');
			$check_mod_rewrite = $this->_checkmodrewritephp();
			if(!empty($_POST['p_from'])) { // check form admin
				$admin_data_ok = true;
				if(empty($_POST['data']['admin']['user'])) {
					$admin_data_ok = false;
					$this->smarty->assign('admin_user_empty',true);
				}
				if(empty($_POST['data']['admin']['password'])) {
					$admin_data_ok = false;
					$this->smarty->assign('admin_pass_empty',true);
				}
				if($_POST['data']['admin']['password'] !== $_POST['data']['admin']['cpassword']) {
					$admin_data_ok = false;
					$this->smarty->assign('cpassworderr',true);
				}
				// #747 BEdita web wizard setup creates core.php with errors - skip _applymodrewrite 
// 				if($check_mod_rewrite != $this->_checkmodrewritecakephp($baseUrl)) {
// 					$this->_applymodrewrite($this->_checkmodrewritephp($baseUrl));
// 					$baseUrl = $config->read('App.baseUrl');
// 				}
				if($admin_data_ok) {
					$userdata = array(
						'User' => array(
							'realname' => trim($_POST['data']['admin']['user']),
							'userid' => trim($_POST['data']['admin']['user']),
							'passwd' => md5(trim($_POST['data']['admin']['password'])),
						),
						'Group' => array(
							'id' => 1,
						),
					);
					if (!empty($_POST['data']['admin']['_overwrite'])) {
						// #540 - Overwrite User with ID = 1.
						$userdata['User']['id'] = 1;
					}
					if(!$this->_saveuser($userdata)) {
						$this->smarty->assign('usercreationerr',true);
					} else {
						$this->smarty->assign('userid', $userdata['User']['userid']);
						$this->smarty->assign('usercreationok',true);
						$this->start(self::PAGE_FINISH);
						return;
					}
				}
			}
			
//			$this->smarty->assign('bedita_url',$config->read('beditaUrl'));
//			$this->smarty->assign('bedita_url_check',$this->_checkurl($config->read('beditaUrl')));
//			$this->smarty->assign('media_root',$config->read('mediaRoot'));
//			$this->smarty->assign('media_root_check',$this->_checkmediaroot($config->read('mediaRoot')));
//			$this->smarty->assign('media_url',$config->read('mediaUrl'));
//			$this->smarty->assign('media_url_check',$this->_checkurl($config->read('mediaUrl')));
			$this->smarty->assign('mod_rewrite_php',$check_mod_rewrite);
			$this->smarty->assign('mod_rewrite_cakephp',$this->_checkmodrewritecakephp($baseUrl));
			$this->smarty->display('admin.tpl');
		}

		private function page_finish() {
			require_once(CORE_PATH . 'cake' . DS . 'bootstrap.php');
			$this->initSmarty();
			
			$confDir = BE_APP . 'config';
			$confFile = $confDir.DS."bedita.cfg.php";
			if(!$this->_checkfileconfig('fileSys','Creating config file: '.$confFile, $confFile, $confDir)) {
				$this->smarty->assign('endinstallfileerr', true);
			} else {
				$p = strrpos($_SERVER['REQUEST_URI'], "/");
				$url1 = substr($_SERVER['REQUEST_URI'], 0, $p);
				$url2 = str_replace("index.php", "", substr($_SERVER['REQUEST_URI'], $p));
				$beUrl = rtrim("http://" . $_SERVER['HTTP_HOST'] . $url1 . $url2, '/');
				try {
					$this->besys->writeConfigFile($confFile, array('beditaUrl' => $beUrl), true);
				} catch (Exception $ex) {
					// fail to write bedita.cfg
					$this->smarty->assign('configWriteFail', true);
				}
			}
			$this->smarty->assign('steps',$this->steps);
			$this->smarty->display('finish.tpl');
		}

		private function page_endinstall() {
		}

		private function _checkurl($url) {
			$r = array();
			$result = @get_headers($url);
			if(empty($result) || !$result) {
				$r['severity'] = WIZ_WARN;
				$r['status'] = 'Invalid url';
				return $r;
			}
			$status = (!empty($result) && !empty($result[0])) ? $result[0] : "";
			$r['status'] = $status;
			$error_400 = stristr($status,'HTTP/1.1 4');
			$error_500 = stristr($status,'HTTP/1.1 5');
			if(!empty($error_400) || !empty($error_500)) {
				$r['severity'] = WIZ_WARN;
			} else {
				$r['severity'] = WIZ_INFO;
			}
			return $r;
		}

		private function _checkmediaroot($path) {
			$r = array();
			if($this->besys->checkAppDirPresence($path)) {
				$r['severity'] = WIZ_INFO;
				$r['status'] = 'found';
			} else {
				$r['severity'] = WIZ_WARN;
				$r['status'] = 'not found';
			}
			return $r;
		}
		private function _checkdir($checklabel,$label,$path) {
			$result = true;
			if(!$this->besys->checkAppDirPresence($path)) {
				$result = false;
				$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_ERR, 'description' => 'directory not found');
			} else {
				$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_INFO, 'description' => 'directory found');
			}
			return $result;
		}

		private function _checkmodrewritephp() {
			if(!function_exists("apache_get_modules")) {
				if(!empty($_POST['mod_rewrite_enabled'])) {
					return $_POST['mod_rewrite_enabled'];
				}
				return "askuser";
			}
			return (in_array('mod_rewrite',apache_get_modules())) ? "enabled" : "disabled";
		}

		private function _checkmodrewritecakephp($appBaseUrl) {
			return (empty($appBaseUrl)) ? "enabled" : "disabled";
		}

		private function _checkdirwriteable($checklabel,$label,$path) {
			$result = $this->_checkdir($checklabel,$label,$path);
			if($result) {
				if(!$this->besys->checkWritable($path)) {
					$result = false;
					$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_ERR, 'description' => 'directory not writable');
				} else {
					$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_INFO, 'description' => 'directory writable');
				}
			}
			return $result;
		}

		private function _checkfileconfig($checklabel,$label,$filename,$confdir) {
			$result = true;
			if(!$this->besys->checkAppFilePresence($filename)) {
				$filesample = $filename.'.sample';
				if(!$this->besys->checkAppFilePresence($filesample)) {
					$result = false;
					$this->check_arr[$checklabel][] = array('label' => 'Check presence of sample file: '.$filesample, 
						'result' => $result, 'severity' => WIZ_ERR, 'description' => 'file not found');
				} else {
					if(!$this->besys->checkWritable($confdir)) {
						$result = false;
						$this->check_arr[$checklabel][] = array('label' => 'Trying to create from .sample:', 
							'result' => $result, 'severity' => WIZ_ERR, 'description' => 'directory ' . $confdir . ' is not writable');
					} else {
						if(!$this->besys->createFileFromSample($filename)) {
							$result = false;
							$this->check_arr[$checklabel][] = array('label' => 'Trying to create from .sample:', 
								'result' => $result, 'severity' => WIZ_ERR, 'description' => 'permission denied');
						} else {
							$this->check_arr[$checklabel][] = array('label' => 'Trying to create from .sample:', 
								'result' => $result, 'severity' => WIZ_INFO, 'description' => 'done');
						}
					}
				}
			} else {
				if(!$this->besys->checkWritable($filename)) {
					$result = false;
					$this->check_arr[$checklabel][] = array('label' => $label, 
						'result' => $result, 'severity' => WIZ_ERR, 'description' => 'file not writable');
				} else {
					$this->check_arr[$checklabel][] = array('label' => $label, 'severity' => WIZ_INFO, 'description' => 'file found');
				}
			}
			return $result;
		}

		private function _checksmarty() {

			// smarty temporary directory must be writable
			$smarty_dir = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'smarty';
			$result = $this->_checkdirwriteable('smarty','Checking smarty directory: '.$smarty_dir,$smarty_dir);

			$smarty_cache_dir = $smarty_dir . DS . 'cache';
			if(!$this->_checkdirwriteable('smarty','Checking smarty cache directory: '.$smarty_cache_dir,$smarty_cache_dir)) {
				$result = false;
			}

			$smarty_compile_dir = $smarty_dir . DS . 'compile';
			if(!$this->_checkdirwriteable('smarty','Checking smarty compile directory: '.$smarty_compile_dir,$smarty_compile_dir)) {
				$result = false;
			}
			return $result;
		}

		private function _applymodrewrite($enable = "enabled") {
			$filename = ROOT . DS . APP_DIR . DS . "config" . DS . "core.php";
			$c = 1;
			$done = false;
			$filedata = array();
			$filearr = file($filename);
			$line_start = 0;
			foreach($filearr as $line_num => $line) {
				if(($line_start == 0) && (stripos($line,"Configure::write('App.baseUrl'") !== false) ) {
					$line_start = $line_num;
				}
				if($line_start > 0 && !$done) {
					$line_end = $line_num;
					$line_start = -1;
					$done = true;
					$filedata[]= ($enable == "enabled") ? "// Configure::write('App.baseUrl', env('SCRIPT_NAME'));" : "Configure::write('App.baseUrl', env('SCRIPT_NAME'));";
					$done = true;
				} else {
					$filedata[]=$line;
				}
			}
			return (file_put_contents($filename,$filedata) !== FALSE);
		}

		private function applyDatabaseConfiguration($filename, $db) {
			$c = 1;
			$dbLoc = $db;
			unset($dbLoc["cpassword"]);
			foreach($dbLoc as $k => &$v) {
				if ($k == 'persistent') {
					$v = (!empty($v) && $v == 'true')? true : false;
				} elseif ($k === 'schema') {
					if(empty($v) && $dbLoc['driver'] === 'postgres') {
						$v = 'public'; // default "public" schema for postgres if not set
					}
				}
				if(empty($v) && $k !== 'persistent') {
					unset($dbLoc[$k]);
				}
			}		
			$dbsize = sizeof($dbLoc);
			$done = false;
			$filedata = array();
			$filearr = file($filename);
			$line_start = 0;
			foreach($filearr as $line_num => $line) {
				if(($line_start == 0) && (stripos($line,'var $default') !== false) ) {
					$line_start = $line_num;
				}
				if($line_start > 0 && !$done) {
					if(stripos($line,');') !== false) {
						$line_end = $line_num;
						$line_start = -1;
						$done = true;
						$filedata[]= '   var $default = ' . var_export($dbLoc, true) . ';' . PHP_EOL;
					}
				} else {
					$filedata[]=$line;
				}
			}
			return (file_put_contents($filename,$filedata) !== FALSE);
		}

		private function _initdb($db) {
			App::import('Model', 'BeSchema');
			$beSchema = new BeSchema();
			$script_schema = APP ."config" . DS . "sql" . DS . "bedita_" . $db->config['driver'] . "_schema.sql";
			$beSchema->executeQuery($db, $script_schema);
			$script_data = APP ."config" . DS . "sql" . DS . "bedita_init_data.sql";
			$beSchema->executeQuery($db, $script_data);
			return true;
		}

		private function _saveuser($userdata) {
			if (!ClassRegistry::init('User')->save($userdata)) {
				return false;
			}
			return true;
		}

		private function _count_errors() {
			$n_errors = 0;
			foreach($this->check_arr as $label => $msg_arr) {
				foreach($msg_arr as $key => $res_arr) {
					if($res_arr['severity'] == WIZ_ERR) {
						$n_errors++;
					}
				}
			}
			return $n_errors;
		}
	}
?>