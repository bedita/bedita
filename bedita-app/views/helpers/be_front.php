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
 * helper class for frontends
 * 
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeFrontHelper extends AppHelper {

	private $_publication;
	private $_section;
	private $_currentContent;
	private $_conf;
	private $_datePattern;

	public function __construct() {
		$view = ClassRegistry::getObject('view');
		$this->_publication = (!empty($view->viewVars['publication'])) ? $view->viewVars['publication'] : null;
		$this->_section =  (!empty($view->viewVars['section'])) ? $view->viewVars['section'] : null;
		$this->_currentContent = (!empty($view->viewVars['section']['currentContent'])) ? $view->viewVars['section']['currentContent'] : null;
		$this->_conf = Configure::getInstance();
		$this->_datePattern = $this->_conf->datePattern;
	}

	public function title($order='asc') {
		$pub = (!empty($this->_publication['public_name'])) ? $this->_publication['public_name'] : $this->_publication['title'];
		if(empty($this->_section) || empty($this->_section['title'])) {
			return $pub;
		}
		$sec = $this->_section['title'];
		if(!empty($this->_section['contentRequested']) && ($this->_section['contentRequested'] == 1) ) {
			$sec = $this->_currentContent['title'];
		}
		if($order=='asc') {
			return $sec . " - " . $pub;
		}
		return $pub . " - " . $sec;
	}

	public function metaDescription() {
		$content = (!empty($this->_currentContent['description'])) ? $this->_currentContent['description'] : $this->_publication['description'];
		$html = '<meta name="description" content="' . strip_tags($content) . '" />';
		return $html;
	}

	public function metaDc() {
		$object = (!empty($this->_currentContent)) ? $this->_currentContent : $this->_publication;
		$title = (!empty($object['public_name'])) ? $object['public_name'] : $object['title'];
		$html = '<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />';
		$html.= "\n" . '<meta name="DC.title" 			content="' . $title . '" />';
		if(!empty($object['description'])) 
			$html.= "\n" . '<meta name="DC.description" 	content="' . $object['description'] . '" />';
		if(!empty($object['lang'])) 
			$html.= "\n" . '<meta name="DC.language" 		content="' . $object['lang'] . '" />';
		if(!empty($object['creator'])) 
			$html.= "\n" . '<meta name="DC.creator" 		content="' . $object['creator'] . '" />';
		if(!empty($object['publisher'])) 
			$html.= "\n" . '<meta name="DC.publisher" 		content="' . $object['publisher'] . '" />';
		if(!empty($object['date'])) 
			$html.= "\n" . '<meta name="DC.date" 			content="' . $object['date'] . '" />';
		if(!empty($object['modified'])) 
			$html.= "\n" . '<meta name="DC.modified" 		content="' . $object['modified'] . '" />';
		$html.= "\n" . '<meta name="DC.format" 			content="text/html" />';
		if(!empty($object['id'])) 
			$html.= "\n" . '<meta name="DC.identifier" 		content="' . $object['id'] . '" />';
		if(!empty($object['rights'])) 
			$html.= "\n" . '<meta name="DC.rights" 			content="' . $object['rights'] . '" />';
		if(!empty($object['license'])) 
			$html.= "\n" . '<meta name="DC.license" 		content="' . $object['license'] . '" />';
		return $html;
	}

	public function metaAll() {
		$html = $this->metaDescription();
		$html.= "\n" . '<meta name="author" content="' . $this->_publication['creator'] . '" />';
		$html.= "\n" . '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		$html.= "\n" . '<meta http-equiv="Content-Style-Type" content="text/css" />';
		return $html;
	}

	public function seealso()  {
		return (!empty($this->_currentContent['relations']['seealso'])) ? $this->_currentContent['relations']['seealso'] : '';
	}

	public function date($date,$format = null) {
		return $this->format($date,$format);
	}

	public function dateTime($date,$format = null) {
		return $this->format($date,$format);
	}

	public function day($date) {
		return $this->format($date,"%d");
	}

	public function dayName($date) {
		return $this->format($date,"%A");
	}

	public function month($date) {
		return $this->format($date,"%m");
	}

	public function monthName($date) {
		return $this->format($date,"%B");
	}

	public function year($date) {
		return $this->format($date,"%Y");
	}

	private function format($string, $format = '%b %e, %Y', $default_date = '') {
		$format = ($format!=null) ? $format : $this->_datePattern;
		if ($string != '') {
			$timestamp = $this->make_timestamp($string);
		} elseif ($default_date != '') {
			$timestamp = $this->make_timestamp($default_date);
		} else {
			return;
		}
		if (DIRECTORY_SEPARATOR == '\\') {
			$_win_from = array('%D',	   '%h', '%n', '%r',		  '%R',	'%t', '%T');
			$_win_to   = array('%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S');
			if (strpos($format, '%e') !== false) {
				$_win_from[] = '%e';
				$_win_to[]   = sprintf('%\' 2d', date('j', $timestamp));
			}
			if (strpos($format, '%l') !== false) {
				$_win_from[] = '%l';
				$_win_to[]   = sprintf('%\' 2d', date('h', $timestamp));
			}
			$format = str_replace($_win_from, $_win_to, $format);
		}
		return strftime($format, $timestamp);
	}
	
	private function make_timestamp($string) {
		if(empty($string)) {
			// use "noew":
			$time = time();
		} elseif (preg_match('/^\d{14}$/', $string)) {
			// it is mysql timestamp format of YYYYMMDDHHMMSS?			
			$time = mktime(substr($string, 8, 2),substr($string, 10, 2),substr($string, 12, 2),
						   substr($string, 4, 2),substr($string, 6, 2),substr($string, 0, 4));
		} elseif (is_numeric($string)) {
			// it is a numeric string, we handle it as timestamp
			$time = (int)$string;
		} else {
			// strtotime should handle it
			$time = strtotime($string);
			if ($time == -1 || $time === false) {
				// strtotime() was not able to parse $string, use "now":
				$time = time();
			}
		}
		return $time;
	}
}
 
?>