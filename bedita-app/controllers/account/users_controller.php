<?php
/***************************************************************************
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
 ***************************************************************************/

/**
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: $
 * @modifiedby 		$LastChangedBy: $
 * @lastmodified	$LastChangedDate: $
 * 
 * $Id: $
 * 
 */

class UsersController extends AppController {
	var $name = 'Users';

	var $helpers = array();
	var $components = array('Session');

	// This controller does not use a model
	 var $uses = array("User");
	
	/**
	 * Torna l'elenco semplice degli userid filtrando sulla parte iniziale della stringa.
	 * Utilizzata dagli autocomplete degli userid.
	 *
  	 * @param integer $q		parte iniziale stringa userid da tornare
  	 * @param integer $d		dimensione lista da tornare
	 * 
	 */
	function userids($q = "", $d = 20) {
	 	// Setup parametri
		$this->setup_args(
			array("q", "string", &$q), 
			array("d", "integer", &$d)
		) ;
		$q = trim($q) ;
		
		// Preleva i dati
		$userids = $this->User->findAll("userid LIKE '{$q}%'",array("userid"), "userid", $d, 1, 0) ;
		
		// visualizza
		$this->set('userids', 		$userids);
		
		$this->render(null, "empty", null) ;
	}
}



?>