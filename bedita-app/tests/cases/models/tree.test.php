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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
require_once ROOT . DS . APP_DIR. DS. 'tests'. DS . 'bedita_base.test.php';

class TreeTestCase extends BeditaTestCase {

 	var $uses		= array('Tree') ;
 	
	public function testAllTree() {
		$tree = $this->Tree->getAll() ;
		pr("Carica l'albero completo") ;
		$this->assertEqual($tree, unserialize($this->data['resultAllTree1']));
	} 

	public function testAreeSezioniTree() {
		$conf  = Configure::getInstance() ;
		$tree = $this->Tree->getAll(null, null, null, array($conf->objectTypes['area']["id"], $conf->objectTypes['section']["id"])) ;
		pr("Carica l'albero con solo aree e sezioni") ;
		$this->assertEqual($tree,unserialize($this->data['resultTree2']));
		pr(serialize($tree));
	} 

	public function testBranchTree() {
		$tree = $this->Tree->getAll(3) ;
		$this->assertEqual($tree,unserialize($this->data['resultTree4']));
	} 

	public function testStatusTree() {
		pr("Carica gli oggetti con status 'on' ") ;
		$tree = $this->Tree->getAll(null, null, 'on') ;
		$compare = $this->Tree->getAll() ;
		$this->assertEqual($tree, $compare);
		pr("Carica gli oggetti con status 'off' (insieme vuoto) ") ;
		$tree = $this->Tree->getAll(null, null, 'off') ;
		$this->assertEqual($tree, array());
	} 

	public function __construct () {
		parent::__construct('Tree', dirname(__FILE__)) ;
	}	
	
}
?> 