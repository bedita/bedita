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
 * Comment annotation
 */
class Comment extends BeditaAnnotationModel
{
	var $useTable = 'annotations';

	var $actsAs = array(); 

	public $objectTypesGroups = array("nodashboard");

    protected $modelBindings = array(
        'detailed' => array(
            'BEObject' => array(
                'ObjectType',
                'UserCreated',
                'UserModified',
                'RelatedObject',
                'Version' => array('User.realname', 'User.userid'),
                'GeoTag'
            ),
            'ReferenceObject'
        ),
        'default' => array(
            'BEObject' => array('ObjectType', 'GeoTag'),
            'ReferenceObject'
        ),
        'minimum' => array('BEObject' => array('ObjectType')),
        'frontend' => array('BEObject' => array('RelatedObject', 'GeoTag'))
    );

	var $validate = array(
			'author' => array(
				'required' 		=> true
	   		),
			'description' => array(
				'required' 		=> true
	   		),
	   		'email' => array(
	   			'rule' 			=> 'email',
				'required' 		=> true,
	   			'message' 		=> 'email not valid'
	   		),
	   		'url' => array (
	   			'rule' 		 	=> 'url',
	   			'required' 		=> false,
	   			'allowEmpty'	=> true,
	   			'message' 		=> 'URL not valid'
	   		)
	   );

	   
	function beforeValidate() {
       	$data = &$this->data[$this->name] ;
        if(isset($data['url']) && $data['url'] == "http://") {
        	unset($data['url']);
        }
   	}
}
