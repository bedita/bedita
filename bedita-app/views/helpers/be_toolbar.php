<?
/**
 *
 * Torna l'html per creare i link di paginazione di un recordset.
 * Tra le variabili deve essere presente l'array toolbar 
 *
 * @package
 * @subpackage
 * @author  giangi@qwerg.com
 */
class BeToolbarHelper extends AppHelper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html');

	var $tags = array(
		'with_text' => '<span %s >%s</span>',
		'without_text' => '<span %s />'
	) ;
	
	/**
	 * Costruisce il link per la pagina sucessiva 
	 *
	 * @param string $title				Label link
	 * @param array $option				Attributi HTML per il link
	 * @param string $disabledTitle		Label link disabilitato
	 * @param array  $disabledOption	Attributi HTML per il link disabilitato 
	 * 									(se presente inserisce un tag SPAN)
	 */
	function next($title = ' > ', $options = array(), $disabledTitle = ' > ', $disabledOption = array()) {
		return $this->_scroll('next', $title, $options, $disabledTitle, $disabledOption) ;
	}
	
	/**
	 * Costruisce il link per la pagina precedente 
	 *
	 * @param string $title				Label link
	 * @param array $option				Attributi HTML per il link
	 * @param string $disabledTitle		Label link disabilitato
	 * @param array  $disabledOption	Attributi HTML per il link disabilitato 
	 * 									(se presente inserisce un tag SPAN)
	 */
	function prev($title = ' < ', $options = array(), $disabledTitle = ' < ', $disabledOption = array()) {
		return $this->_scroll('prev', $title, $options, $disabledTitle, $disabledOption) ;
	}

	/**
	 * Costruisce il link per la prima pagina 
	 *
	 * @param string $title				Label link
	 * @param array $option				Attributi HTML per il link
	 * @param string $disabledTitle		Label link disabilitato
	 * @param array  $disabledOption	Attributi HTML per il link disabilitato 
	 * 									(se presente inserisce un tag SPAN)
	 */
	function first($title = ' |< ', $options = array(), $disabledTitle = ' |< ', $disabledOption = array()) {
		return $this->_scroll('first', $title, $options, $disabledTitle, $disabledOption) ;
	}

	/**
	 * Costruisce il link per l'ultima pagina 
	 *
	 * @param string $title				Label link
	 * @param array $option				Attributi HTML per il link
	 * @param string $disabledTitle		Label link disabilitato
	 * @param array  $disabledOption	Attributi HTML per il link disabilitato 
	 * 									(se presente inserisce un tag SPAN)
	 */
	function last($title = ' >| ', $options = array(), $disabledTitle = ' >| ', $disabledOption = array()) {
		return $this->_scroll('last', $title, $options, $disabledTitle, $disabledOption) ;
	}

	/**
	 * Costruisce il link per la pagina indicata 
	 *
	 * @param string $where				pagina richiesta (next, prev, first, last)
	 * @param string $title				Label link
	 * @param array $option				Attributi HTML per il link
	 * @param string $disabledTitle		Label link disabilitato
	 * @param array  $disabledOption	Attributi HTML per il link disabilitato 
	 * 									(se presente inserisce un tag SPAN)
	 */
	private function _scroll($where, $title, $options, $disabledTitle, $disabledOption) {
		$page = (isset($this->params['toolbar'][$where]))?$this->params['toolbar'][$where]:false ;
		
		// Non c'e' la pagina sucessiva o la toolar, link disabilitato
		if(!$page) {
			return $this->_output($disabledTitle, $disabledOption) ;
		}
		
		// Crea l'url
		$data	= array( "controller" => $this->params["controller"],"action" => $this->params["action"], "plugin" => $this->params["plugin"]) ;		
		foreach ($this->namedArgs as $k => $v) {
			$data[$k] = $v ;
		}
		$data['page'] = $page ;
		
		$url = Router::url($data) ;	

		return $this->Html->link($title, $url, $options);
	}

	private function _output($text, $options) {
		return $this->output(
			sprintf(
					(($text)?$this->tags['with_text']:$this->tags['without_text']), 
					$this->_parseAttributes($options, null, ' ', ''), $text
			)
		);
	}
}


?>