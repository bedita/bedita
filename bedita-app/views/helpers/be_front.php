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
	private $_feedNames;
	private $_conf;
	private $_breadcrumbs;
	private $_viewExt;

	public function __construct() {
		$view = ClassRegistry::getObject('view');
		$this->_publication = (!empty($view->viewVars['publication'])) ? $view->viewVars['publication'] : null;
		$this->_section =  (!empty($view->viewVars['section'])) ? $view->viewVars['section'] : null;
		$this->_currentContent = (!empty($view->viewVars['section']['currentContent'])) ? $view->viewVars['section']['currentContent'] : null;
		$this->_feedNames = (!empty($view->viewVars['feedNames']))? $view->viewVars['feedNames'] : null;
		$this->_viewExt = $view->ext;
		$this->_conf = Configure::getInstance();
	}

	public function title($order='asc') {
		$pub = (!empty($this->_publication['public_name'])) ? $this->_publication['public_name'] : $this->_publication['title'];
		if(empty($this->_section) || empty($this->_section['title']) || $this->_section['nickname'] == $this->_publication['nickname']) {
			if(!empty($this->_section['contentRequested']) && ($this->_section['contentRequested'] == 1) ) {
				$sec = $this->_currentContent['title'];
				if($order=='asc') {
					return $sec . " - " . $pub;
				}
				return $pub . " - " . $sec;
			}
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
		return $this->Html->meta("description", strip_tags($content));
	}

	public function metaDc() {
		$object = (!empty($this->_currentContent)) ? $this->_currentContent : $this->_publication;
		$title = (!empty($object['public_name'])) ? $object['public_name'] : $object['title'];
		$html = $this->Html->meta(array(
			"rel" => "schema.DC",
			'link' => "http://purl.org/dc/elements/1.1/"
		));
		$html .= "\n" . $this->Html->meta(array(
			"name" => "DC.title",
			"content" => $title
		));
		$content = $this->get_description();
		if(!empty($content)) {
			//$html.= "\n" . '<meta name="DC.description" 	content="' . strip_tags($content) . '" />';
			$html .= "\n" . $this->Html->meta(array(
				"name" => "DC.description",
				"content" => strip_tags($content)
			));
		}

		$html .= "\n" . $this->Html->meta(array(
			"name" => "DC.format",
			"content" => "text/html"
		));

		$mapDCtagsToFields = array(
			"DC.language" => "lang",
			"DC.creator" => "creator",
			"DC.publisher" => "publisher",
			"DC.date" => "date",
			"DC.modified" => "modified",
			"DC.identifier" => "id",
			"DC.rights" => "rights",
			"DC.license" => "license"
		);

		foreach ($mapDCtagsToFields as $dcTag => $field) {
			$content = $this->get_value_for_field($field);
			if (!empty($content)) {
				$html.= "\n" . $this->Html->meta(array(
					"name" => $dcTag,
					"content" => strip_tags($content)
				));
			}
		}
		
		return $html;
	}

	public function metaAll() {
		$html = "\n" . $this->metaDescription();
		$content = $this->get_value_for_field("license");
		if(!empty($content)) {
			$html.= "\n" . $this->Html->meta(array(
				"name" => "author",
				"content" => $this->_publication['creator']
			));
		}
		$html.= "\n" . $this->Html->meta(array(
			"http-equiv" => "Content-Style-Type",
			"content" => "text/css"
		));
		$html.= "\n" . $this->Html->meta(array(
			"name" => "generator",
			"content" => $this->_conf->userVersion
		));
		return $html;
	}

	public function feeds() {
		$html = "";
		if (!empty($this->_feedNames)) {
			foreach ($this->_feedNames as $feed) {
				$html .= "\n" . $this->Html->meta($feed["title"], "/rss/" . $feed["nickname"], array("type" => "rss"));
			}
		}
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
	 * choose a view template verifying if file exists. Test follow the order:
	 *		1) filename = frontendMap currentContent nickname
	 *		2) filename = frontendMap section nickname
	 * 		3) filename = currentContent nickname
	 *		4) filename = section nickname
	 *		5) filename = parent sections nickname
	 *		6) filename = object type
	 *		7) default
	 *
	 * @param string $default, default fallback template
	 * @return void
	 */
	public function chooseTemplate($default="generic_section") {

		$pagesPath = VIEWS . "pages" . DS;
		
		$tplFile = null;
		$cNick = null;
		$cId = null;
		if (!empty($this->_section["currentContent"])) {
			$cNick = $this->_section["currentContent"]["nickname"];
			$cId = $this->_section["currentContent"]["id"];
		}

		// 1. check frontendMap currentContent nickname
		if (isset($cNick)) {
			if(!empty($this->_conf->frontendMap[$cNick])) {
				$tplFile = $pagesPath . $this->_conf->frontendMap[$cNick] . $this->_viewExt;
				if (file_exists($tplFile)) {
					return $tplFile;
				}
			} else if(!empty($this->_conf->frontendMap[$cId])) {
				$tplFile = $pagesPath . $this->_conf->frontendMap[$cId] . $this->_viewExt;
				if (file_exists($tplFile)) {
					return $tplFile;
				}
			}
		}
		
		// 2. check frontendMap currentContent nickname
		$sNick = $this->_section["nickname"];
		if (!empty($this->_conf->frontendMap[$sNick])) {
			$tplFile = $pagesPath . $this->_conf->frontendMap[$sNick] . $this->_viewExt;
			if (file_exists($tplFile)) {
				return $tplFile;
			}
		}
		$sId = $this->_section["id"];
		if (!empty($this->_conf->frontendMap[$sId])) {
			$tplFile = $pagesPath . $this->_conf->frontendMap[$sId] . $this->_viewExt;
			if (file_exists($tplFile)) {
				return $tplFile;
			}
		}
		
		// 3. template with same name as currentContent nickname
		if (isset($cNick)) {
			$tplFile =  $pagesPath . $this->_section["currentContent"]["nickname"] . $this->_viewExt;
			if (file_exists($tplFile)) {
				return $tplFile;
			}
		}
		
		// 4. template with same name as section nickname
		$tplFile = $pagesPath . $sNick . $this->_viewExt;
		if (file_exists($tplFile)) {
			return $tplFile;
		}
		
		// 5. parent sections nickname
		if (!empty($this->_section["pathSection"])) {
			$parentFiles = array_reverse(Set::format($this->_section["pathSection"], $pagesPath . "{0}" . $this->_viewExt, array("{n}.nickname")));
			foreach ($parentFiles as $pFile) {
				if(file_exists($pFile)) {
					return $pFile;
				}
			}
		}

		// 6. object type template name
		if (!empty($this->_section["currentContent"])) {
			$tplFile = $pagesPath . $this->_conf->objectTypes[$this->_section["currentContent"]["object_type_id"]]["name"] . $this->_viewExt;
			if (file_exists($tplFile)) {
				return $tplFile;
			}
		}

		// 7. default
		return $pagesPath . $default . $this->_viewExt;
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

	/**
	 * return publication stats code only if frontend app isn't staging site
	 * @return stats code or nothing
	 */
	public function stats() {
		if (empty($this->_conf->staging) && !empty($this->_publication["stats_code"])) {
			return $this->_publication["stats_code"];
		}
	}

	/**
	 * return the breadcrumb trail
	 *
	 * @param  array options, possible values are:
	 *				"separator" => '&raquo;' [default] text to separate crumbs
	 *				"startText" => false [default] this will be the first crumb, if false it defaults to first crumb in array
	 *				"classOn"	=> "crumbOn" [default]  css class for current section
	 *				"showPublication" => true [default] choose if publication is shown in breadcrumb
	 *
	 * @return string
	 */
	public function breadcrumb(array $options = array() ) {

		$options = array_merge(
			array("separator" => '&raquo;', "startText" => false, "classOn" => "crumbOn", "showPublication" => true),
			(array)$options
		);

		$breadcrumb = $this->Html->getCrumbs($options["separator"], $options["startText"]);
		
		if (empty($breadcrumb)) {

			if ($options["showPublication"]) {
				$publication_name = (!empty($this->_publication["public_name"]))? $this->_publication["public_name"] : $this->_publication["title"];
				$this->Html->addCrumb($publication_name, '/');
			}

			if (!empty($this->_section["pathSection"])) {
				foreach ($this->_section["pathSection"] as $sec) {
					$this->Html->addCrumb($sec["title"], $sec["canonicalPath"]);
				}
			}
			if ($this->_section["id"] != $this->_publication["id"]) {
				$this->Html->addCrumb($this->_section["title"], $this->_section["canonicalPath"], array("class" => $options["classOn"]));
			}
			$breadcrumb = $this->Html->getCrumbs($options["separator"], $options["startText"]);
		}
		return $breadcrumb;
	}

}
 
?>
