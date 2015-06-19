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
 * Section of website/publication
 */
class Section extends BeditaCollectionModel
{
	var $actsAs = array();

	public $searchFields = array(
        "title" => 10,
        "nickname" => 8,
        "description" => 6,
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
                'Alias',
                'Annotation',
                'Category',
                'Version' => array('User.realname', 'User.userid'),
                'GeoTag'
            ),
            'Tree'
        ),
        'default' => array(
            'BEObject' => array(
                'ObjectProperty',
                'LangText',
                'ObjectType',
                'Category',
                'RelatedObject',
                'GeoTag'
            ),
            'Tree'
        ),
        'minimum' => array('BEObject' => array('ObjectType')),
        'frontend' => array(
            'BEObject' => array(
                'LangText',
                'Category',
                'RelatedObject',
                'ObjectProperty'
                ),
            'Tree'
        ),
        'api' => array(
            'BEObject' => array(
                'LangText',
                'Category',
                'ObjectProperty'
                ),
            'Tree'
        )
    );

	var $validate = array(
		'title'	=> array(
			'rule' => 'notEmpty',
			'required' => true
		),
		'parent_id'	=> array(
			'rule' => 'notEmpty'
		)
	) ;


	function afterSave($created) {
		if (!$created) {
			return;
		}
		$tree = ClassRegistry::init('Tree');
		if ($tree->appendChild($this->id, $this->data[$this->name]['parent_id']) === false) {
			return false;
		}
		// save Tree.menu
		$menu = (!empty($this->data[$this->name]['menu']))? 1 : 0;
		$this->Tree->saveMenuVisibility($this->id, $this->data[$this->name]["parent_id"], $menu);
		return true;
	}

	public function feedsAvailable($areaId) {
        $feeds = $this->find('all', array(
                'conditions' => array('Section.syndicate' => 'on', 'BEObject.status' => 'on', "Tree.object_path LIKE '/$areaId/%'"),
                'fields' => array('BEObject.nickname', 'BEObject.title'),
                'contain' => array("BEObject", "Tree"))
        );
        $feedNames = array();
        foreach ($feeds as $f) {
        	$feedNames[] = $f['BEObject'];
        }

        return $feedNames;
    }

    /**
     * Promote a section to publication (area)
     * All section's tree structure is maintained
     *
     * @param int $sectionId, the section id to promote
     */
    public function promoteToArea($sectionId) {
    	// promote section to area (remove any categories and custom properties)
    	$title = $this->BEObject->field("title", array("id" => $sectionId));
    	$data = array(
    		"id" => $sectionId,
    		"object_type_id" => Configure::read("objectTypes.area.id"),
    		"title" => $title,
    		"Category" => array(),
    		"ObjectProperty" => array()
    	);
    	$Area = ClassRegistry::init("Area");
    	if (!$Area->save($data)) {
    		throw new BeditaException(__("Error promoting section to publication", true), array_merge(array("id" => $sectionId), $Area->validationErrors));
    	}

    	// update tree
		$treeRow = $this->Tree->find("first", array(
			"conditions" => array("id" => $sectionId)
		));
		// remove old section's row
		$this->Tree->delete($treeRow["Tree"]["object_path"]);
		// insert new publication row
		$sectionObjectPath = $treeRow["Tree"]["object_path"];
		$treeRow["Tree"]["area_id"] = $sectionId;
		$treeRow["Tree"]["parent_id"] = null;
		$treeRow["Tree"]["object_path"] = "/" . $sectionId;
		$treeRow["Tree"]["parent_path"] = "/";
		if (!$this->Tree->save($treeRow)) {
			throw new BeditaException(__("Error promoting section's row relative to trees table", true));
		}

		// update children paths
		$rowsToUpdate = $this->Tree->find("all", array(
			"conditions" => array("object_path LIKE" => "%/" . $sectionId . "/%")
		));

		if (!empty($rowsToUpdate)) {
			$regExpPath = str_replace("/", "\/", $sectionObjectPath);
			$objectPathPattern = "/(^" . $regExpPath  . ")(\/)(.*)/";
			$parentPathPattern = "/(^" . $regExpPath  . "$|^" . $regExpPath  . "(\/))(.*)/";
			$replacement = "/".$sectionId."$2$3";
			foreach ($rowsToUpdate as $row) {
				$row["Tree"]["area_id"] = $sectionId;
				$row["Tree"]["object_path"] = preg_replace($objectPathPattern, $replacement, $row["Tree"]["object_path"]);
				$row["Tree"]["parent_path"] = preg_replace($parentPathPattern, $replacement, $row["Tree"]["parent_path"]);
				$this->Tree->create();
				if (!$this->Tree->save($row)) {
					throw new BeditaException(__("Error updating tree", true), $row["Tree"]);
				}
			}
		}
	}

    /**
     * Return an array of column types to transform (cast) for generic BEdita object type
     * Used to build consistent REST APIs
     *
     * In general it returns all castable fields from BEAppObjectModel::apiTransformer() and add transformer results from Tree
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
        $transformer += $this->Tree->apiTransformer($options);
        return $transformer;
    }

}
