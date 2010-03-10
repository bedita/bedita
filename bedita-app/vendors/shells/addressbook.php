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

require_once 'bedita_base.php';
App::import("File", "BeLib", true, array(BEDITA_LIBS), "be_lib.php");
BeLib::getObject("BeConfigure")->initConfig();

/**
 * Shell script to import/export/manipulate cards.
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class AddressbookShell extends BeditaBaseShell {

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
		$options = array();
		if (!isset($this->params['c'])) {
			$this->out("No categories set");
		} else {
			$categories = trim($this->params['c']);
			$catTmp = split(",", $categories);
			$categoryModel = ClassRegistry::init("Category");
			$cardTypeId = Configure::read("objectTypes.card.id");
			$options = array("Category" => $categoryModel->findCreateCategories($catTmp, $cardTypeId));
		}

		$ext = strtolower(substr($cardFile, strrpos($cardFile, ".")+1));
		$isCsv = ($ext == "csv");
		$this->out("Importing file $cardFile using " . (($isCsv) ? "CSV" : "VCard") . " format");
		
		$cardModel = ClassRegistry::init("Card");
		if($isCsv) {
			$result = $cardModel->importCSVFile($cardFile, $options);
		} else {
			$result = $cardModel->importVCardFile($cardFile, $options);
		}
		$this->out("Done\nResult: " . print_r($result, true));		
	}

	function help() {
        $this->out('Available functions:');
  		$this->out(' ');
        $this->out('1. import: import vcf/vcard or microsoft outlook csv file');
  		$this->out(' ');
        $this->out('    Usage: import -f <csv-cardfile> [-c <categories>]');
  		$this->out(' ');
  		$this->out("    -f <csv-cardfile>\t vcf/vcard or csv file to import");
  		$this->out("    -c <categories> \t comma separated <categories> to use on import (created if not exist)");
  		$this->out(' ');
	}

}
?>
