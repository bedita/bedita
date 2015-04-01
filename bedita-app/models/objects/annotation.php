<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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
 * Base annotation
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Annotation extends BEAppModel {

	/**
	 * build thread structure
	 *
	 * @param array $items
	 * @return array
	 */
	public function buildThread($items) {
		$thread = array();
		foreach ($items as $annotation) {
			$annotation['children']	= array() ;
			$this->putAnnotationInThread($thread, $annotation);
		}
		return $thread;
	}

	/**
	 *	put an annotation in the right position of thread
	 *
	 * @param array $thread
	 * @param array $annotation (annotation object like Comment, EditorNote,...)
	 * @param array $pathArr if it's empty it will be build from thread_path field
	 * @return void
	 */
	public function putAnnotationInThread(&$thread, $annotation, $pathArr=null) {
		if (empty($pathArr)) {
			if (empty($annotation["thread_path"])) {
				$thread[] = $annotation;
				return;
			}
			$pathArr = explode("/", $annotation["thread_path"]);
		}
		$parent_id = end($pathArr);
		foreach ($thread as $k => $v) {
			if (empty($v["children"])) {
				$thread[$k]["children"] = array();
			}
			if ($v["id"] == $parent_id) {
				$thread[$k]["children"][] = $annotation;
				return;
			} elseif (in_array($v["id"], $pathArr)) {
				$this->putAnnotationInThread($thread[$k]["children"], $annotation, $pathArr);
				return;
			}
		}
	}

	/**
	 * passed an array of BEdita objects add 'num_of_annotation_name' key
	 * with the number of annotation applied to objects
	 *
	 * @param  array $objects
	 * @param  array $options list of options accepted
	 *             - type: array of Annotation object as array('Comment', 'EditorNote')
	 * @return array $objects
	 */
	public function countAnnotations(array $objects, array $options) {
        if (!empty($options['type'])) {
    		foreach ($objects as &$obj) {
                foreach ($options['type'] as $annotationType) {
                    $annotationModel = ClassRegistry::init($annotationType);
                    $objectTypeName = Inflector::underscore($annotationModel->name);
                    $numOf = 'num_of_' . $objectTypeName;
                    $objectTypeId = Configure::read('objectTypes.' . $objectTypeName . '.id');
        			$obj[$numOf] = $annotationModel->find('count', array(
        				'conditions' => array(
                            'object_id' => $obj['id'],
                            'BEObject.object_type_id' => $objectTypeId
                        ),
                        'contain' => array('BEObject')
        			));
                }
    		}
        }
		return $objects;
	}

}
?>