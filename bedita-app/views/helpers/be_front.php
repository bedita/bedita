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

	var $helpers = array('Html');

	private $_publication;
	private $_section;
	private $_currentContent;
	private $_conf;

	public function __construct() {
		$view = ClassRegistry::getObject('view');
		$this->_publication = (!empty($view->viewVars['publication'])) ? $view->viewVars['publication'] : null;
		$this->_section =  (!empty($view->viewVars['section'])) ? $view->viewVars['section'] : null;
		$this->_currentContent = (!empty($view->viewVars['section']['currentContent'])) ? $view->viewVars['section']['currentContent'] : null;
		$this->_conf = Configure::getInstance();
	}

	public function title($order='asc') {
		$pub = (!empty($this->_publication['public_name'])) ? $this->_publication['public_name'] : $this->_publication['title'];
		if(empty($this->_section) || empty($this->_section['title']) || $this->_section['nickname'] == $this->_publication['nickname']) {
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
		$content = $this->get_description();
		if(empty($content)) {
			return "";
		}
		return '<meta name="description" content="' . strip_tags($content) . '" />';
	}

	public function metaDc() {
		$object = (!empty($this->_currentContent)) ? $this->_currentContent : $this->_publication;
		$title = (!empty($object['public_name'])) ? $object['public_name'] : $object['title'];
		$html = '<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />';
		$html.= "\n" . '<meta name="DC.title" 			content="' . $title . '" />';
		$content = $this->get_description();
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.description" 	content="' . strip_tags($content) . '" />';
		$content = $this->get_value_for_field("lang");
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.language" 		content="' . strip_tags($content) . '" />';
		$content = $this->get_value_for_field("creator");
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.creator" 		content="' . strip_tags($content) . '" />';
		$content = $this->get_value_for_field("publisher");
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.publisher" 		content="' . strip_tags($content) . '" />';
		$content = $this->get_value_for_field("date");
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.date" 			content="' . strip_tags($content) . '" />';
		$content = $this->get_value_for_field("modified");
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.modified" 		content="' . strip_tags($content) . '" />';
		$html.= "\n" . '<meta name="DC.format" 			content="text/html" />';
		$content = $this->get_value_for_field("id");
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.identifier" 		content="' . strip_tags($content) . '" />';
		$content = $this->get_value_for_field("rights");
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.rights" 			content="' . strip_tags($content) . '" />';
		$content = $this->get_value_for_field("license");
		if(!empty($content)) 
			$html.= "\n" . '<meta name="DC.license" 		content="' . strip_tags($content) . '" />';
		return $html;
	}

	public function metaAll() {
		$html = $this->metaDescription();
		$content = $this->get_value_for_field("license");
		if(!empty($content))
			$html.= "\n" . '<meta name="author" content="' . $this->_publication['creator'] . '" />';
		$html.= "\n" . '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		$html.= "\n" . '<meta http-equiv="Content-Style-Type" content="text/css" />';
		$html.= "\n" . '<meta name="generator" content="' . $this->_conf->userVersion . '" />';
		return $html;
	}

	public function seealso()  {
		return (!empty($this->_currentContent['relations']['seealso'])) ? $this->_currentContent['relations']['seealso'] : '';
	}

	public function canonicalPath() {
		$canonical_path = $this->_section["canonicalPath"];
		if(empty($canonical_path)) {
			return "";
		}
		$current_path = $this->Html->here;
		if(!strstr($canonical_path,$current_path)) {
			$public_url = $this->_publication[($this->_conf->staging) ? 'staging_url' : 'public_url'];
			if(substr($public_url,-1) == "/") {
				$public_url = substr($public_url,0,strlen($public_url)-1);
			}
			return '<link rel="canonical" href="' . $public_url . $this->Html->base . $canonical_path .'" />';
		}
		return "";
	}

	private function get_value_for_field($field) {
		$current = $this->_currentContent;
		$section = $this->_section;
		$publish = $this->_publication;
		if(!empty($current[$field])) {
			$content = $current[$field];
		} else if(!empty($section[$field])) {
			$content = $section[$field];
		} else if(!empty($publish[$field])) {
			$content = $publish[$field];
		} else {
			return "";
		}
		return $content;
	}

	private function get_description() {
		$field = "description";
		$current = $this->_currentContent;
		$section = $this->_section;
		$publish = $this->_publication;
		if(!empty($current["description"])) {
			$content = $current["description"];
		} else if(!empty($current["abstract"])) {
			$content = substr($current["abstract"],0,255);
		} else if(!empty($current["body"])) {
			$content = substr($current["body"],0,255);
		} else if(!empty($section[$field])) {
			$content = $section[$field];
		} else if(!empty($publish[$field])) {
			$content = $publish[$field];
		} else {
			return "";
		}
		return $content;
	}

	/**
	 * return an innested unordered list
	 *		<ul id="menuItem">
	 *			<li class="$liClass"><a href="...">item 1</a></li>
	 *			<li class="$liClass">
	 *				<a href="...">item 2</a>
	 *				<ul class="$ulClass">
	 *					<li class="$liClass"><a href="...">item 3</a></li>
	 *					...
	 *				</ul>
	 *			</li>
	 *			....
	 *		</ul>
	 *
	 * @param array $tree section's tree (structure from FrontendController::loadSectionsTree is aspected)
	 * @param string $ulClass css class name for <ul> except for the first that has id="menuItem"
	 * @param string $liClass css class name for all <li>
	 */
	public function menu(array $tree, $ulClass="children", $liClass="childItem") {
		$htmlMenu = "<ul id='menuItem'>";
		if (empty($tree)) {
			return $htmlMenu . "<li class='" .$liClass . "'></li></ul>";
		}

		foreach ($tree as $section) {
			$htmlMenu .= $this->menuBranch($section, $ulClass, $liClass);
		}

		return $htmlMenu;
	}

	/**
	 * return an html <li></li> menu branch
	 *
	 * @param array $section
	 * @param string $ulClass
	 * @param string $liClass
	 * @return string
	 */
	private function menuBranch(array $section, $ulClass, $liClass) {
		$liClasses = $liClass;
		if (!empty($this->_section['nickname']) && 
				($this->_section["nickname"] == $section["nickname"] || strstr($this->_section["canonicalPath"], '/' . $section["nickname"] . '/'))) {
			$liClasses .= " " . "on";
		}
		$htmlBranch = "<li class='" . $liClasses . "'>" .
			"<a href='" . $this->Html->url($section["canonicalPath"]) . "' title='" . $section["title"] . "'>" . $section["title"] . "</a>";

		if (!empty($section["sections"])) {
			$htmlBranch .= "<ul class='" . $ulClass . "'>";
			foreach ($section["sections"] as $subSection) {
				$htmlBranch .= $this->menuBranch($subSection, $ulClass, $liClass);
			}
			$htmlBranch .= "</ul>";
		}

		$htmlBranch .= "</li>";
		return $htmlBranch;
	}

}
 
?>
