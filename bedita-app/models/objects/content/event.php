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

/**
 * Event content. Associated to items DateItem (event start_date and end_date) and GeoTag (event location)
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
													"Permission",
													"ObjectProperty",
													"LangText",
													"RelatedObject",
													"Annotation",
													"Category",
													"Alias",
													"Version" => array("User.realname", "User.userid"),
													"GeoTag"
													),
									"DateItem"),
				"default" 	=> array("BEObject" => array("ObjectProperty", "LangText", 
								"ObjectType", "Category", "RelatedObject", "Annotation", "GeoTag"),
								"DateItem"),
				"minimum" => array("BEObject" => array("ObjectType")),
		
				"frontend" => array("BEObject" => array("LangText", 
														"UserCreated", 
														"RelatedObject", 
														"Category", 
														"Annotation",
														"GeoTag",
														"ObjectProperty"), 
									"DateItem")
	);
    
	var $actsAs 	= array(
			'CompactResult' 		=> array('DateItem'),
			'DeleteObject' 			=> 'objects',
	);
	
	public $objectTypesGroups = array("leafs", "related", "tree");

	var $hasMany = array(
			'DateItem' =>
				array(
					'className'		=> 'DateItem',
					'foreignKey'	=> 'object_id',
					'dependent'		=> true
				)
		) ;

	function afterSave() {
		return $this->updateHasManyAssoc();
	}

    /**
     * Return an array of column types to transform (cast) for generic BEdita object type
     * Used to build consistent REST APIs
     *
     * In general it returns all castable fields from BEAppObjectModel::apiTransformer() and DateItem
     *
     * Possible options are:
     * - 'castable' an array of fields that the REST APIs should cast to
     *
     * @see BEAppObjectModel::apiTransformer()
     * @param array $options
     * @return array
     */
    public function apiTransformer(array $options = array()) {
        $transformer = parent::apiTransformer($options);
        $transformer['DateItem'] = $this->DateItem->apiTransformer($options);
        return $transformer;
    }

}
