<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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

App::uses("BEAppModel", "Model");

/**
 * Version object
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class Version extends BEAppModel
{
	/**
	 * fields not versioned/revisioned
	 *
	 * @var array
	 */
	private $noRevision = array("user_modified");

	public $belongsTo = array("User");

	/**
	 * Create a new revision, creating a 'diff' between two object data arrays
	 * and saving a new record in 'versions'
	 *
	 * @param array $oldData
	 * @param array $newData
	 */
	public function addRevision(array& $oldData, array& $newData) {
		$vData = array("object_id" => $oldData["id"],
			"created" => $oldData["modified"],
			"user_id" => $oldData["user_modified"]);
		$lastRev = $this->field("revision", array("object_id" => $vData["object_id"]),
			"revision desc");
		if(empty($lastRev)) {
			$vData["revision"] = 1;
		} else {
			$vData["revision"] = $lastRev + 1;
		}
		$diff = $this->calcDiff($oldData, $newData);
		if(!empty($diff)) {
			$vData["diff"] = serialize($diff);
			$this->create();
			$this->save($vData);
		}
	}

	private function calcDiff(array& $old, array &$new) {
		$newDiff = array_diff_assoc($new, $old);
		// remove relations, keep only old object fields/attribs
		$diff = array();
		foreach ($newDiff as $k => $v) {
			if(!is_array($v) && !in_array($k, $this->noRevision)) {
				$diff[$k] = !empty($old[$k]) ? $old[$k] : null;
			}
		}
		return $diff;
	}

	/**
	 * Create a diff array containing only changed fields between last version
	 * and requested revision
	 *
	 * @param int $id, object id
	 * @param int $revNum, revision number requested
	 * @return array with revision information,
	 *
	 */
	public function diffData($id, $revNum) {
		// check $revNum
		$r = $this->field("revision", array("object_id" => $id,
			"revision" => $revNum));
		if(empty($r)) {
			throw new BeditaException(__("Requeste revision not found"));
		}
		$diffs = $this->find("all", array("conditions" =>
			array("Version.object_id" => $id, "Version.revision >= $revNum"),
			"fields" => array("diff"), "order" => "Version.revision desc"));
		$res = array();
		foreach ($diffs as $d) {
			$rd = unserialize($d["Version"]["diff"]);
			$res = array_merge($res, $rd);
		}
		return $res;
	}

	/**
	 * Return revision data for a specified model, by id and revision number
	 *
	 * @param int $id
	 * @param int $revNum
	 * @param BEAppModel $model
	 * @return array
	 */
	public function revisionData($id, $revNum, BEAppModel $model) {
		$model->containLevel('minimum');
		$currData = $model->findById($id);
		foreach ($currData as $k => $v) {
			if(is_array($v)) {
				unset($currData[$k]);
			}
		}
		$diff = $this->diffData($id, $revNum);
		return array_merge($currData, $diff);
	}

	/**
	 * Return number of revisions for specified object id
	 *
	 * @param int $id
	 * @return int
	 */
	public function numRevisions($id) {
		$count = $this->find("count", array(
				"conditions" => array("Version.object_id" => $id)
						)
				);
		return $count;
	}
}
?>