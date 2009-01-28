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

 */
class StatisticsController extends ModulesController {
	var $name = 'Statistics';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Document', 'Tree') ;
	protected $moduleName = 'statistics';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {    	
    	$conf  = Configure::getInstance() ;
		$this->paginatedList($id, @$filter, $order, $dir, $page, $dim);
	 }
	
	 public function view($id = null) {
	 	
		function bho() {	
			$sql 		=  "SELECT *  FROM objects" ;
			$results 	= $this->query($sql);
			$res = array();
			for ($i=0; $i < count($results) ; $i++) {
	            $results[$i]  = $this->am($results[$i]);
			}
			return $res ;
		}
	
		$this->viewObject($this->Document, $id);
	 }










}	

?>