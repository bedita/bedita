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

App::import('vendor', "VCard", true, array(), "vcard.php");
require_once 'bedita_base.php';

/**
 * Shell script to import/export/manipulate cards.
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class AddressbookShell extends Shell {

	public function import() {
		if (!isset($this->params['f'])) {
			$this->out("Input file is mandatory");
			return;
		}

		$cardFile = $this->params['f'];
		if(!file_exists($cardFile)) {
			$this->out("$cardFile not found, bye");
			return;
		}

		// categories
		$categoryArray = array();
		if (!isset($this->params['c'])) {
			$this->out("No categories set");
		} else {
			$categories = trim($this->params['c']);
			$catTmp = split(",", $categories);
			
			$categoryModel = ClassRegistry::init("Category");
			$cardTypeId = Configure::read("objectTypes.card.id");
			// if not exists create
			foreach ($catTmp as $cat) {
				$categoryModel->create();
				$cat = trim($cat);
				$categoryModel->bviorCompactResults = false;
				$idCat = $categoryModel->field('id', array('label'=>$cat, 'object_type_id' => $cardTypeId));
				$categoryModel->bviorCompactResults = true;
				if(empty($idCat)) {
					$dataCat = array('name'=>$cat,'label'=>$cat,
						'object_type_id' => $cardTypeId, 'status'=>'on');
					if(!$categoryModel->save($dataCat)) {
						throw new BeditaException("Error saving category: " . print_r($dataCat, true));
					}
					$idCat = $categoryModel->id;
				}
				$categoryArray[] = $idCat;
			}
		}
		
		$this->out("Importing file $cardFile");
		$lines = file($cardFile);
		if (!$lines) {
			$this->out("Can't read the vCard file: $cardFile");
			return;
		}
		$cards = $this->parseVCards($lines);
		
		$defaults = array( 
			"status" => "on",
			"user_created" => "1",
			"user_modified" => "1",
			"lang" => "ita",
			"ip_created" => "127.0.0.1",
			"Category" => $categoryArray,
		);

		$cardModel = ClassRegistry::init("Card");
		foreach ($cards as $c) {
		
			$cardModel->create();
			$data = array_merge($defaults, $c);
			if(!$cardModel->save($data)) {
				throw new BeditaException("Error saving card: " . print_r($c, true) . " \nvaldation: " . print_r($cardModel->validationErrors, true));
			}
		}

	}

	private function parseVCards(&$lines) {
		$cards = array();
		$done = false;
		while (!$done) {
			$card = new VCard();
			if(!$card->parse($lines)) {
				$done = true;
			} else {
			
				$property = $card->getProperty('N');
				if (!$property) {
					return "";
				}
				$n = $property->getComponents();
				
				$fnProp = $card->getProperty('FN');
				
				$nameProp = $card->getProperty('NAME');
				$emailProp = $card->getProperties('EMAIL');
				$telProp = $card->getProperties('TEL');
				$orgProp = $card->getProperty('ORG');
				
				$item = array();
				$item["title"] = !empty($nameProp->value) ? trim($nameProp->value) : (!empty($fnProp->value) ? trim($fnProp->value) : null );
				
				$item["name"] = !empty($n[1]) ? trim($n[1]) : null;
				$item["surname"] = !empty($n[0]) ? trim($n[0]) : null;

				$item["email"] = !empty($emailProp[0]->value) ? trim($emailProp[0]->value) : null;
				$item["phone"] = !empty($telProp[0]->value) ? trim($telProp[0]->value) : null;
				$item["email2"] = !empty($emailProp[1]->value) ? trim($emailProp[1]->value) : null;
				$item["phone2"] = !empty($telProp[1]->value) ? trim($telProp[1]->value) : null;
							
				if(empty($item["title"])) {
					$item["title"] = $item["name"] . " " . $item["surname"];
				}
				$item["company_name"] = !empty($orgProp->value) ? trim($orgProp->value) : null;
				$cards[] = $item;
			}
		}
		return $cards;
	}

	function help() {
        $this->out('Available functions:');
  		$this->out(' ');
        $this->out('1. import: import vcf/vcard file with default categories');
  		$this->out(' ');
        $this->out('    Usage: import -f <cardfile> [-c <categories>]');
  		$this->out(' ');
  		$this->out("    -f <cardfile>\t vcf/vcard file to import");
  		$this->out("    -c <categories> \t comma separated <categories> to use on import (created if not exist)");
  		$this->out(' ');
	}

}
?>
