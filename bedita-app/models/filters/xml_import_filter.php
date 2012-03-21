<?php

/* -----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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
 * ------------------------------------------------------------------->8-----
 */

/**
 * XmlImportFilter: class to import objects from XML
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class XmlImportFilter extends BeditaImportFilter 
{

	protected $typeName = "BE Xml";
	protected $mimeTypes = array("text/xml", "application/xml");
	
	/**
	 * Import BE objects from XML source string
	 * @param string $source, XML source
	 * @param array $options, import options: "sectionId" => import objects in this section 
	 * @return array , result array containing 
	 * 	"objects" => number of imported objects
	 *  "message" => generic message (optional)
	 *  "error" => error message (optional)
	 * @throws BeditaException
	 */
	public function import($source, array $options = array()) {

		App::import("Core", "Xml");
		$xml = new XML(file_get_contents($source));
		$treeModel = ClassRegistry::init("Tree");
		$nObj = 0;	
		$parsed = set::reverse($xml);				
		$objs = array();
		if(!empty($parsed["Section"]["ChildContents"])) {
			$objs = $parsed["Section"]["ChildContents"];
		} else if(!empty($parsed["Bedita"]["Objects"])) {
			$objs = $parsed["Bedita"]["Objects"];
		} else {
			$this->out("No contents found.");
			return;
		}
		if(!is_int(key($objs))) {
			$objs = array($objs);
		} 
		foreach ($objs as $data) {

			$objTypeId = isset($data['ObjectType']['name']) ?  
				Configure::read("objectTypes." . $data['ObjectType']['name'] . ".id") : $data['object_type_id']; 
			$modelType = Configure::read("objectTypes." . $objTypeId . ".model");
			$model = ClassRegistry::init($modelType);
			
			// remove fields to recreate
			$remove = array("id", "user_created", "user_modified", "created", "modified", "ip_created");
			foreach ($remove as $r) {
				unset($data[$r]);
			}

			$data["object_type_id"] = $objTypeId;
			$model->create();
			if(!$model->save($data)) {
				throw new BeditaException("Error saving object - " . print_r($data, true) . 
					" - validation: " . print_r($model->validationErrors, true));
			}
			if(!empty($options["sectionId"])) {
				$treeModel->appendChild($model->id, $options["sectionId"]);
			}
			$nObj++;		
		}
		return array("objects" => $nObj);
	}
};
