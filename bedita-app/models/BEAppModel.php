<?
/**
 * 
 * Estende la classe AppModel.
 * 
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2006
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 */

vendor('smarty/libs/Smarty.class');

class BEAppModel extends AppModel {
	
	/**
	 * Collassa il risultato di un record in un array unico
	 * 
	 * @param array record	record da collassare
	 * 
	 * @return array		record collassato
	 */
	function am($record) {
		$tmp = array() ;
		foreach ($record as $key => $val) {
			if(is_array($val)) $tmp = array_merge($tmp, $val) ;
			else $tmp[$key] = $val ;
		}
		
		return $tmp ;
	}
	
	/**
	 * Torna una lista di contenuti. Il tipo di contenuti e' definito da $name.
	 * Il nome pu˜ essere cambiato dalle classe derivate.
	 *
	 * @param array $recordset		Dove inserire il risultato
	 * @param integer $page			Pagina dell'elenco richiesta
	 * @param integer $dim			Dimensione della pagina
	 * @param string $order			Campo su cui ordinare.Anche nome_campo + spazio + DESC
	 * @return boolean
	 */
	
	/**
	 * Torna il risultato di una ricerca findAll, paginata
	 *
	 * @param unknown_type $conditions
	 * @param unknown_type $fields
	 * @param unknown_type $order
	 * @param unknown_type $page
	 * @param unknown_type $dim
	 * @return boolean
	 */
/*	
	function find($conditions = null, $fields = null, $order = null, $page = 1, $dim = 100000) {
		// Esegue la ricerca
        if(($tmp = $this->findAll($conditions, $fields, $order, $dim, $page, 0)) === false) return false ;

		// Formatta il record set da tornare
		for ($i =0; $i < count($tmp); $i++) {
			$tmp[$i] = $this->am($tmp[$i]);
		}

		$recordset = array(
			"items"		=> &$tmp,
			"toolbar"	=> $this->toolbar($page, $dim, $conditions)
		) ;
		
		
		return $recordset ;
	}
*/	
	
	/**
	 * Esegue una query in un template di smarty e torna il recordset.
	 * la query puo' esserein un file di template o in una stringa 
	 *
	 * @param string $sql		Path di un file o stringa con la query
	 * @param array $params		Array con i parametri da passare, puo' essere vuoto
	 * @param mixed $recordset	Dove tornare il risultato
	 */
	function execTemplate($sql, &$params = null, $page = 1, $dim = null, $file = true) {
		
		// Crea la query
		$templater = new SQLFromSmarty() ;
		$sql = $templater->getSQLFromTemplate($sql, $params, $file) ;
		unset($templater);
		
		// Definisce i limiti del recordset
		$offset = null;
		if ($page > 1 && $dim != null) {
			$offset = ($page - 1) * $dim;
		}
				
		// esegue la query
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$sql .= $db->limit($dim, $offset);			
		$recordset = $db->fetchAll($sql, $this->cacheQueries, $this->name);
		unset($db) ;
		
		return $recordset ;
	}
		
	/**
	 * Crea una toolbar da una ricerca
	 *
	 * @param integer 	$page		
	 * @param integer 	$dimPage	
	 * @param mixed 	$condition	condizione utilizzata per la ricerca
	 * @param boolean 	$recursive	TRUE, preleva gli ogetti connessi
	 * @return array
	 */
	function toolbar($page = null, $dimPage = null, $condition = null, $recursive = null) {
		// conta il numero di record
		if(($size = $this->findCount($condition, $recursive)) === false) return false ;
		
		$toolbar = array("first" => 0, "prev" => 0, "next" => 0, "last" => 0, "size" => 0, "pages" => 0, "page" => 0) ;
		
		if(!$page || empty($page)) $page = 1 ;
		if(!$dimPage || empty($dimPage)) $dimPage = $size ;
		
		$pageCount = $size / $dimPage ;
		settype($pageCount,"integer");
		if($size % $dimPage) $pageCount++ ;
		
		$toolbar["pages"] = $pageCount ;
		$toolbar["page"]  = $page ;
				
		if($page == 1) {
			if($page >= $pageCount) {
				// Una sola
				
			} else {
				// Prima pagina
				$toolbar["next"] = $page+1 ;
				$toolbar["last"] = $pageCount ;
			} 
		} else {
			if($page >= $pageCount) {
				// Ultima
				$toolbar["first"] = 1 ;
				$toolbar["prev"] = $page-1 ;
			} else {
				// Pagina di mezzo
				$toolbar["next"] = $page+1 ;
				$toolbar["last"] = $pageCount ;
				$toolbar["first"] = 1 ;
				$toolbar["prev"] = $page-1 ;
			}
		}

		$toolbar["start"]	= (($page-1)*$dimPage)+1 ;
		$toolbar["end"] 	= $page * $dimPage ;
		if($toolbar["end"] > $size) $toolbar["end"] = $size ;
		
		$toolbar["size"] = $size ;
		
		return $toolbar ;	
	}
}

///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
/**
 * Classe utilizata per le viste nn fa niente 
 * un solo metodo: afterFind
 */
class _emptyAfterFindView {
	function afterFind($result) { return $result ; }
}


