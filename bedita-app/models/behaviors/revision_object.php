<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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
 * Behavior to create/handle revisions for an object 
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class RevisionObjectBehavior extends ModelBehavior {

	var $config = array();
	private $prevData = array();
	
	function setup($model, $config) {		
	}
	
	/**
	 * load previous data array
	 */
	function beforeSave($model) {
		
		if(!empty($model->id)) {
			$model->containLevel('minimum');
			$this->prevData[$model->id] = $model->findById($model->id);
		}
		return true ;
	}
	
	/**
	 * save diff in a new array
	 */
	public function afterSave($model, $created) {

		if(!$created) {
			$fieldsToRevison = $model->getColumnTypes();
			// revision only object base tables
			if (!empty($model->actsAs["ForeignDependenceSave"])) {
				foreach ($model->actsAs["ForeignDependenceSave"] as $dependeceModelName) {
					$fieldsToRevison = array_merge($fieldsToRevison, $model->{$dependeceModelName}->getColumnTypes());
				}
			}
			$dataToRevision = array_intersect_key($model->data[$model->name], $fieldsToRevison);
			$version = ClassRegistry::init("Version");
			$version->addRevision($this->prevData[$model->id], $dataToRevision);
		}
	}
	
}
?>