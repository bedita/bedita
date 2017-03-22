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
 * Publication data
 *
 */
class Area extends BeditaCollectionModel
{

	var $actsAs = array(
		'ForeignDependenceSave' => array('SectionDummy'),
		'RemoveDummyName'
	);

	public $searchFields = array(
		"title" => 10,
		"nickname" => 8,
		"description" => 6,
		"public_name" => 10,
		"public_url" => 8,
		"note" => 2
	);

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
                'Version' => array('User.realname', 'User.userid')
            ),
            'SectionDummy'
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'RelatedObject',
                'ObjectType'
            ),
            'SectionDummy'
        ),
        'minimum' => array('BEObject' => array('ObjectType')),
        'frontend' => array(
            'BEObject' => array(
                'LangText',
                'RelatedObject',
                'ObjectProperty'
            )
        ),
        'api' => array(
            'BEObject' => array(
                'LangText',
                'ObjectProperty'
            )
        )
    );

	var $hasOne = array(
			'BEObject' => array(
					'className'		=> 'BEObject',
					'conditions'   => '',
					'foreignKey'	=> 'id',
					'dependent'		=> true
				),
			'SectionDummy' => array(
					'className'		=> 'SectionDummy',
					'foreignKey'	=> 'id'
				),
	) ;

	var $validate = array(
		'title'	=> array(
			'rule' => 'notEmpty',
			'required' => true
		),
		'public_url' => array(
			'rule' => 'url',
			'message' => 'url not valid',
			'allowEmpty' => true
		),
		'staging_url' => array(
			'rule' => 'url',
			'message' => 'url not valid',
			'allowEmpty' => true
		),
		'email' => array(
			'rule' => 'email',
			'message' => 'email not valid',
			'allowEmpty' => true
		),
		'stats_provider_url' => array(
			'rule' => 'url',
			'message' => 'url not valid',
			'allowEmpty' => true
		)
	);

    public $objectTypesGroups = array('related');

	function afterSave($created) {
		if (!$created)
			return ;

		$tree = ClassRegistry::init('Tree', 'Model');
		$tree->appendChild($this->id, null) ;
	}

    /**
     * Return area data (id, title, status) by $areaId id.
     * If $details param is true, extra data is retrieved: count (objects inside area by type)
     * 
     * @param int $areaId area id
     * @param bool $details load details
     * @return array area data
     */
    public function getArea($areaId, $details = false) {
        $areas = ClassRegistry::init('BEObject')->find('all', array(
            'fields' => array('id', 'title', 'status'),
            'conditions' => array(
                'id' => $areaId,
                'object_type_id' => Configure::read('objectTypes.area.id')
            ),
            'contain' => array()
        ));
        $result = array();
        foreach ($areas as $area) {
            $result[$area['BEObject']['id']] = $area['BEObject'];
            if ($details) {
                $result[$area['BEObject']['id']]['count'] = $this->treeCountArea($area['BEObject']['id']);
            }
        }
        return $result;
    }

    /**
     * Return areas data (id, title, status).
     * If $details param is true, extra data is retrieved: count (objects inside area by type)
     * 
     * @param bool $details load details
     * @return array areas
     */
    public function getAreas($details = false) {
        $areas = ClassRegistry::init('BEObject')->find('all', array(
            'fields' => array('id', 'title', 'status'),
            'conditions' => array('object_type_id' => Configure::read('objectTypes.area.id')),
            'contain' => array()
        ));
        $result = array();
        foreach ($areas as $area) {
            $result[$area['BEObject']['id']] = $area['BEObject'];
            if ($details) {
                $result[$area['BEObject']['id']]['count'] = $this->treeCountArea($area['BEObject']['id']);
            }
        }
        return $result;
    }

    /**
     * Return count of objects in area $areaId or its descendant: group by type ('byType') and total ('allTypes').
     *
     * @param int $areaId area id
     * @return array summary data about area $areaId
     */
    public function treeCountArea($areaId) {
        $result = ClassRegistry::init('Tree')->find('all', array(
            'fields' => 'DISTINCT BEObject.object_type_id',
            'joins' => array(
                array(
                    'table' => 'objects',
                    'alias' => 'BEObject',
                    'type' => 'inner',
                    'conditions' => array(
                        'BEObject.id = Tree.id',
                        'Tree.area_id' => $areaId,
                    )
                )
            ),
            'contain' => array()
        ));
        $descendantsObjectTypeIds = Set::extract('/BEObject/object_type_id', $result);
        sort($descendantsObjectTypeIds);
        $result = array();
        $total = 0;
        foreach ($descendantsObjectTypeIds as $objectTypeId) {
            $count = $this->treeCountAreaPerType($areaId, $objectTypeId);
            $result['byType'][Configure::read('objectTypes.'.$objectTypeId.'.name')] = $count;
            $total+= $count;
        }
        $result['allTypes'] = $total;
        return $result;
    }

    /**
     * Return count of objects (descendants) in $areaId by type $objectTypeId
     * 
     * @param int $areaId area id
     * @param int $objectTypeId object type id
     * @return int count of objects
     */
    private function treeCountAreaPerType($areaId, $objectTypeId) {
        return ClassRegistry::init('Tree')->find('count', array(
            'conditions' => array(
                'Tree.area_id' => $areaId
            ),
            'joins' => array(
                array(
                    'table' => 'objects',
                    'alias' => 'BEObject',
                    'type' => 'inner',
                    'conditions' => array(
                        'BEObject.id = Tree.id',
                        'BEObject.object_type_id' => $objectTypeId
                    )
                )
            )
        ));
    }
}
