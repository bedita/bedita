<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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

}
?>
