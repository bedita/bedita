<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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
 * SampleObject Model
 * 
 *
 * @version			$Revision: 2566 $
 * @modifiedby 		$LastChangedBy: niki $
 * @lastmodified	$LastChangedDate: 2010-01-27 10:36:17 +0100 (mer, 27 gen 2010) $
 * 
 * $Id: app_controller.php 2566 2010-01-27 09:36:17Z niki $
 */
class SampleObject extends BeditaContentModel {
	
	public $useTable = 'contents';
	
	var $actsAs 	= array('Notify');
	
	public $objectTypesGroups = array("leafs", "related");
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"ObjectProperty",
															"LangText",
															"RelatedObject",
															"Annotation",
															"Category"
															)),
				"default" => array("BEObject" => array("ObjectProperty", 
									"LangText", "ObjectType", "Annotation",
									"Category", "RelatedObject" )),

				"minimum" => array("BEObject" => array("ObjectType"))		
	);

}
?>
