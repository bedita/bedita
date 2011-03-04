<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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
 * BeditaInstallationWizard class
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

	define('WIZ_INFO','INFO');
	define('WIZ_WARN','WARN');
	define('WIZ_ERR','ERROR');

	require ROOT . DS . 'vendors' . DS . 'smarty' . DS . 'libs' . DS . 'Smarty.class.php';
	require ROOT . DS . APP_DIR . DS . 'libs' . DS . 'be_system.php';

	class BeditaInstallationWizard {

		var $smarty;
		var $check_arr = array();
		var $besys;
		var $steps = array(
			'Environment Settings',
			'Database Configuration',
			'Bedita Admin',
			'Finish'
		);

		public function BeditaInstallationWizard() {
			$this->besys = new BeSystem();
		}

		public function start() {
			$wizard_finished = false;
			$p = (empty($_POST['page'])) ? "1" : $_POST['page'];
			switch ($p) {
				default: case "1":
					$this->page_envstart();
					break;
				case "2":
					$this->page_dbconn();
					break;
				case "3":
					$this->page_beadmin();
					break;
				case "4":
					$this->page_finish();
					break;
				case "5":
					$this->page_endinstall();
					$wizard_finished = true;
					break;
			}
			return $wizard_finished;
		}

		private function initSmarty() {
			$this->smarty = new Smarty();
			$this->smarty->template_dir = ROOT . DS . APP_DIR . DS . 'views' . DS . 'install';
			$this->smarty->compile_dir = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'smarty' . DS . 'compile';
			$this->smarty->cache_dir = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'smarty' . DS . 'cache';
		}

		private function page_envstart() {
			if($this->_checksmarty()) {
				$this->initSmarty();
				$this->_checkcakephp();
				$this->_checkinstalldir();
				$this->smarty->assign('steps',$this->steps);
				$this->smarty->assign('results_smarty',$this->check_arr['smarty']);
				$this->smarty->assign('results_cake',$this->check_arr['cake']);
				$this->smarty->assign('results_install',$this->check_arr['install']);
				$this->smarty->assign('n_errors',$this->_count_errors());
				$this->smarty->display('setup.tpl');
			} else {
				echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
				echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">';
				echo '<head>';
				echo '<title>BEdita installation wizard</title>';
				echo '<style>';
				echo 'ul { list-style: none; }';
				echo '.INFO { color:green}';
				echo '.WARN { color:yellow }';
				echo '.ERROR { color:red }';
				echo 'li.done { color: green; }';
				echo 'li.curr { color: blue; }';
				echo 'li.todo { color: orange; }';
				echo 'div { margin:10px }';
				echo '</style>';
				echo '</head>';
				echo '<body>';
				echo '<h1>BEdita installation wizard</h1>';
				echo '<div id="map" style="float:left">';
				echo '<ul>';
				foreach($this->steps as $key => $value) {
					$c = ($key > 0) ? "todo" : "curr";
					echo '<li class="' . $c . '">' . $value . '</li>';
				}
				echo '</ul>';
				echo '</div>';
				echo '<div style="float:left">';
				echo '<h1>Smarty Php</h1>';
				echo '<h2>Directory Permits Error</h2>';
				foreach($this->check_arr['smarty'] as $k => $v) {
					echo '<p><span class="' . $v['severity'] . '">[' . $v['severity'] . ']</span>: <code>' . $v['label'] . '</code>: <span class="' . $v['severity'] . '">' . $v['description'] . '</span></p>';
				}
				echo '<p>Please check directories permits. Smarty temporary directories must be writeable.</p>';
				echo '<form method="post" action="index.php"><input type="submit" value="Refresh" /></form>';
				echo '</div>';
				echo '</body>';
				echo '</html>';
			}
			
		}

		private function page_dbconn() {
			$this->initSmarty();
			$filename = ROOT . DS . APP_DIR . DS ."config" . DS . "database.php";
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
			require_once(CORE_PATH . 'cake' . DS . 'bootstrap.php');
			App::import('ConnectionManager');
			$db = ConnectionManager::getDataSource('default');
			if(!empty($_POST['action']) && ($_POST['action'] == 'initdb')) {
				$this->_initdb($db);
			}
			if(!empty($db)) {
				$this->smarty->assign('database_sources',$db->listSources());
				$this->smarty->assign('database_config',$db->config);
				$this->smarty->assign('is_connected',$db->isConnected() ? "y" : "n");
			}
			$this->smarty->assign('dbfile',$filename);
			$this->smarty->assign('dbfile_writable',$this->besys->checkWritable($filename) ? "y" : "n");
			$this->smarty->display('database.tpl');
		}

		private function page_beadmin() {
			require_once(CORE_PATH . 'cake' . DS . 'bootstrap.php');
			require_once(ROOT . DS . APP_DIR . DS . 'config' . DS . 'bedita.ini.php');
			$this->initSmarty();
			$this->smarty->assign('steps',$this->steps);
			$config = Configure::getInstance();
			$baseUrl = $config->read('App.baseUrl');
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
				if($admin_data_ok) {
					$userdata = array(
						'id' => '1',
						'realname' => trim($_POST['data']['admin']['user']),
						'userid' => trim($_POST['data']['admin']['user']),
						'passwd' => md5(trim($_POST['data']['admin']['password']))
					);
					if(!$this->_saveuser($userdata)) {
						$this->smarty->assign('usercreationerr',true);
					} else {
						$this->smarty->assign('userid',$userdata['userid']);
						$this->smarty->assign('usercreationok',true);
					}
				}
				if($this->_checkmodrewritephp() != $this->_checkmodrewritecakephp($baseUrl)) {
					$this->_applymodrewrite($this->_checkmodrewritephp($baseUrl));
					$baseUrl = $config->read('App.baseUrl');
				}
			}
			$this->smarty->assign('bedita_url',$config->read('beditaUrl'));
			$this->smarty->assign('bedita_url_check',$this->_checkurl($config->read('beditaUrl')));
			$this->smarty->assign('media_root',$config->read('mediaRoot'));
			$this->smarty->assign('media_root_check',$this->_checkmediaroot($config->read('mediaRoot')));
			$this->smarty->assign('media_url',$config->read('mediaUrl'));
			$this->smarty->assign('media_url_check',$this->_checkurl($config->read('mediaUrl')));
			$this->smarty->assign('mod_rewrite_php',$this->_checkmodrewritephp());
			$this->smarty->assign('mod_rewrite_cakephp',$this->_checkmodrewritecakephp($baseUrl));
			$this->smarty->display('admin.tpl');
		}

		private function page_finish() {
			require_once(CORE_PATH . 'cake' . DS . 'bootstrap.php');
			require_once(ROOT . DS . APP_DIR . DS . 'config' . DS . 'bedita.ini.php');
			$this->initSmarty();
			$this->smarty->assign('steps',$this->steps);
			$this->smarty->display('finish.tpl');
		}

		private function page_endinstall() {
			$filename = ROOT . DS . "setup" . DS . "install.done";
			$filedata = array("BEdita installed on " . strtotime("now"));
			file_put_contents($filename,$filedata);
		}

		private function _checkurl($url) {
			$r = array();
			$result = @get_headers($url);
			if(empty($result) || !$result) {
				$r['severity'] = WIZ_ERR;
				$r['status'] = 'Invalid url';
				return $r;
			}
			$status = (!empty($result) && !empty($result[0])) ? $result[0] : "";
			$r['status'] = $status;
			$error_400 = stristr($status,'HTTP/1.1 4');
			$error_500 = stristr($status,'HTTP/1.1 5');
			if(!empty($error_400) || !empty($error_500)) {
				$r['severity'] = WIZ_ERR;
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
				$r['severity'] = WIZ_ERR;
				$r['status'] = 'not found';
			}
			return $r;
		}
		private function _checkdir($checklabel,$label,$path) {
			$result = true;
			if(!$this->besys->checkAppDirPresence($path)) {
				$result = false;
				$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_ERR, 'description' => 'dir not found');
			} else {
				$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_INFO, 'description' => 'dir found');
			}
			return $result;
		}

		private function _checkmodrewritephp() {
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
					$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_ERR, 'description' => 'dir not writeable');
				} else {
					$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_INFO, 'description' => 'dir writeable');
				}
			}
			return $result;
		}

		private function _checkfileconfig($checklabel,$label,$filename,$confdir) {
			if(!$this->besys->checkAppFilePresence($filename)) {
				$this->check_arr[$checklabel][] = array('label' => $label, 'result' => false, 'severity' => WIZ_ERR, 'description' => 'file not found');
				$filesample = $filename.'.sample';
				if(!$this->besys->checkAppFilePresence($filesample)) {
					$this->check_arr[$checklabel][] = array('label' => 'Check presence of sample file: '.$filesample, 'result' => false, 'severity' => WIZ_ERR, 'description' => 'file not found');
				} else {
					if(!$this->besys->checkWritable($confdir)) {
						$this->check_arr[$checklabel][] = array('label' => 'Trying to create from .sample:', 'result' => false, 'severity' => WIZ_ERR, 'description' => 'directory ' . $confdir . ' is not writable');
					} else {
						if(!$this->besys->createFileFromSample($filename)) {
							$this->check_arr[$checklabel][] = array('label' => 'Trying to create from .sample:', 'result' => false, 'severity' => WIZ_ERR, 'description' => 'permission denied');
						} else {
							$this->check_arr[$checklabel][] = array('label' => 'Trying to create from .sample:', 'result' => true, 'severity' => WIZ_INFO, 'description' => 'done');
						}
					}
				}
			} else {
				$this->check_arr[$checklabel][] = array('label' => $label, 'result' => true, 'severity' => WIZ_INFO, 'description' => 'file found');
			}
		}

		private function _checksmarty() {

			// smarty temporary directory must be writable
			$smarty_dir = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'smarty';
			$result = $this->_checkdirwriteable('smarty','Check of smarty dir: '.$smarty_dir,$smarty_dir);

			$smarty_cache_dir = $smarty_dir . DS . 'cache';
			if(!$this->_checkdirwriteable('smarty','Check of smarty cache dir: '.$smarty_cache_dir,$smarty_cache_dir)) {
				$result = false;
			}

			$smarty_compile_dir = $smarty_dir . DS . 'compile';
			if(!$this->_checkdirwriteable('smarty','Check of smarty compile dir: '.$smarty_compile_dir,$smarty_compile_dir)) {
				$result = false;
			}
			return $result;
		}

		private function _checkcakephp() {

			// checking app dir
			$appPath = ROOT . DS . APP_DIR;
			$result = $this->_checkdir('cake','Check of cake app dir: '.$appPath,$appPath);

			$confDir = $appPath.DS."config";

			// checking config files
			$confFile = $confDir.DS."core.php";
			if(!$this->_checkfileconfig('cake','Check of cake config file: '.$confFile,$confFile,$confDir)) {
				$result = false;
			}

			$confFile = $confDir.DS."database.php";
			if(!$this->_checkfileconfig('cake','Check of cake config file: '.$confFile,$confFile,$confDir)) {
				$result = false;
			}

			return $result;
		}

		private function _checkinstalldir() {
			$this->check_arr['install'] = array();
			$result = true;
			$confDir = ROOT . DS . 'setup';
			if(!$this->_checkdirwriteable('install','Check of install dir: '.$confDir,$confDir)) {
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
				if(($line_start == 0) && (stripos($line,"Configure::write('App.baseUrl'")>0) ) {
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

		private function applyDatabaseConfiguration($filename,$db = array()) {
			$c = 1;
			$dbsize = sizeof($db);
			$done = false;
			$filedata = array();
			$filearr = file($filename);
			$line_start = 0;
			foreach($filearr as $line_num => $line) {
				if(($line_start == 0) && (stripos($line,'var $default')>0) ) {
					$line_start = $line_num;
				}
				if($line_start > 0 && !$done) {
					if(stripos($line,');')>0) {
						$line_end = $line_num;
						$line_start = -1;
						$done = true;
						$filedata[]= 'var $default = array(';
						foreach($db as $k => $v) {
							$l = "'$k' => '$v'";
							if($c<$dbsize) {
								$l.= ",";
							}
							$c++;
							$filedata[]=$l;
						}
						$filedata[]= ');';
						$done = true;
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
		}

		private function _saveuser($userdata) {
			$q = "UPDATE users SET userid = '" . $userdata['userid'] . "', realname = '" . $userdata['realname'] . "', passwd = '" . $userdata['passwd'] . "' WHERE id = " . $userdata['id'] . "";
			$db = ConnectionManager::getDataSource('default');
			$db->execute($q);
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

		private function _debug($arr) {
			echo '<pre>'; print_r($arr); echo '</pre>'; exit;
		}
	}
?>