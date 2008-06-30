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
	 * @return html for simple view tree
	 */
	public function view($tree=array()) {
	
		$output = "";
		if (!empty($tree)) {
			
			foreach ($tree as $publication) {
				$output .= "<div><h2>+ ". $publication["title"] . "</h2>";
				if (!empty($publication["children"])) {
					$output .= $this->designBranch($publication["children"]);
				}
				$output .= "</div>";
			}
			
		}
		return $this->output($output);
		
	}
	
	/**
	 * get html section
	 *
	 * @param array $branch, section
	 * @return html for section simple tree
	 */
	private function designBranch($branch) {
		$res = "<ul>";
		foreach ($branch as $section) {
			$url = $this->Html->url('/') . $this->params["controller"] . "/index/id:" . $section["id"];
			
			$class = (!empty($this->params["url"]["id"]) && $this->params["url"]["id"] == $section["id"])? " class='on'" : "" ;
			$res .= "<li rel='" . $url . "'" . $class . ">" . $section["title"] . "</li>";
			if (!empty($section["children"])) {
				$res .= $this->designBranch($section["children"]);
			}
		}
		$res .= "</ul>";
		return $res;
	}
	
	
	
}

?>