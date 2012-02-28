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
class BeTreeHelper extends AppHelper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array("Html");

	var $tags = array(
		'option'	=> "<option value=\"%s\"%s>%s</option>",
		'checkbox'	=> "<input type=\"checkbox\" name=\"data[destination][]\" value=\"%s\" %s/>",
		'radio'	=> "<input type=\"radio\" name=\"data[destination]\" value=\"%s\" %s/>"
	) ;

		
	/**
	 * output a tree
	 *
	 * @param array $tree, publications tree
	 * @param string $inputType, type of input to prepend to section name (checkbox, radio)
	 * @param array $parent_ids, array of ids parent
	 * @return html for simple view tree
	 */
	public function view($tree=array(), $inputType=null, $parent_ids=array()) {
	
		$output = "";
		$class = "";
		$url = "";
		
		if (!empty($tree)) {
				
			foreach ($tree as $publication) {
								
				if (empty($inputType)) {
					$url = $this->Html->url('/') . $this->params["controller"] . "/" . $this->params["action"] . "/id:" . $publication["id"];
					if ( (!empty($this->params["named"]["id"]) && $this->params["named"]["id"] == $publication["id"]) 
							|| !empty($this->params["pass"][0]) && $this->params["pass"][0] == $publication["id"]) {
						$class = " class='on'";
					} else {
						$class = "";
					}
				}
				
				$output .= "<div class='pub'><h2 id='pub_" . $publication['id'] . "'";
				// add publication's permission icon
				if (!empty($publication["num_of_permission"])) {
					$output .= " class='protected'";
				}
				$output .= ">";
				$output .= "<a ".$class." rel='" . $url . "'>";
				
				if (!empty($inputType) && !empty($this->tags[$inputType])) {
					$checked = (in_array($publication["id"], $parent_ids))? "checked='checked'" : "";
					$output .= sprintf($this->tags[$inputType], $publication["id"], $checked) ;
				}
				
				$output .= $publication["title"] . "</a>";
				$output .= "</h2>";
				
				if (!empty($publication["children"])) {
					$output .= $this->designBranch($publication["children"], $inputType, $parent_ids);
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
	 * @return html for sitemap
	 */
	public function sitemap($sections=array(),$public_url='/') {
		$output = '<ul id="sitemap">';
		$output .= $this->designsitemap($sections,$public_url);
		$output .= '</ul>';
		return $this->output($output);
	}

	private function designsitemap($sections=array(),$public_url='/') {
		$output = '';
		if (!empty($sections)) {
			foreach($sections as $section) {			
				$show = !isset($section["menu"]) ? true : (($section["menu"] === '0') ? false : true);
				if($show) {
					$output .= '<li class="Section">';
					$url = $public_url . $section['canonicalPath'];
					$output .= '<a href="' . $url . '">';
					$output .= $section['title'];
					$output .= '</a>';
					if(!empty($section['objects'])) {
						$output .= '<ul class="contents">';
						$children = $section['objects'];
						foreach($children as $child) {
							$output .= '<li class="' . Configure::read('objectTypes.' . $child['object_type_id'] . ".model") . '">';
							$url = $public_url . $child['canonicalPath'];
							$output .= '<a href="' . $url . '">';
							$output .= $child['title'];
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
	 * @return String 	<option value="">...</option>
	 * 		   			<option value="">...</option>
	 * 					....
	 */
	public function option($tree, $selId=null, $numInd=3, $indentation="&nbsp;") {
		
		$output = "<option value=\"\"> -- </option>";
		
		if (!empty($tree)) {
			foreach ($tree as $publication) {
				$selected = ($selId == $publication["id"])? " selected" : "";
				$output .= sprintf($this->tags['option'], $publication["id"], $selected, mb_strtoupper($publication["title"])) ;
				if (!empty($publication["children"])) {
					$output .= $this->optionBranch($publication["children"], $selId, $numInd, $indentation);
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
	 * @return html for section simple tree
	 */
	private function designBranch($branch, $inputType, $parent_ids) {
		$url = "";
		$class = "";
		$res = "<ul class='menutree'>";
		
		foreach ($branch as $section) {
			
			if (empty($inputType)) {
				$url = $this->Html->url('/') . $this->params["controller"] . "/" . $this->params["action"] . "/id:" . $section["id"];
				if ( (!empty($this->params["named"]["id"]) && $this->params["named"]["id"] == $section["id"]) 
						|| !empty($this->params["pass"][0]) && $this->params["pass"][0] == $section["id"]) {
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
				$res .= sprintf($this->tags[$inputType], $section["id"], $checked);
				
			}
			
			$res .= $section["title"] . "</a>";
			
			if (!empty($inputType) && !empty($this->tags[$inputType])) {
				$res .= "<a target='_blank' title='go to this section' href='".$this->Html->url('/areas/view/').$section['id']."'> › </a>";
			}
			
			if (!empty($section["children"])) {
				$res .= $this->designBranch($section["children"], $inputType, $parent_ids);
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
	private function optionBranch($branch, $selId, $numInd, $indentation) {
		
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
			$selected = ($selId == $section["id"])? " selected" : "";
			$res .= sprintf($this->tags['option'], $section["id"], $selected, $space.$section["title"]) ;
			if (!empty($section["children"])) {
				$res .= $this->optionBranch($section["children"], $selId, $numInd+$this->numInd, $indentation);
			}
			
		}
		
		return $res;
	}
	
	
}

?>