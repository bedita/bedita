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
	require APP_DIR . DS . 'libs' . DS . 'be_system.php';

	class BeditaInstallationWizard {

		var $smarty;
		var $check_arr = array();
		var $besys;
		var $steps = array(
			'Environment Settings',
			'Database Configuration',
			'Bedita Admin',
			'Summary',
			'Finish'
		);

		public function BeditaInstallationWizard() {
			$this->besys = new BeSystem();
		}

		public function start() {
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
			}
		}

		private function initSmarty() {
			$this->smarty = new Smarty();
			$this->smarty->template_dir = APP_DIR . DS . 'views' . DS . 'install';
			$this->smarty->compile_dir = APP_DIR . DS . 'tmp' . DS . 'smarty' . DS . 'compile';
			$this->smarty->cache_dir = APP_DIR . DS . 'tmp' . DS . 'smarty' . DS . 'cache';
		}

		private function page_envstart() {
			if($this->performSmartyCheck()) {
				$this->initSmarty();
				$this->performCakeCheck();
				$this->smarty->assign('steps',$this->steps);
				$this->smarty->assign('results_smarty',$this->check_arr['smarty']);
				$this->smarty->assign('results_cake',$this->check_arr['cake']);
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
			include(CORE_PATH . 'cake' . DS . 'bootstrap.php');
			App::import('ConnectionManager');
			$db = ConnectionManager::getDataSource('default');
			$this->smarty->assign('database_config',$db->config);
			$this->smarty->assign('is_connected',$db->isConnected() ? "y" : "n");
			$this->smarty->assign('dbfile',$filename);
			$this->smarty->assign('dbfile_writable',$this->besys->checkWritable($filename) ? "y" : "n");
			$this->smarty->display('database.tpl');
		}

		private function page_beadmin() {
			$this->initSmarty();
			$this->smarty->assign('steps',$this->steps);
//			include(CORE_PATH . 'cake' . DS . 'bootstrap.php');
			$this->smarty->display('admin.tpl');
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

		private function _checkdirwriteable($checklabel,$label,$path) {
			$result = $this->_checkdir($checklabel,$label,$path);
			if($result) {
				if(!$this->besys->checkWritable($path)) {
					$result = false;
					$this->check_arr[$checklabel][] = array('label' => $label, 'result' => $result, 'severity' => WIZ_INFO, 'description' => 'dir not writeable');
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

		private function performSmartyCheck() {

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

		private function performCakeCheck() {

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
	}
?>