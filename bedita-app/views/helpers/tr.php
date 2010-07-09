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
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
/**
 * i18n - translation helper
 * 
 */
class TrHelper extends AppHelper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html');

	/**
	 * map fields in db in form fields for any module
	 * @var array
	 */
	private $moduleFieldMap = array(
		"commonFields" => array(
			"title" => "title",
			"description" => "description",
			"nickname" => "unique name",
			"start_date" => "scheduled from",
			"end_date" => "to",
			"abstract" => "short text",
			"body" => "long text",
			"creator" => "author",
			"lang" => "main language",
			"duration" => "duration in minutes",
			"subject" => "subject"
		),
		"events" => array("creator" => "promoter"),
		"areas" => array("creator" => "creator"),
		"newsletter" => array(
			"sender" => "sender email",
			"reply_to" => "reply to",
			"bounce_to" => "bounce to",
			"privacy_disclaimer" => "privacy disclaimer",
			"abstract" => "PLAIN TEXT version",
			"body" => "HTML version"
		)
	);
			
	function t($s, $return = false) {
		return __($s, $return);
	}
	
	/**
	* Normal translation using i18n in cake php
	*/
	function translate($s, $return = false) {
		return __($s, $return);
	}

	/**
	* translate html->link url...
	*/
	function link($s, $u) {
		$tr = __($s, true);
		return $this->Html->link($tr, $u);
	}
	
	/**
	* Normal translation using i18n in cake php
	*/
	function translatePlural($s, $plural, $count, $return = false) {
		return __($s, $plural, $count, $return);
	}

	/**
	 * return the field used in the module corresponding to a database field
	 *
	 * @param string $moduleName
	 * @param string $dbFieldName
	 */
	function moduleField($moduleName, $dbFieldName) {
		$fieldName = $dbFieldName;
		if (array_key_exists($moduleName, $this->moduleFieldMap) && array_key_exists($dbFieldName, $this->moduleFieldMap[$moduleName])) {
			$fieldName = $this->moduleFieldMap[$moduleName][$dbFieldName];
		} elseif (array_key_exists($dbFieldName, $this->moduleFieldMap["commonFields"])) {
			$fieldName = $this->moduleFieldMap["commonFields"][$dbFieldName];
		}
		return $this->output($fieldName);
	}
}
?>