///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
/**
 * Classe che crea una query SQL da template Smarty e 
 * parametri. 
 */
class SQLFromSmarty extends Object {
		var $template_dir;
		var $compile_dir;
		var $cache_dir;
		var $config_dir;
		
		var $smarty = NULL;
		var $_DBSQL_CACHE = null ;
		var	$_dataTemplate = null ;
		var	$_timeTemplate = null ;
		
		var $processedTpl = NULL;
				
		function __construct ()
		{
			parent::__construct();
			
			$this->_DBSQL_CACHE = array() ;
			$this->ext = ".sql";
			
			$this->template_dir = VIEWS . "SQL" . DS ;
			
			$this->compile_dir 	= TMP . 'smarty' . DS . 'compile' ;
			$this->cache_dir 	= TMP . 'smarty' . DS . 'cache' . DS;
			$this->config_dir 	= ROOT . APP_DIR.DS . 'config' . DS . 'smarty' . DS;
			
			$this->smarty = & new Smarty();

			$this->smarty->compile_dir 	= $this->compile_dir;
			$this->smarty->cache_dir 		= $this->cache_dir;
			$this->smarty->config_dir 		= $this->config_dir;
			
			// Aggiunta Giangi
			$this->smarty->plugins_dir[] = ROOT . DS . APP_DIR . DS . 'vendors' . DS . '_smartyPlugins' ;
/*			
			$svckResFuncs = array(
				__CLASS__ . "::get_template",
				__CLASS__ . "::get_timestamp",
				__CLASS__ . "::get_secure",
				__CLASS__ . "::get_trusted");
*/
			$svckResFuncs = array(
				__CLASS__ . "_get_template",
				__CLASS__ . "_get_timestamp",
				__CLASS__ . "_get_secure",
				__CLASS__ . "_get_trusted");
			
			$this->smarty->register_resource("DBSQL", $svckResFuncs);

			$this->smarty->sv_this = &$this;
		}

		function & getSmarty()
		{
			return ($this->smarty);
		}


		// cambia la directory template 
		function setTemplateDir($path = VIEW) {
			$old = $this->template_dir ;
			$this->template_dir  = $path ;
			
			return $old ;
		}
		
		function getTemplateDir() {
			return $this->template_dir ;
		}

		/**
		 * Dato un template o una stringa torna la query SQL
		 *
		 * @param string 	$sql	Path del template o direttamente una stringa 
		 * @param mixed 	$params	Array con i dati per la costruzione della query
		 * @param boolean	se TRUE, il primo parametro e' un file
		 */
		function getSQLFromTemplate($sql, &$params = null, $file = true) {
			if($file) {
				$ret = $this->_getSQLFromTemplate($sql, $params) ;
			} else {
				if(empty($params)) $ret = $sql ;
				else $ret = $this->_getSQLFromString($sql, $params) ;
			}
			
			return $ret ;
		}
		
		/**
		 * Dato un template torna la query SQL
		 *
		 * @param string 	$path	Path del template
		 * @param mixed 	$params	Array con i dati per la costruzione della query
		 */
		function _getSQLFromTemplate($path, &$params = null) {
			// Legge i dati dal file
			if(array_key_exists($path, $this->_DBSQL_CACHE)) $ret = $this->_DBSQL_CACHE[$path] ;
			else {
				$tmp = file($this->template_dir . $path) ;
				$ret = preg_replace("/\r/", "", implode("", $tmp));
				$_DBSQL_CACHE[$path] = $ret ;
			}

			// Inserisce nel template i dati e il time
			clearstatcache();
			$this->_dataTemplate = &$ret ;
			$this->_timeTemplate = filemtime($this->template_dir . $path) ;

			// Processa tutti i dati
			if(!empty($params)) $this->smarty->assign($params) ;

			$ret = $this->smarty->fetch("DBSQL:".$path) ;

			unset($this->_dataTemplate);
			
			return $ret ;
		}
		
		/**
		 * Data una stringa torna la query SQL
		 *
		 * @param string 	$strSQL Stringa template
		 * @param mixed 	$params	Array con i dati per la costruzione della query
		 */
		function _getSQLFromString($strSQL, &$params = null) {

			// Inserisce nel template i dati e il time
			clearstatcache();
			$this->_dataTemplate = preg_replace("/\r/", "", $strSQL);
			$this->_timeTemplate = time() ;

			// Processa tutti i dati
			if(!empty($params)) $this->smarty->assign($params) ;
			$this->smarty->clear_compiled_tpl("DBSQL:__DBSQLStr") ;
			$ret  = $this->smarty->fetch("DBSQL:__DBSQLStr") ;

			unset($this->_dataTemplate);
			
			return $ret ;
		}

}

function SQLFromSmarty_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
{
	$tpl_source = $smarty_obj->sv_this->_dataTemplate ;
	return true;
}

function SQLFromSmarty_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
{
	$tpl_timestamp = $smarty_obj->sv_this->_timeTemplate ;
	return true;
}

function SQLFromSmarty_get_secure($tpl_name, &$smarty_obj)
{
	return true;
}

function SQLFromSmarty_get_trusted($tpl_name, &$smarty_obj)
{
	return;
}

?>