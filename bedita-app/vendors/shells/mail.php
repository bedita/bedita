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

App::import('Core', 'Controller');
App::import('Controller', 'App'); // BeditaException
App::import('Component', 'BeMail');

/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

class MailShell extends Shell {
	
	var $BeMail;

	/**
	 * startup method for MailShell, initialize BeMail Component
	 * override startup method in Shell
	 * 
	 * @return 
	 */
	function startup() {
		if (!empty($this->Dispatch->shellCommand) && $this->Dispatch->shellCommand != "main") 
			$this->_welcome();
		
		$this->BeMail = new BeMailComponent();
		$this->BeMail->startup(); 
	}

	
	function main() {

		try {
			$msgIds = $this->BeMail->lockMessages();
			$this->BeMail->createJobs($msgIds);
			$this->BeMail->sendQueuedJobs($msgIds);
				
		} catch (BeditaException $ex) {
			$this->log("Error: " . $ex->errorTrace());
		}
	
	}
	
	
	function help() {
		$this->out("Shell script to send newsletters");
	}
	
}
?>
