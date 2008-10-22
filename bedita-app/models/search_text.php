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
class SearchText extends BEAppModel
{
	var $belongsTo = array(
		'BEObject' =>
			array(
				'fields'		=> 'id',
				'foreignKey'	=> 'object_id'
			)
	);

	public function createSearchText($model) {
		
		$bviorCompactResults = null;
		if(isset($model->bviorCompactResults)) {
			$bviorCompactResults = $model->bviorCompactResults ;
		}
		$model->bviorCompactResults = true ;
		$model->containLevel("default");
		if(!($data = $model->findById($model->{$model->primaryKey}))) 
		  throw new BeditaException("Error loading {$model->name}");
		$model->bviorCompactResults = $bviorCompactResults ;
		
		$searchFields = array();
		$conf = Configure::getInstance();
		if(isset( $conf->searchFields[$model->name])) {
			$searchFields = $conf->searchFields[$model->name];
		} elseif($model->searchFields != null) {
			$searchFields = $model->searchFields;
		}

		if(!empty($searchFields)) {
			$indexFields = array_keys($searchFields);
			foreach ($data as $k => $v) {
				if(in_array($k, $indexFields)) {
	                if (!empty($v)) {
						$sText = array(
			                'object_id' => $data['id'],
			                'lang'      => $data['lang'], 
			                'content'   => $v,
			                'relevance' => $searchFields[$k]
		                );
	
		                $this->create();
		                if(!$this->save($sText)) 
		                    throw new BeditaException("Error saving search text {$model}: $k => $v");
	                }
				}
			}
		}
		return true ;
	}

}
?>
