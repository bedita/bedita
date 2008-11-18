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
class Book extends BeditaContentModel 
{
	public $searchFields = array("title" => 10 , "description" => 6, 
		"subject" => 4, "abstract" => 4, "isbn" => 10, "editor" => 4);	
	
	var $actsAs 	= array(
			'CompactResult' 		=> array(),
			'SearchTextSave',
			'ForeignDependenceSave' => array('BEObject', 'Content'),
			'DeleteObject' 			=> 'objects',
	); 
	
	var $hasOne= array(
			'BEObject' => array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'Content' => array(
					'className'		=> 'Content',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				)
		);
	

	protected $modelBindings = array( 
				"detailed" =>  array("BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"ObjectProperty",
															"LangText",
															"RelatedObject",
															"Category"
															),
									 "Content"
									),
				"default" => array("BEObject" => array("ObjectProperty", 
									"LangText", "ObjectType", 
									"Category", "RelatedObject"), "Content"),
									
				"minimum" => array("BEObject" => array("ObjectType"))
	);
	
//	TODO: validation rules for year, ISBN
//	var $validate = array(
//		"subject" => array(
//			"rule" 			=> array('custom', '/.+/') ,
//			"required" 		=> true,
//			"message" 		=> "Subject required"
//		)
//	);
	
	
	function beforeValidate() {
		$this->checkNumber('year');
        return true;
	}
	
}
?>
