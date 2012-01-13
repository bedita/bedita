<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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
 * BEdita docs
 */	
class PagesController extends FrontendController {
	
	var $helpers = array('BeFront', 'BeTime');
	var $components = array('BeTree');
	var $uses = array('BEObject','Tree') ;	
	
	/**
	* load common data, for all frontend pages...
	*/ 
	function beditaBeforeFilter() {

		// first menu level
		$menu1 = $this->loadSectionsTree(Configure::read("frontendAreaId"), false, array(), 1);
		$this->set("menu", $menu1);
				
		// setup rss feeds
		$this->set('feedNames', $this->Section->feedsAvailable(Configure::read("frontendAreaId")));
	}
}

?>