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
 * Property object
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class ObjectProperty extends BEAppModel  {
 	
	var $belongsTo = array(
		"Property", 
		"BEObject" => array(
			"foreignKey" => "object_id"
		)
	);

	/**
	 * return smart readable array of object properties
	 * The array keys are the property names
	 *
	 * @param  int  $objectId
	 * @param  boolean $compact  true to compact results as
	 *                           array(
	 *                           	'propertyName' => 'propertyValue', // if property with single choice
	 *                           	'propertyName2' => array( // if property with multiple choice
	 *                           		'propertyValue1',
	 *                           		'propertyValue2'
	 *                           	),
	 *                           )
	 * @return array
	 */
	public function getObjectCustomProperties($objectId, $compact = false) {
	    $objProp = array();
	    $res = $this->find("all", array(
	            "conditions" => array(
	                    "object_id" => $objectId
	            ),
	            "contain" => array('Property')
	    ));
	    foreach ($res as $op) {
	    	if ($op['Property']['multiple_choice']) {
	    		$objProp[$op['Property']['name']][] = ($compact)? $op['ObjectProperty']['property_value'] : $op['ObjectProperty'];
	    	} else {
	    		$objProp[$op['Property']['name']] = ($compact)? $op['ObjectProperty']['property_value'] : $op['ObjectProperty'];
	    	}
	    }
	    return $objProp;
	}

	/**
	 * passed an array of BEdita objects add 'customProperties' key with array of custom properties
	 *
	 * @param  array $objects
	 * @param  array $options accept 'compact' (default true) to compact or not custom prop array
	 * @return array $objects with added 'customProperties' key
	 */
	public function objectsCustomProperties(array $objects, array $options) {
		$options = array_merge(array('compact' => true), $options);
		foreach ($objects as &$obj) {
			$obj['customProperties'] = $this->getObjectCustomProperties($obj['id'], $options['compact']);
		}
		return $objects;
	}

}
 
?>