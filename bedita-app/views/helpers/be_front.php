<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
		$this->_currLang = (!empty($view->viewVars['currLang'])) ? $view->viewVars['currLang'] : null;
	}

	/**
	 * show 639-1 code, two letters, for html lang
	 * value should be in "frontendLangs" config array
	 *
	 * 	"eng"	=> array("en", "english"),
	 *	"spa"	=> array("es", "espa&ntilde;ol"),
	 * 	"ita"	=> array("it", "italiano"),
	 *  .....
	 *
	 * @return 639-1 code if found, empty string otherwise
	 */
	public function lang() {
		$res = "";
		if(!empty($this->_currLang)) {
			if(!empty($this->_conf->frontendLangs[$this->_currLang])
					&& is_array($this->_conf->frontendLangs[$this->_currLang])) {
				$res = $this->_conf->frontendLangs[$this->_currLang][0];
			}
		}
		return $res;
	}

	/**
	 * return title for current page
	 * if page is publication root => return <publication title> (if 'contentRequested' return <publication title> - <content title>)
	 * if page is a section => return <publication title> - <section title> [$order 'desc'] or <section title> - <publication title> [$order 'asc' default]
	 *  (if 'contentRequested' return <section title> - <content title> [$order 'desc'] or <content title> - <section title> [$order 'asc' default])
	 *
	 * @param string $order can be 'asc' or 'desc'
	 * @return string
	 */
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
			if(!empty($this->_currentContent['title'])) $sec = $this->_currentContent['title'];
			else $sec = $this->_section['title'];
		}
		if($order=='asc') {
			return h($sec . " - " . $pub);
		}
		return h($pub . " - " . $sec);
	}

	/**
	 * return html meta description.
	 * try to get description from:
	 *  _currentContent ('description' or 'abstract' or 'body')
	 *  _section['description']
	 *  _publication['description']
	 *
	 * @see HtmlHelper
	 * @return string
	 */
	public function metaDescription() {
		$content = $this->get_description();
		if(empty($content)) {
			return "";
		}
		return $this->Html->meta("description", h(strip_tags($content)));
	}

	/**
	 * return html meta of dublin core meta data for current content (if present) or publication
	 *
	 * DC fields:
	 *
	 *    DC.description
	 *    DC.format
	 *    DC.language
	 *    DC.creator
	 *    DC.publisher
	 *    DC.date
	 *    DC.modified
	 *    DC.identifier
	 *    DC.rights
	 *    DC.license
	 *
	 * @see HtmlHelper
	 * @return string
	 */
	public function metaDc() {
		$contentRequested = !empty($this->_section['contentRequested']) && ($this->_section['contentRequested'] == 1);
		$object = (!empty($this->_currentContent) && $contentRequested) ? $this->_currentContent : $this->_publication;
		$title = (!empty($object['public_name'])) ? $object['public_name'] : $object['title'];
		$html = $this->Html->meta(array(
			"rel" => "schema.DC",
			'link' => "http://purl.org/dc/elements/1.1/"
		));
		$html .= "\n" . $this->Html->meta(array(
			"name" => "DC.title",
			"content" => h($title)
		));
		$content = $this->get_description();
		if(!empty($content)) {
			//$html.= "\n" . '<meta name="DC.description" 	content="' . strip_tags($content) . '" />';
			$html .= "\n" . $this->Html->meta(array(
				"name" => "DC.description",
				"content" => h(strip_tags($content))
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
					"content" => h(strip_tags($content))
				));
			}
		}

		return $html;
	}

	/**
	 * return html of OG meta data (Object Graph Metadata - for facebook)
	 * 
	 * @return string
	 */
	public function metaOg() {
		$contentRequested = !empty($this->_section['contentRequested']) && ($this->_section['contentRequested'] == 1);
		$object = (!empty($this->_currentContent) && $contentRequested) ? $this->_currentContent : $this->_publication;
		$title = (!empty($object['public_name'])) ? $object['public_name'] : $object['title'];
		$html = '';
		// og:title
		$html .= "\n" . $this->Html->meta(array(
			'property' => 'og:title',
			'content' => h($title)
		));

		// og:type
		$conf = Configure::getInstance() ;
		$type = 'article';
		$objTypeId = $object['object_type_id'];
		if ($objTypeId == $conf->objectTypes['area']['id']) {
			$type = 'website';
		} else if (!empty($conf->objectTypes['book']['id']) && 
		    ($object['object_type_id'] == $conf->objectTypes['book']['id'])) {
			$type = 'book';
		} else if ($object['object_type_id'] == $conf->objectTypes['video']['id']) {
			$type = 'video';
		} else if ($object['object_type_id'] == $conf->objectTypes['audio']['id']) {
			$type = 'audio';
		}
		// TODO: handle more types: http://ogp.me/#types
		$html .= "\n" . $this->Html->meta(array(
			'property' => 'og:type',
			'content' => $type
		));

		// og:url
		$path = $this->_publication['public_url'];
		if(!empty($object['canonicalPath'])) {
			$path.=	$object['canonicalPath'];
		}
		$html .= "\n" . $this->Html->meta(array(
			'property' => 'og:url',
			'content' => $path
		));

		// TODO: alternative og:image if poster is empty and there is a multimedia image in relations 
		if (!empty($object['relations']['poster'])) {
			$imgUri = Configure::read('mediaUrl') . $object['relations']['poster'][0]['uri'];
	 		$html .= "\n" . $this->Html->meta(array(
				'property' => 'og:image',
				'content' => $imgUri
			));

			$html .= "\n" . '<link rel="image_src" type="image/jpeg" href="' . $imgUri . '" />';
		}

		// og:description
		$content = $this->get_description();
		if(!empty($content)) {
			$html .= "\n" . $this->Html->meta(array(
				'property' => 'og:description',
				'content' => h(strip_tags($content))
			));
		}

		// og:site_name
		$html .= "\n" . $this->Html->meta(array(
			'property' => 'og:site_name',
			'content' => h($this->_publication['public_name'])
		));

		
		$mapOGtagsToFields = array (
		/* TODO diventa fb:app_id, id di un'applicazione facebook da gestire in conf		
			'og:app_id' => 'id', */
			'og:updated_time' => 'modified'
		);

		foreach ($mapOGtagsToFields as $ogTag => $field) {
			$content = $this->get_value_for_field($field);
			if (!empty($content)) {
				$html.= "\n" . $this->Html->meta(array(
					'property' => $ogTag,
					'content' => h(strip_tags($content))
				));
			}
		}

		return $html;
	}

	/**
	 *	return html of common web-app metadata
	 *
	 *	@param string $title, the application name
	 *	@param array $icons, an array of icons to use when the application is pinned in user home screen
	 *	@param string $statusBar, hex string color for status and navigation bars
	 *	@param string $tileColor, hex string for application name color (Windows)
	 *	@param string $feed, the nickname of the section to use as feed
	 *
	 *	@return string
	 */
	public function metaWebApp($title = false, $icons = false, $statusBar = false, $tileColor = '#000', $feed = false) {
		$html = '';

		if (!empty($title)) {
			$html.= "\n" . $this->Html->meta(array(
				'name' => 'application-name',
				'content' => $title
			));
		}

		$html.= "\n" . $this->Html->meta(array(
			'name' => 'msapplication-config',
			'content' => 'none'
		));

		$html.= "\n" . $this->Html->meta(array(
			'name' => 'msapplication-starturl',
			'content' => $this->Html->url('/')
		));

		$html.= "\n" . $this->Html->meta(array(
			'name' => 'msapplication-TileColor',
			'content' => $tileColor
		));

		if (!empty($statusBar)) {
			$html.= "\n" . $this->Html->meta(array(
				'name' => 'msapplication-navbutton-color',
				'content' => $statusBar
			));
		}

		$html.= "\n" . $this->Html->meta(array(
			'name' => 'mobile-web-app-capable',
			'content' => 'yes'
		));

		$html.= "\n" . $this->Html->meta(array(
			'name' => 'apple-mobile-web-app-capable',
			'content' => 'yes'
		));

		if (!empty($statusBar)) {
			$html.= "\n" . $this->Html->meta(array(
				'name' => 'apple-mobile-web-app-status-bar-style',
				'content' => $statusBar
			));
		}

		if (!empty($icons)) {
			$default = null;
			if (!empty($icons['default'])) {
				$default = $icons['default'];
				$html.= "\n" . '<link rel="apple-touch-icon" href="' . $default . '" />';
				unset($icons['default']);
			}

			$appleIcons = array('76x76', '120x120', '152x152');
			$missing = array();

			foreach ($appleIcons as $value) {
				if (!empty($icons[$value])) {
					$ico = $icons[$value];
				} else if (!empty($default) && file_exists(WWW_ROOT . DS . $default)) {
					$pathInfo = pathinfo(WWW_ROOT . DS . $default);
					if (!empty($pathInfo)) {
						$use = str_replace('.' . $pathInfo['extension'], '-' . $value . '.' . $pathInfo['extension'], $default);
						if (file_exists(WWW_ROOT . DS . $use)) {
							$ico = $use;
						}
					}
				}

				if (!empty($ico)) {
					$html.= "\n" . '<link rel="apple-touch-icon" sizes="' . $value . '" href="' . $ico . '" />';
				} else {
					array_push($missing, $value);
				}
			}

			if (!empty($default)) {
				$html.= "\n" . $this->Html->meta(array(
					'name' => 'msapplication-TileImage',
					'content' => $default
				));
			}

			$windowsIcons = array('70x70', '150x150', '310x310', '310x150');
			foreach ($windowsIcons as $value) {
				$ico = false;
				if (empty($icons[$value])) {
					if (!empty($default) && file_exists(WWW_ROOT . DS . $default)) {
						$pathInfo = pathinfo(WWW_ROOT . DS . $default);
						if (!empty($pathInfo)) {
							$use = str_replace('.' . $pathInfo['extension'], '-' . $value . '.' . $pathInfo['extension'], $default);
							if (file_exists(WWW_ROOT . DS . $use)) {
								$ico = $use;
							}
						}
					}
				} else {
					$ico = $icons[$value];
				}

				if (!empty($ico)) {
					switch ($value) {
						case '70x70':
						case '150x150':
						case '310x310':
							$windowsMeta = 'msapplication-square' . $value . 'logo';
							break;
						case '310x150':
							$windowsMeta = 'msapplication-wide' . $value . 'logo';
						default:
							$windowsMeta = false;
							break;
					}

					if ($windowsMeta) {
						$html.= "\n" . $this->Html->meta(array(
							'name' => $windowsMeta,
							'content' => $ico
						));
					}
				} else {
					array_push($missing, $value);
				}
			}
		}

		if (!empty($feed)) {
			$feed = $this->Html->url('/rss/' . $feed);
			$html.= "\n" . $this->Html->meta(array(
				'name' => 'msapplication-notification',
				'content' => 'frequency=30;polling-uri=$feed&amp;id=1; cycle=1'
			));

			$html.= "\n" . $this->Html->meta(array(
				'name' => 'msapplication-badge',
				'content' => 'frequency=30;polling-uri=$feed&amp;id=1; cycle=1'
			));
		}

		return $html;
	}

	/**
	 * return all html meta
	 * all meta = description, author, content, generator
	 *
	 * @see HtmlHelper
	 * @return string
	 */
	public function metaAll() {
		$html = "\n" . $this->metaDescription();
		$content = $this->get_value_for_field("license");
		if(!empty($content)) {
			$html.= "\n" . $this->Html->meta(array(
				"name" => "author",
				"content" => h($this->_publication['creator'])
			));
		}
		$html.= "\n" . $this->Html->meta(array(
			"http-equiv" => "Content-Style-Type",
			"content" => "text/css"
		));
		$html.= "\n" . $this->Html->meta(array(
			"name" => "generator",
			"content" => 'BEdita ' . $this->_conf->version
		));
		return $html;
	}

	/**
	 * return html meta for rss feeds
	 *
	 * @see HtmlHelper
	 * @return string
	 */
	public function feeds() {
		$html = "";
		if (!empty($this->_feedNames)) {
			foreach ($this->_feedNames as $feed) {
				$html .= "\n" . $this->Html->meta($feed["title"], "/rss/" . $feed["nickname"], array("type" => "rss"));
			}
		}
		return $html;
	}

	/**
	 * return currentContent seealso relation, if present.
	 *
	 * @return string
	 */
	public function seealso()  {
		return (!empty($this->_currentContent['relations']['seealso'])) ? $this->_currentContent['relations']['seealso'] : '';
	}

	/**
	 * build <link rel="canonical"/> canonical path tag of content/section selected
	 *
	 * @return string
	 */
	public function canonicalPath() {
		$canonical_path = (empty($this->_section["contentRequested"]))? $this->_section["canonicalPath"] : $this->_section["currentContent"]["canonicalPath"];

		if(empty($canonical_path)) {
			return "";
		}

		if (Router::url($canonical_path) != $this->Html->here) {
			$public_url = $this->_publication[($this->_conf->staging) ? 'staging_url' : 'public_url'];
			return '<link rel="canonical" href="' . rtrim($public_url, "/") . $this->Html->base . $canonical_path .'" />';
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
		$contentRequested = !empty($this->_section['contentRequested']) && ($this->_section['contentRequested'] == 1);
		$field = "description";
		$current = ($contentRequested) ? $this->_currentContent : $this->_section;
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
		if (!empty($this->_section["currentContent"]) && !empty($this->_section['contentRequested'])) {
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

		// 2. check frontendMap section nickname
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
		if (isset($cNick)) {
			$tplFile = $pagesPath . $this->_conf->objectTypes[$this->_section["currentContent"]["object_type_id"]]["name"] . $this->_viewExt;
			if (file_exists($tplFile)) {
				return $tplFile;
			}
		}

		// 7. default
		return $pagesPath . $default . $this->_viewExt;
	}

	/**
	 * return an nested unordered list
	 *
	 *		<ul id="$options['id']" class="$options['menu']">
	 *			<li class="$options['liClass']"><a href="...">item 1</a></li>
	 *			<li class="$options['liClass']">
	 *				<a href="...">item 2</a>
	 *				<ul class="$options['ulClass']">
	 *					<li class="$options['liClass']"><a href="...">item 3</a></li>
	 *					...
	 *				</ul>
	 *			</li>
	 *			....
	 *		</ul>
	 *
	 * @param array $tree section's tree (structure from FrontendController::loadSectionsTree is aspected)
	 * @param array $options (defaukt values are visible in $defaultOptions array)
	 * 					"id" => id of main <ul>
	 * 					"menuClass" => css class of main <ul>
	 * 					"ulClass" => css class of nested <ul>
	 * 					"liClass" => css class of <li>
	 * 					"activeClass" => css class for <li> selected
	 * @return string
	 */
	public function menu(array $tree, array $options = array()) {
		$defaultOptions = array(
			"id" => "menu-item_" . time(),
			"menuClass" => "menu",
			"ulClass" => "children",
			"liClass" => "child-item",
			"activeClass" => "on"
		);
		$options = array_merge($defaultOptions, $options);
		$htmlMenu = "<ul id='" . $options["id"] . "' class='" . $options["menuClass"] . "'>";
		if (empty($tree)) {
			return $htmlMenu . "<li class='" .$options["liClass"] . "'></li></ul>";
		}

		foreach ($tree as $section) {
			$htmlMenu .= $this->menuBranch($section, $options);
		}

		return $htmlMenu;
	}

	/**
	 * return an html <li></li> menu branch
	 *
	 * @param array $section
	 * @param array $options
	 * 					"ulClass" => css class of nested <ul>
	 * 					"liClass" => css class of <li>
	 * @return string
	 */
	private function menuBranch(array $section, array $options) {
		$liClasses = $options["liClass"];
		if (!empty($this->_section['nickname']) &&
				($this->_section["nickname"] == $section["nickname"] || strstr($this->_section["canonicalPath"], '/' . $section["nickname"] . '/'))) {
			$liClasses .= " " . $options["activeClass"];
		}
		$htmlBranch = "<li class='" . $liClasses . "'>" .
			"<a href='" . $this->Html->url($section["canonicalPath"]) . "' title='" . h($section["title"]) . "'>" . h($section["title"]) . "</a>";

		if (!empty($section["sections"])) {
			$htmlBranch .= "<ul class='" . $options["ulClass"] . "'>";
			foreach ($section["sections"] as $subSection) {
				$htmlBranch .= $this->menuBranch($subSection, $options);
			}
			$htmlBranch .= "</ul>";
		}

		$htmlBranch .= "</li>";
		return $htmlBranch;
	}

	/**
	 * return publication stats code only if frontend app isn't staging site
	 *
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
					$show = !isset($sec["menu"]) ? true : (($sec["menu"] === '0') ? false : true);
					if($show && !empty($sec["title"]) && !empty($sec["canonicalPath"])) {
						$this->Html->addCrumb($sec["title"], $sec["canonicalPath"]);
					}
				}
			}
			if ($this->_section["id"] != $this->_publication["id"]) {
				$show = !isset($this->_section["menu"]) ? true : (($this->_section["menu"] === '0') ? false : true);
				if($show && !empty($this->_section["title"]) && !empty($this->_section["canonicalPath"])) {
					$this->Html->addCrumb($this->_section["title"], $this->_section["canonicalPath"], array("class" => $options["classOn"]));
				}
			}
			$breadcrumb = $this->Html->getCrumbs($options["separator"], $options["startText"]);
		}
		return $breadcrumb;
	}

	/**
	 * if frontend is a staging app then it shows a toolbar on the top of the page
	 *
	 * @return void
	 */
	public function stagingToolbar() {
		if ($this->_conf->staging) {
			echo ClassRegistry::getObject('view')->element("staging_toolbar");
		}
	}

	/**
	 * helper beforeRender.
	 * include js that staging toolbar needs, include css (backend and eventually frontend override), override css
	 *
	 * @return void
	 */
	public function beforeRender() {
		/* if staging load js e css for staging toolbar.
		 * Their are loaded here because in layout view doesn't work inline=false option.
		 * In fact for the design of cakePHP layouts are simply parsed by PHP interpeter
		 */
		if ($this->_conf->staging) {

			// include js that staging toolbar needs
			echo $this->Html->script(
				array(
					$this->_conf->beditaUrl . "/js/libs/jquery/plugins/jquery.cookie.js",
					$this->_conf->beditaUrl . "/js/staging_toolbar.js"
				),
				array("inline" => false)
			);

			// include css (backend and eventually frontend override)
			$css = $this->_conf->beditaUrl . "/css/staging_toolbar.css";
			echo $this->Html->css($css, null, array("inline" => false));

			// override css
			if (file_exists(APP . "webroot" . DS . "css" . DS . "staging_toolbar.css")) {
				echo $this->Html->css("staging_toolbar", null, array("inline" => false));
			}
		}

	}
}
?>