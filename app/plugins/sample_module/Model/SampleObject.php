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
 * SampleObject Model
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class SampleObject extends BeditaContentModel {
	
	public $useTable = 'contents';
	
	var $actsAs 	= array('Notify');
	
	public $objectTypesGroups = array("leafs", "related");
	
	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permission",
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