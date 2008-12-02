<?php
/**
 *
 * Torna l'html per l'inserimento di un treeView
 * dell'abero dei contenuti.
 *
 * @package
 * @subpackage
 * @author  giangi@qwerg.com
 */
class BeTreeHelper extends Helper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array("Html");

	var $tags = array(
		'tree'		=> "<ul class=\"publishingtree\" id=\"%s\">\n\t%s\n</ul>\n",
		'children'	=> "<ul>%s</ul>\n",

		'area'		=> "<li class=\"area\">\n\t<input type='hidden' name='id' value='%s'/>\n\t<span class=\"AreaItem\">%s</span>\n\t%s\n</li>\n",
		'section'	=> "<li class=\"section\">\n\t<input type='hidden' name='id' value='%s'/>\n\t<span class=\"SectionItem\">%s</span>\n\t%s\n</li>\n",
	
		'option'	=> "<option value=\"%s\"%s>%s</option>",
		'checkbox'	=> "<input type=\"checkbox\" name=\"data[destination][]\" value=\"%s\" %s/>",
		'radio'	=> "<input type=\"radio\" name=\"data[destination]\" value=\"%s\" %s/>"
	) ;

	/**
	 * Scrive l'albero dei contenuti.
	 * Per il formato dell'albero vedere il:
	 *		BeTreeComponent::getSectionsTree()
	 *
	 * @param array $id		ID da assegnare al tag UL radice della treeView
	 * @param array $data	array con i dati
	 * @param 		$excludeSubTreeId	ID dell'oggetto/sottoalbero da escludere
	 * @return string
	 */
	function tree($id, &$data, $excludeSubTreeId = null) {
		$html = "" ;

		for ($i=0; $i < count($data) ; $i++) {
			$html .= $this->treeBranch($data[$i],$excludeSubTreeId) ;
		}

		$html = sprintf($this->tags['tree'], $id, $html) ;

		return $html ;
	}

	private function treeBranch(&$item, $excludeSubTreeId = null)  {
		if( ($excludeSubTreeId != null) && ($item['id'] == $excludeSubTreeId) )
			return "";
		
		$conf = Configure::getInstance() ;

		$key = array_search($item['object_type_id'], $conf->objectTypes) ;

		if(!isset($this->tags[$key])) return "" ;

		// Preleva il testo dei figli
		$txtChildren = "" ;
		if(isset($item['children'])) {
			for($i=0; $i < count($item['children']) ; $i++) {
				$txtChildren .= $this->treeBranch($item['children'][$i], $excludeSubTreeId) ;
			}
			if(count($item['children'])) {
				$txtChildren = sprintf($this->tags['children'], $txtChildren) ;
			}
		}
		
		// Crea l'html per il tag
		$txt = sprintf($this->tags[$key], $item['id'], $item['title'], $txtChildren) ;

		return $txt ;
	}
	
	
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
				
				$output .= "<div><h2 id='pub_" . $publication['id'] . "'>";
				
				if (!empty($inputType) && !empty($this->tags[$inputType])) {
					$checked = (in_array($publication["id"], $parent_ids))? "checked='checked'" : "";
					$output .= sprintf($this->tags[$inputType], $publication["id"], $checked) ;
				}
				
				$output .= "<a ".$class." rel='" . $url . "'>" . $publication["title"] . "</a></h2>";
				
				if (!empty($publication["children"])) {
					$output .= $this->designBranch($publication["children"], $inputType, $parent_ids);
				}
				$output .= "</div>";
			}
			
		}
		return $this->output($output);
		
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
		$res = "<ul>";
		
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
			
			$res .= "<li id='pub_" . $section['id'] . "'><a " . $class . " rel='" . $url . "'>" . $section["title"] . "</a>";
			
			if (!empty($inputType) && !empty($this->tags[$inputType])) {
				$checked = (in_array($section["id"], $parent_ids))? "checked='checked'" : "";
				$res .= sprintf($this->tags[$inputType], $section["id"], $checked);
				
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