<?php
/**
 * BEdita - a semantic content management framework
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * 
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 * See the Affero GNU General Public License for more details.
 * 
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita.  If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 * @link			http://www.bedita.com - BEdita project
 * @version			$Rev$
 * @modifiedby		$Author$
 * @lastmodified	$Date$
 * 
 * $Id$
 */

class PagesController extends ModulesController {
	
	var $components = array('Session', 'Cookie');
	
	var $uses = array();

	 function display() {
	 	$this->action = "index" ;
	 }
	 
	 function changePasswd() {
	 }
	
	function changeLang($lang = null) {
		if (!empty($lang)) {
			$this->Session->write('Config.language', $lang);
			$this->Cookie->write('bedita.lang', $lang, null, '+350 day'); 
		}
		$this->redirect($this->referer());
	}
	 
	 function login() {
	 }

	 protected	function beditaBeforeFilter() {
		if($this->action === 'changeLang') { // skip auth check, on lang change
			$this->skipCheck = true;
		}
	}	 
	 
}

?>