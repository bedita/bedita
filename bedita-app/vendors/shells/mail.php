<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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

App::import('Core', 'Controller');
App::import('Component', 'BeMail');

require_once 'bedita_base.php';

/**
 * Default shell script for email notifications and newsletters.
 * To put in cron/crontab for normal use, launch at prompt using "./cake.sh mail"
 * WARNING: before using the script check your mail settings
 *   - $config['mailOptions'] basic mail params like sender, reply-to, signature... in config/bedita.cfg.php
 * 	 - $config['smtpOptions'] smtp settings ... in config/bedita.sys.ph
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class MailShell extends BeditaBaseShell {
	
	protected $BeMail;
	protected $Controller;

	/**
	 * startup method for MailShell, initialize BeMail Component
	 * override startup method in Shell
	 * 
	 * @return 
	 */
	function startup() {
		$this->initConfig();
		if (!empty($this->Dispatch->shellCommand) && $this->Dispatch->shellCommand != "main") {
			$this->_welcome();
		}
		$this->Controller = new Controller();
		$this->Controller->view = "Smarty";
		$this->BeMail = new BeMailComponent(); 
		$this->BeMail->startup($this->Controller);
	}

	
	function main() {
		$timeout = (!empty($this->params["timeout"]))? $this->params["timeout"] : Configure::read("newsletterTimeout");
		try {
			$this->BeMail->notify($timeout);
		} catch (BeditaException $ex) {
			$this->log("Error: " . $ex->errorTrace());
		}
		
		$msgIdsBlocked = $this->BeMail->getMessagesBlocked($timeout);
		
		try {
		
			$msgIds = $this->BeMail->lockMessages();
			$this->BeMail->createJobs($msgIds);
			$msgIds = array_merge($msgIds,$msgIdsBlocked);
			$this->BeMail->sendQueuedJobs($msgIds);
				
		} catch (BeditaException $ex) {
			$this->log("Error: " . $ex->errorTrace());
		}
	
	}
	
	
	
	function help() {
		$this->out("Shell script to send notifications and newsletters");
	}
	
}
?>