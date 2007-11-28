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
	var $helpers = array('Form', 'Html');

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
	 * Torna il numero di record trovati
	 *
	 */
	function size() {
		return (isset($this->params['toolbar']['size'])?$this->params['toolbar']['size']:"" ) ;
	}

	/**
	 * Torna la pagina corrente
	 *
	 */
	function current() {
		return (isset($this->params['toolbar']['page'])?$this->params['toolbar']['page']:"" ) ;
	}

	/**
	 * Torna il numero totale di pagine
	 *
	 */
	function pages() {
		return (isset($this->params['toolbar']['pages'])?$this->params['toolbar']['pages']:"" ) ;
	}

	/**
	 * Visualizza il tag select per la selezione delle dimensioni della lista
	 *
	 * @param array $htmlAttributes		Array associativo con gli attributi HTML
	 * @param arry $options				Array. Default: 1, 5, 10,20, 50, 100
	 */
	function changeDim($htmlAttributes = array(), $options = array(1, 5, 10, 20, 50, 100)) {
		if(!isset($this->params['toolbar']['dim'])) return "" ;

		// Definisce lo script per il cambio di pagina
		$data	= array( "controller" => $this->params["controller"],"action" => $this->params["action"], "plugin" => $this->params["plugin"]) ;
		foreach ($this->namedArgs as $k => $v) {
			if($k != "dim") $data[$k] = $v ;
			$data[$k] = $v ;
		}
		$url = Router::url($data) ;
		$htmlAttributes['onchange'] = "document.location = '{$url}'+'/dim:'+ this[this.selectedIndex].value +'/page:1'" ;

		$tmp = array() ;
		foreach ($options as $k) $tmp[$k] = $k ;
		$options = $tmp ;

		return $this->Form->select("", $options, $this->params['toolbar']['dim'], $htmlAttributes, false) ;
	}

	function changeDimSelect($selectId, $htmlAttributes = array(), $options = array(1, 5, 10, 20, 50, 100)) {
		if(!isset($this->params['toolbar']['dim'])) return "" ;

		// Definisce lo script per il cambio di pagina
		$data	= array( "controller" => $this->params["controller"],"action" => $this->params["action"], "plugin" => $this->params["plugin"]) ;
		foreach ($this->namedArgs as $k => $v) {
			if($k != "dim") $data[$k] = $v ;
			$data[$k] = $v ;
		}
		$url = Router::url($data) ;
		$htmlAttributes['onchange'] = "document.location = '{$url}'+'/dim:'+ this[this.selectedIndex].value" ;

		$tmp = array() ;
		foreach ($options as $k) $tmp[$k] = $k ;
		$options = $tmp ;

		return $this->Form->select($selectId, $options, $this->params['toolbar']['dim'], $htmlAttributes, false) ;
	}

	/**
	 * Cambia la pagina selezionata
	 *
	 * @param array $htmlAttributes		Array associativo con gli attributi HTML
	 * @param arry $items				numero di pagine selezionabili prima e dopo la corrente. Default: 5
	 */
	function changePage($htmlAttributes = array(),	$items = 5) {
		if(!isset($this->params['toolbar']['page'])) return "" ;

		// Definisce lo script per il cambio di pagina
		$data	= array( "controller" => $this->params["controller"],"action" => $this->params["action"], "plugin" => $this->params["plugin"]) ;
		foreach ($this->namedArgs as $k => $v) {
			$data[$k] = $v ;
		}
		$url = Router::url($data) ;
		$htmlAttributes['onchange'] = "document.location = '{$url}'+'/page:'+ this[this.selectedIndex].value" ;

		// Definisce il numero di pagine selezionabili
		$pages = array() ;
		for($i = $this->params['toolbar']['page']; $i >= 1 ; $i--) {
			$pages[] =  $i ;
		}

		for($i = $this->params['toolbar']['page']; $i <= $this->params['toolbar']['pages'] ; $i++) {
			$pages[] =  $i ;
		}
		sort($pages) ;

		// Visualizza il select
		$tmp = array() ;
		foreach ($pages as $k) $tmp[$k] = $k ;
		$pages = $tmp ;

		return $this->Form->select("", $pages, $this->params['toolbar']['page'], $htmlAttributes, false) ;
	}

	function changePageSelect($selectId, $htmlAttributes = array(),	$items = 5) {
		if(!isset($this->params['toolbar']['page'])) return "" ;

		// Definisce lo script per il cambio di pagina
		$data	= array( "controller" => $this->params["controller"],"action" => $this->params["action"], "plugin" => $this->params["plugin"]) ;
		foreach ($this->namedArgs as $k => $v) {
			$data[$k] = $v ;
		}

		$url = Router::url($data) ;
		$htmlAttributes['onchange'] = "document.location = '{$url}'+'/page:'+ this[this.selectedIndex].value" ;

		// Definisce il numero di pagine selezionabili
		$pages = array() ;
		for($i = $this->params['toolbar']['page']; $i >= 1 ; $i--) {
			$pages[] =  $i ;
		}

		for($i = $this->params['toolbar']['page']; $i <= $this->params['toolbar']['pages'] ; $i++) {
			$pages[] =  $i ;
		}
		sort($pages) ;

		// Visualizza il select
		$tmp = array() ;
		foreach ($pages as $k) $tmp[$k] = $k ;
		$pages = $tmp ;

		return $this->Form->select($selectId, $pages, $this->params['toolbar']['page'], $htmlAttributes, false) ;
	}

	/**
	 * Cambia l'ordina della lista
	 *
	 * @param string $field				Nome del campo su cui si fa l'ordinamento
	 * @param string $title				Titolo al link. Default: nome del campo
	 * @param array $htmlAttributes		Array associativo con gli attributi HTML
	 * @param boolean $dir				Se presente impone la direzione. 1: ascendente, 0: discendente
	 * 									altrimenti mette la direzione opposta della corrente.
	 */
	function order($field, $title = "", $htmlAttributes = array(),	$dir = null) {
		$title = __($title, true);
		if(!isset($this->params['toolbar'])) return "" ;

		if(!isset($this->namedArgs['order'])) $this->namedArgs['order'] = "" ;
		if(!isset($this->namedArgs['dir'])) $this->namedArgs['dir'] = true ;

		if($this->namedArgs['order'] == $field) {
			if(!isset($dir)) $dir = !$this->namedArgs['dir'] ;
		}  else {
			if(!isset($dir)) $dir = true ;
		}

		// Crea l'url
		$data	= array( "controller" => $this->params["controller"],"action" => $this->params["action"], "plugin" => $this->params["plugin"]) ;
		foreach ($this->namedArgs as $k => $v) {
			if($k != "order" && $k != "dir") $data[$k] = $v ;
		}
		$data['order'] 	= $field ;
		$data['dir'] 	= (integer)$dir ;

		$url = Router::url($data) ;

		return $this->Html->link(__($title, true), $url, $htmlAttributes);
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
			if($k != "page") $data[$k] = $v ;
		}
		$data['page'] = $page ;

		$url = Router::url($data) ;
		return '<a href="' . $url . '">' . __($title, true) . '</a>';
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