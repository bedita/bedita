<?
/**
 *
 * Torna l'html per l'inserimento di un treeView
 * dell'abero dei contenuti.
 * 
 * @package		
 * @subpackage	
 */
class BeTreeHelper extends Helper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array();
		
	var $tags = array(
		'tree'		=> "<ul id=\"%s\">\n\t%s\n</ul>\n",
		'children'	=> "<ul>%s</ul>\n",
		
		'area'		=> "<li>\n\t<span class=\"AreaItem\">%s</span>\n\t%s\n</li>\n",
		'section'	=> "<li>\n\t<span class=\"SectionItem\">%s</span>\n\t%s\n</li>\n",
	) ;
	
	/**
	 * Scrive l'abero dei contenuti.
	 * Per il formato dell'albero vedere il:
	 *		BeTreeComponent::getSectionsTree()
	 * 
	 * @param array $id		ID da assegnare al tag UL radice della treeView
	 * @param array $data	array con i dati
	 * @return string
	 */
	function tree($id, &$data) {
		$html = "" ;
		
		for ($i=0; $i < count($data) ; $i++) {
			$html .= $this->treeBranch($data[$i]) ;
		}
		
		$html = sprintf($this->tags['tree'], $id, $html) ;
		
		return $html ;
	}
	
	private function treeBranch(&$item)  {
		$conf = Configure::getInstance() ;
		
		$key = array_search($item['object_type_id'], $conf->objectTypes) ;
		
		if(!isset($this->tags[$key])) return "" ;
		
		// Preleva il testo dei figli
		$txtChildren = "" ;
		for($i=0; $i < count($item['children']) ; $i++) {
			$txtChildren .= $this->treeBranch($item['children'][$i]) ;
		}
		if(count($item['children'])) {
			$txtChildren = sprintf($this->tags['children'], $txtChildren) ;
		}
		
		// Crea l'html per il tag
		$txt = sprintf($this->tags[$key], $item['title'], $txtChildren) ;
		
		return $txt ;
	}
}

?>