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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Event extends BeditaContentModel
{
    var $useTable = 'contents';

	protected $modelBindings = array( 
				"detailed" =>  array(
								"BEObject" => array("ObjectType", 
													"UserCreated", 
													"UserModified", 
													"Permissions",
													"ObjectProperty",
													"LangText",
													"RelatedObject",
													"Annotation",
													"Category"
													),
									"DateItem", "GeoTag"),
				"default" 	=> array("BEObject" => array("ObjectProperty", "LangText", 
								"ObjectType", "Category", "RelatedObject", "Annotation"),
								"DateItem", "GeoTag"),
				"minimum" => array("BEObject" => array("ObjectType"))
	);
    
	var $actsAs 	= array(
			'CompactResult' 		=> array('DateItem', 'GeoTag'),
			'DeleteObject' 			=> 'objects',
	); 	 

	var $hasMany = array(
			'DateItem' =>
				array(
					'className'		=> 'DateItem',
					'foreignKey'	=> 'object_id',
					'dependent'		=> true
				),
			'GeoTag' =>
				array(
					'foreignKey'	=> 'object_id',
					'dependent'		=> true
				)
		) ;

	function afterSave() {
		return $this->updateHasManyAssoc();
	}

}
?>
