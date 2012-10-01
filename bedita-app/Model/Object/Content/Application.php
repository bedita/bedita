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

App::uses("BeditaStreamModel", "Model/Object/Base");

/**
 * Multimedia application object
 *
 * @version			$Revision: 1627 $
 * @modifiedby 		$LastChangedBy: bato $
 * @lastmodified	$LastChangedDate: 2009-01-02 20:21:19 +0100 (ven, 02 gen 2009) $
 *
 * $Id: video.php 1627 2009-01-02 19:21:19Z bato $
 */
class Application extends BeditaStreamModel
{
	var $actsAs = array();

	public $objectTypesGroups = array("multimedia", "leafs", "related");

	public $applicationType = array();

	public function  __construct() {
		parent::__construct();
		$appType = Configure::read("validate_resource.mime.Application");
		foreach ($appType as &$a) {
			foreach ($a["mime_type"] as &$v) {
				$v = trim(stripslashes($v), "/");
			}
		}
		$this->applicationType = $appType;
	}
}
?>