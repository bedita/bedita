<?php
/**
 * BEdita - a semantic content management framework
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * ------------------------------------------------------------------------
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
 * ------------------------------------------------------------------------
 * @link			http://www.bedita.com
 * @version			$Revision: $
 * @modifiedby 		$LastChangedBy: $
 * @lastmodified	$LastChangedDate: $
 * 
 * $Id: $
 */
class GroupsController extends AppController {
	var $name = 'Groups';

	var $helpers = array();
	var $components = array('Session');

	// This controller does not use a model
	 var $uses = array("Group");
	
	/**
	 * Torna l'elenco semplice dei gruppi filtrando sulla parte iniziale della stringa.
	 * Utilizzata dagli autocomplete dei nomi gruppi.
	 * Ad eccezione del gruppo "administrator"
	 *
  	 * @param integer $q		parte iniziale stringa gruppo da tornare
  	 * @param integer $d		dimensione lista da tornare
	 * 
	 */
	function names($q = "", $d = 50) {
	 	// Setup parametri
		$this->setup_args(
			array("q", "string", &$q), 
			array("d", "integer", &$d)
		) ;
		$q = trim($q) ;
		
		// Preleva i dati
		$names = $this->Group->findAll("name LIKE '{$q}%' AND name <> 'administrator'",array("name"), "name", $d, 1, 0) ;
		
		// visualizza
		$this->set('names', 		$names);
		
		$this->render(null, "empty", null) ;
	}
}



?>