<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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

/**
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

 class VersionTestData extends BeditaTestData {
	var $data =  array(
		"docs" => array (
			0 => array("title" => "revision 1", "description" => "revision 1", "status" => "draft"),
			1 => array("title" => "revision 2", "description" => "revision 2", "status" => "on"),
			2 => array("title" => "revision 3", "description" => "revision 3", "status" => "off", "creator" => "r3"),
			3 => array("title" => "last version", "description" => "", "status" => "on", "creator" => "last"),
		),
	);
}
?>