<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
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
 * BEdita tree of contents helper
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 */
class BeTreeHelper extends AppHelper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array("Html", "SessionFilter");

	var $tags = array(
		'option'	=> "<option value=\"%s\"%s>%s</option>",
		'checkbox'	=> "<input type=\"checkbox\" name=\"data[destination][]\" value=\"%s\" %s/>",
		'radio'	=> "<input type=\"radio\" name=\"data[destination]\" value=\"%s\" %s/>",
	);

	protected $treeParams = array();

	/**
	 * beforeRender callback
	 * initialize BeTree::treeParams to $this->params
	 */
	public function beforeRender() {
		$this->resetTreeParams();
	}

	/**
	 * Merge BeTree::treeParams with array of params
	 * you can use it to override some params used to build url in the rel attribute inside tree items.
	 * For example:
	 * 		url is build using $treeParams['controller'] and treeParams['action'].
	 *   	If you want tree items go to another controller you have to call BeTree::setTreeParams() before building the tree.
	 *    	From view file call {$beTree->setTreeParams(['controller' => 'myController'])}
	 * @param array $params
	 */
	public function setTreeParams(array $params) {
		$this->treeParams = array_merge($this->treeParams, $params);
	}

	/**
	 * set BeTree::treeParams to $this->params
	 */
	public function resetTreeParams() {
		$this->treeParams = $this->params;
	}

	/**
	 * build option for select
	 *
	 * @param array $tree
	 * @param int $numInd number of $indentation repetition foreach branch
	 * @param string $indentation string to use for indentation
	 *
	 * @return string 	<option value="">...</option>
	 * 		   			<option value="">...</option>
	 * 					....
	 */
	public function optionsMobile($tree, $options = array() ) {
		$default_options = array(
			'parentIds' => array(),
			'selId' => null,
			'numInd' => 3,
			'level' => 0,
			'indentation' => "&nbsp;"
		);
		$options = array_merge($default_options, $options);
		extract($options);

		$output = "<option value=\"\"> -- </option>";

		if (!empty($tree)) {
			foreach ($tree as $publication) {
				$selected = (in_array($publication['id'],$parentIds)) ? " selected" : "";
				$output .= sprintf($this->tags['option'], $publication["id"], $selected, h(mb_strtoupper($publication["title"]))) ;
				if (!empty($publication["children"])) {
					$options2 = array_merge( // Aumenta livello
						$options,
						array('level' => $level + 1)
					);
					$output .= $this->optionMobileBranch($publication["children"], $options2);
				}
			}
		}

		return $this->output($output);

	}
	/**
	 * build branch
	 *
	 * @param $branch
	 * @param int $numInd number of repetition on $indentation string foreach branch
	 * @param string $indentation string to use for indentation
	 *
	 * @return string of option
	 */
	private function optionMobileBranch($branch, $options = array() ) {
		$default_options = array(
			'parentIds' => array(),
			'selId' => null,
			'numInd' => 3,
			'level' => 0,
			'indentation' => "&nbsp;"
		);
		$options = array_merge($default_options, $options);
		extract($options);

		if (!isset($this->numInd)) {
			$this->numInd = $numInd;
		}

		if (empty($space)) {
			$space = "";
		}

		if (empty($res)) {
			$res = "";
		}

		for ($i = 1; $i <= $numInd * $level; $i++) {
			$space .= $indentation;
		}

		foreach ($branch as $section) {
			$selected = (in_array($section['id'],$parentIds)) ? " selected" : "";
			$res .= sprintf($this->tags['option'], $section["id"], $selected, $space . h($section["title"])) ;
			if (!empty($section["children"])) {
				$options2 = array_merge( // Aumenta livello
					$options,
					array('level' => $level + 1)
				);
				$res .= $this->optionMobileBranch($section["children"], $options2);
			}

		}

		return $res;
	}

	/**
	 * get html section
	 *
	 * @param array $branch, section
	 * @param string $inputType, type of input to prepend to section name (checkbox, radio)
	 * @param array $parent_ids, array of ids parent
	 * @return string html for section simple tree
	 */
	public function designBranchMobile($branch, $inputType = null, $parent_ids = array()) {
		$url = "";
		$class = "";
		$res = '<ul data-role="listview" data-split-icon="gear" data-split-theme="d" data-inset="true">';

		foreach ($branch as $section) {

			if (empty($inputType)) {
				$url = $section["id"];
				if ( (!empty($this->treeParams["named"]["id"]) && $this->treeParams["named"]["id"] == $section["id"])
						|| !empty($this->treeParams["pass"][0]) && $this->treeParams["pass"][0] == $section["id"]) {
					$class = " class='on'";
				} else {
					$class = "";
				}
			}

			$liClass = "sec_" . $section['status'];
			// check if it's a protecetd section
			if (!empty($section["num_of_permission"])) {
				$liClass .= " protected";
			}

			// check it's a hidden section (from menu and canonical path)
			if ($section["menu"] == 0) {
				$liClass .= " menuhidden";
			}

			$res .= "<li class='" . $liClass . "' id='pub_" . $section['id'] . "'>";
			$res .= "<a " . $class . " rel='" . $url . "'>";

			if (!empty($inputType) && !empty($this->tags[$inputType])) {
				$checked = (in_array($section["id"], $parent_ids))? "checked='checked'" : "";
				$checked .= ' class="ui-li-thumb"';
				$res .= sprintf($this->tags[$inputType], $section["id"], $checked);
			} else {

			}

			$res .= h($section["title"]) . "</a>";
			/*
			if (!empty($inputType) && !empty($this->tags[$inputType])) {
				$res .= "<a target='_blank' title='go to this section' href='".$this->Html->url('/areas/view/').$section['id']."'> › </a>";
			}
			*/
			if (!empty($section["children"])) {
				$res .= $this->designBranch($section["children"], $inputType, $parent_ids);
			}

			$res .= "</li>";
		}
		$res .= "</ul>";
		return $res;
	}

	/**
	 * output a tree
	 *
	 * @param array $tree, publications tree
	 * @param string $inputType, type of input to prepend to section name (checkbox, radio)
	 * @param array $parent_ids, array of ids parent
	 * @return string html for simple view tree
	 */
	public function view($tree = array(), $inputType = null, $parent_ids = array()) {

		$output = "";
		$url = "";

		if (!empty($tree)) {

			foreach ($tree as $publication) {

				$class = "treeAreaTitle " . $publication['id'];

				if (empty($inputType)) {
					if (!empty($this->treeParams["pass"][1]) && !empty($this->tags[$this->treeParams["pass"][1]]) ) {
						$inputType = $this->treeParams["pass"][1];
						if ($inputType == 'option') {
							return $this->option($tree);
						}
					} else {
						$url = $publication["id"];
						if ( (!empty($this->treeParams["named"]["id"]) && $this->treeParams["named"]["id"] == $publication["id"])
								|| !empty($this->treeParams["pass"][0]) && $this->treeParams["pass"][0] == $publication["id"]
								|| $this->SessionFilter->read('parent_id') == $publication['id'] ) {
							$class .= ' on';
						}
					}
				}

				$output .= "<div class='pub'><h2 id='pub_" . $publication['id'] . "'";
				// add publication's permission icon
				if (!empty($publication["num_of_permission"])) {
					$output .= " class='protected'";
				}
				$output .= ">";

				$output .= '<a class="' . $class . '"';
				if ($inputType == null) {
					$output .= ' rel="' . $url . '"';
				}
				$output .= '>';

				if (!empty($inputType) && !empty($this->tags[$inputType])) {
					$checked = (in_array($publication['id'], $parent_ids))? 'checked="checked"' : '';
					$output .= sprintf($this->tags[$inputType], $publication['id'], $checked) ;
				}

				$output .= h($publication['title']);

				$output .= '</a>';

				$output .= '</h2>';

				if (!empty($publication["children"])) {
					$output .= $this->designBranch($publication["children"], $inputType, $parent_ids, $publication['id']);
				} else {
					$url = $this->Html->url('/pages/tree/' . $publication['id']);
					$data = '';
					if (!empty($this->treeParams["controller"])) {
						$data .= ' data-controller="' . $this->Html->url('/'.$this->treeParams["controller"]) . '"';
					}
					if (!empty($this->treeParams["action"])) {
						$data .= ' data-action="' . $this->treeParams["action"] . '"';
					}
					if ($inputType != null) {
						$data .= ' data-type="' . $inputType . '"';
						$url .= '/' . $inputType;
					}

					$output .= '<ul class="menutree" rel="' . $url . '"' . $data . '></ul>';
				}
				$output .= "</div>";
			}

		}
		return $this->output($output);

	}

	/**
	 * output sitemap tree
	 *
	 * @param array $sections, sections tree
	 * @param string $public_url, public url of publication
	 * @return string html for sitemap
	 */
	public function sitemap($sections=array(),$public_url='/') {
		$output = '<ul id="sitemap">';
		$output .= $this->designsitemap($sections,$public_url);
		$output .= '</ul>';
		return $this->output($output);
	}

	/**
	 * get sitemap
	 *
	 * @param array $sections
	 * @param string $public_url
	 */
	private function designsitemap($sections=array(),$public_url='/') {
		$output = '';
		if (!empty($sections)) {
			foreach($sections as $section) {
				$show = !isset($section["menu"]) ? true : (($section["menu"] === '0') ? false : true);
				if($show) {
					$output .= '<li class="Section">';
					$url = $public_url . $section['canonicalPath'];
					$output .= '<a href="' . $url . '">';
					$output .= h($section['title']);
					$output .= '</a>';
					if(!empty($section['objects'])) {
						$output .= '<ul class="contents">';
						$children = $section['objects'];
						foreach($children as $child) {
							$output .= '<li class="' . Configure::read('objectTypes.' . $child['object_type_id'] . ".model") . '">';
							$url = $public_url . $child['canonicalPath'];
							$output .= '<a href="' . $url . '">';
							$output .= h($child['title']);
							$output .= '</a>';
							$output .= '</li>';
						}
						$output .= '</ul>';
					}
					$output .= '</li>';
				}
				if(!empty($section['sections'])) {
					$outMap = $this->designsitemap($section['sections'],$public_url);
					if(!empty($outMap)) {
						if($show) {
							$output .= '<ul>' . $outMap . '</ul>';
						} else {
							$output .= '<li>' . $outMap . '</li>';
						}
					}
				}
				if ($show) {
					$output .= '</li>';
				}
			}
		}
		return $output;
	}

	/**
	 * build option for select
	 *
	 * @param array $tree
	 * @param int $numInd number of $indentation repetition foreach branch
	 * @param string $indentation string to use for indentation
	 *
	 * @return string 	<option value="">...</option>
	 * 		   			<option value="">...</option>
	 * 					....
	 */
	public function option($tree, $selId=null, $numInd=1, $indentation="&nbsp;&nbsp;&nbsp;&nbsp;") {

		$output = "";

		if (!empty($tree)) {
			foreach ($tree as $publication) {
				$params = ' class="pubOption" rel="'. $this->Html->url('/pages/tree/' . $publication["id"]) . '/option"';
				if (!empty($publication["children"])) {
					$params .= ' data-loaded="true"';
				}
				$path = h(mb_strtoupper($publication["title"]));
				$params .= ($selId == $publication["id"])? ' selected' : '';
				$output .= sprintf($this->tags['option'], $publication["id"], $params, $path) ;
				if (!empty($publication["children"])) {
					$output .= $this->optionBranch($publication["children"], $selId, $numInd, $indentation, $path);
				}
			}
		}

		return $this->output($output);

	}

	/**
	 * get html section
	 *
	 * @param array $branch, section
	 * @param string $inputType, type of input to prepend to section name (checkbox, radio)
	 * @param array $parent_ids, array of ids parent
	 * @param string $parent_id, the bedita id of the parent
	 * @return string html for section simple tree
	 */
	private function designBranch($branch, $inputType, $parent_ids, $parent_id) {
		$url = "";
		$class = "";

		$data = '';
		if (!empty($this->treeParams["controller"])) {
			$data .= ' data-controller="' . $this->Html->url('/'.$this->treeParams["controller"]) . '"';
		}
		if (!empty($this->treeParams["action"])) {
			$data .= ' data-action="' . $this->treeParams["action"] . '"';
		}
		if ($inputType != null) {
			$data .= ' data-type="' . $inputType . '"';
		}

		$res = '<ul class="menutree" rel="'. $this->Html->url('/pages/tree/' . $parent_id ). '"' . $data . '>';

		foreach ($branch as $section) {

			if (empty($inputType)) {
				$url = $section["id"];
				if ( (!empty($this->treeParams["named"]["id"]) && $this->treeParams["named"]["id"] == $section["id"])
						|| !empty($this->treeParams["pass"][0]) && $this->treeParams["pass"][0] == $section["id"]
						|| $this->SessionFilter->read('parent_id') == $section['id'] ) {
					$class = " class='on'";
				} else {
					$class = "";
				}
			}

			$liClass = "sec_" . $section['status'];
			// check if it's a protecetd section
			if (!empty($section["num_of_permission"])) {
				$liClass .= " protected";
			}

			// check it's a hidden section (from menu and canonical path)
			if ($section["menu"] == 0) {
				$liClass .= " menuhidden";
			}

			$res .= "<li class='" . $liClass . "' id='sec_" . $section['id'] . "'>";
			$res .= "<a " . $class . " rel='" . $url . "'>";

			if (!empty($inputType) && !empty($this->tags[$inputType])) {
				$checked = (in_array($section["id"], $parent_ids))? "checked='checked'" : "";
				$res .= sprintf($this->tags[$inputType], $section["id"], $checked);

			}

			$res .= h($section["title"]) . "</a>";

			if (!empty($inputType) && !empty($this->tags[$inputType])) {
				$res .= "<a target='_blank' title='go to this section' href='".$this->Html->url('/areas/view/').$section['id']."'> › </a>";
			}

			if (!empty($section["children"])) {
				$res .= $this->designBranch($section["children"], $inputType, $parent_ids, $section['id']);
			}

			$res .= "</li>";
		}
		$res .= "</ul>";
		return $res;
	}


	/**
	 * build branch
	 *
	 * @param $branch
	 * @param int $numInd number of repetition on $indentation string foreach branch
	 * @param string $indentation string to use for indentation
	 *
	 * @return string of option
	 */
	private function optionBranch($branch, $selId, $numInd, $indentation, $path) {

		if (!isset($this->numInd)) {
			$this->numInd = $numInd;
		}

		if (empty($space)) {
			$space = "";
		}

		if (empty($res)) {
			$res = "";
		}

		for ($i = 1; $i <= $numInd; $i++) {
			$space .= $indentation;
		}

		foreach ($branch as $section) {
			$params = ' class="depth' . $numInd . '"';
			$params .= ($selId == $section['id'])? ' selected' : '';
			$npath = $path . ' > ' . h($section["title"]);
			$res .= sprintf($this->tags['option'], $section["id"], $params, $npath) ;
			if (!empty($section["children"])) {
				$res .= $this->optionBranch($section["children"], $selId, $numInd+$this->numInd, $indentation, $npath);
			}

		}

		return $res;
	}

}

?>