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
 * Document content
 */
class Document extends BeditaContentModel {
	var $useTable = 'contents';

    protected $modelBindings = array(
        'detailed' => array(
            'BEObject' => array(
                'ObjectType',
                'UserCreated',
                'UserModified',
                'Permission',
                'ObjectProperty',
                'LangText',
                'RelatedObject',
                'Annotation',
                'Category',
                'Alias',
                'Version' => array('User.realname', 'User.userid'),
                'GeoTag'
            )
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'ObjectType',
                'Annotation',
                'Category',
                'RelatedObject',
                'GeoTag'
            )
        ),
        'minimum' => array('BEObject' => array('ObjectType')),
        'frontend' => array(
            'BEObject' => array(
                'LangText',
                'UserCreated',
                'RelatedObject',
                'Category',
                'Annotation',
                'GeoTag',
                'ObjectProperty'
            )
        ),
        'api' => array(
            'BEObject' => array(
                'LangText',
                'Category',
                'GeoTag',
                'ObjectProperty'
            )
        )
    );

	var $actsAs = array();

	public $objectTypesGroups = array('leafs', 'related', 'tree');

}
