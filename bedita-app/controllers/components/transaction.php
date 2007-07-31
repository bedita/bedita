<?
/**
 * @author giangi@qwerg.com
 * 
 * Componente per la gestione di transazioni su + model contemporaneamente
 * 
 */
class TransactionComponent extends Object {
	
	private static $dbConfig	= 'default' ;
	private static $db			= null ;
	private static $transFS		= null ;
	
	function __construct($dbConfigName = 'default', $pathTmp = '/tmp') {
		$this->init($dbConfigName, $pathTmp) ;
	} 
	
	function init($dbConfigName = 'default', $pathTmp = '/tmp') {
		if(!isset(self::$transFS)) {
			self::$transFS = &new transactionFS($pathTmp) ;
		}
		self::$dbConfig 		= (isset($dbConfigName))?$dbConfigName:'default' ;
		self::$transFS->tmpPath = $pathTmp ;
		
		
		$this->setupDB() ;
		
		
	}

	/**
	 * Inizia una transazione
	 *
	 * @return unknown
	 */
	public function begin() {
		$this->setupDB() ;
		
		self::$transFS->begin() ;
		if(!self::$db->execute('START TRANSACTION')) return false ;
		
		return true  ;
	}
	
	/**
	 * Fine positiva di una transazione
	 *
	 * @return unknown
	 */
	public function commit() {
		$this->setupDB() ;
		
		self::$transFS->commit() ;
		if(!self::$db->execute('COMMIT')) return false ;
	}

	/**
	 * Fine negativa di una transazione
	 *
	 * @return unknown
	 */
	public function rollback() {
		$this->setupDB() ;
		
		self::$transFS->rollback() ;
		
		if(!self::$db->execute('ROLLBACK')) return false ;
	}
	
	//////////////////////////////////////////
	//////////////////////////////////////////
	private function setupDB() {
		if(isset(self::$db)) return ;
		
		if(!class_exists('ConnectionManager')) {
			loadModel('ConnectionManger') ;
		}
		
		if(isset(self::$dbConfig))
			self::$db =& ConnectionManager::getDataSource(self::$dbConfig);
	}

	/**
	 * Crea un file con i dati passati
	 *
	 * @param string $path		path del nuovo file da creare
	 * @param string $arrDati	dati da inserire nel file
	 * @return string
	 */
	function makeFromData($path, &$arrDati) {
		return self::$transFS->makeFileFromData($path, $arrDati) ;
	}
	
	
	/**
	 * Crea un file dal file passato
	 *
	 * @param string $path				path del nuovo file da creare
	 * @param string $pathFileSource	file sorgente
	 * @return string
	 */
	function makeFromFile($path, $pathFileSource) {
		return self::$transFS->makeFileFromFile($path, $pathFileSource) ;
	}

	/**
	 * Cambia i dati di un file
	 *
	 * @param string path		File da cambiare
	 * @param string $arrDati	Nuovi dati
	 * @return boolean
	 */
	function replaceData($path, &$arrDati) {
		return self::$transFS->replaceFileData($path, $arrDati) ;
	}	
	
	/**
	 * Cancella un file
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	function rm($path) {
		return self::$transFS->rmFile($path) ;
	}

	/**
	 * Sposta un file
	 *
	 * @param unknown_type $newPath
	 * @param unknown_type $pathSource
	 * @return unknown
	 */
	function mv($newPath, $pathSource) {
		return self::$transFS->mvFile($newPath, $pathSource) ;
	}

	/**
	 * Copia un file
	 *
	 * @param unknown_type $newPath
	 * @param unknown_type $pathSource
	 * @return unknown
	 */
	function cp($newPath, $pathSource) {
		return self::$transFS->cpFile($newPath, $pathSource) ;
	}
	
}

/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * 
 * Alla creazione dell'oggetto se ci sono delle operazioni pendenti esegue il Rollback ().
 * Le operazioni sono rese permanenti solo con Commit().
 * Le copie dei file da cancellare sono inseriti in una directory temporanea.
 * 
 * struttura dati (uno stack):
 * array(
 *	array("cmd" => <comando>, "roolCmd" => <comando>, "params" => array(..)),
 *	array("cmd" => <comando>, "roolCmd" => <comando>, "params" => array(..)),
 *	...................................................
 * )
 * "cmd"		comando eseguito
 * "roolCmd"	comando inverso
 * "params"	array con i parametri per il rollback, (il numero ed il significato definito da ogni singola operazione)
*/
class transactionFS {
	var $commands				= null ;			// array con i dati
	var $errorMsg				= "" ;				// Ultimo msg d'errore
	var $error					= false ;			// true se c'e' stato un errore
	var $tmpPath				= '/tmp';
	
	// COSTRUTTORE
	function __construct($tmpPath = '/tmp') {
		$this->commands = array() ;
		$this->tmpPath 	= $tmpPath ;
	}

	function begin() {
		$this->rollback() ;
		
		$this->commands = array() ;
	}
	
	function commit() {
		while(count($this->commands)) {
			$item = array_pop($this->commands) ;
			$cmd = $item["cmd"] ;
			switch($item["cmd"]){
				case 'replaceFileData': @unlink($item["params"]["source"]); break ;
				case 'rmFile': 			@unlink($item["params"]["source"]); break ;
			}
		}

		return true ;
	}
	
	function rollback() {
		while(count($this->commands)) {
			$item = array_pop($this->commands) ;

			call_user_func_array(array(&$this, $item["rollCmd"]), $item["params"]) ;
		}

		return true ;
	}
	
	
	
	/**
	 * Crea un file con i dati passati
	 *
	 * @param string $path		path del nuovo file da creare
	 * @param string $arrDati	dati da inserire nel file
	 * @return string
	 */
	function makeFileFromData($path, &$arrDati) {
//		if(fwrite(fopen($path, "wb"), $arrDati, strlen($arrDati)) == -1) return false ;

		if(($fp  = fopen($path, "wb")) === false) {
			return false ;
		}
		
		$ret = fwrite(fopen($path, "wb"), $arrDati, strlen($arrDati)) ;

		// Salva l'operazione
		$item = array("cmd"	=> "makeFile", "rollCmd" => "_rollMakeFile", 	"params" => array("path" => $path)) ;
		if(!array_push($this->commands, $item)) return false ;

		return $path ;
	}
	
	
	/**
	 * Crea un file dal file passato
	 *
	 * @param string $path				path del nuovo file da creare
	 * @param string $pathFileSource	file sorgente
	 * @return string
	 */
	function makeFileFromFile($path, $pathFileSource) {
		if(!copy($pathFileSource, $path)) return false ;
		
		// Salva l'operazione
		$item = array("cmd"	=> "makeFile", "rollCmd" => "_rollMakeFile", 	"params" => array("path" => $path)) ;
		if(!array_push($this->commands, $item)) return false ;

		return $path ;
	}

	/**
	 * Cambia i dati di un file
	 *
	 * @param string path		File da cambiare
	 * @param string $arrDati	Nuovi dati
	 * @return boolean
	 */
	function replaceFileData($path, &$arrDati) {
		// crea un file temporaneo
		$tmpfname = tempnam ($this->tmpPath, "UF");
		if(!is_string($tmpfname)) return false ;

		// salva e copia i vecchi dati
		if(!copy($path, $tmpfname)) return false ;
		if(fwrite(fopen($path, "wb"), $arrDati, strlen($arrDati)) == -1) return false ;

		// Salva l'operazione
		$item = array("cmd"	=> "replaceFileData", "rollCmd" => "_rollReplaceFileData", 	"params" => array("source" => $tmpfname, "dest" => $path)) ;
		if(!array_push($this->commands, $item)) return false ;

		return true ;
	}	
	
	/**
	 * Cancella un file
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	function rmFile($path) {
		$tmpfname = tempnam ($this->tmpPath, "UF");
		if(!is_string($tmpfname)) return false ;

		// salva e copia i vecchi dati
		if(!copy($path, $tmpfname)) return false ;
		
		if(!unlink($path)) return false ;

		// Salva l'operazione
		$item = array("cmd"	=> "rmFile", "rollCmd" => "_rollRmFile", 	"params" => array("source" => $tmpfname, "dest" => $path)) ;
		if(!array_push($this->commands, $item)) return false ;
		
		return true ;
	}

	/**
	 * Sposta un file
	 *
	 * @param unknown_type $newPath
	 * @param unknown_type $pathSource
	 * @return unknown
	 */
	function mvFile($newPath, $pathSource) {
		// salva e copia i vecchi dati
		if(!copy($pathSource, $newPath)) return false ;

		if(!unlink($pathSource)) return false ;

		// Salva l'operazione
		$item = array("cmd"	=> "mvFile", "rollCmd" => "_rollMvFile", 	"params" => array("source" => $newPath, "dest" => $pathSource)) ;
		if(!array_push($this->commands, $item)) return false ;

		return true ;
	}

	/**
	 * Copia un file
	 *
	 * @param unknown_type $newPath
	 * @param unknown_type $pathSource
	 * @return unknown
	 */
	function cpFile($newPath, $pathSource) {
		// salva e copia i vecchi dati
		if(!copy($pathSource, $newPath)) return false ;

		// Salva l'operazione
		$item = array("cmd"	=> "cpFile", "rollCmd" => "_rollCpFile", 	"params" => array("source" => $newPath)) ;
		if(!array_push($this->commands, $item)) return false ;

		return true ;
	}
	
	//////////////////////////////////////////////////
	/**
	 * Funzioni di rollback
	 */
	private function _rollMakeFile($path) {
		if(!unlink($path)) return false ;
		return true ;
	}
	
	private function _rollReplaceFileData($source, $dest) {
		if(!copy($source, $dest)) return false ;
		if(!unlink($source)) return false ;
		return true ;
	}

	private function _rollRmFile($source, $dest) {
		if(!copy($source, $dest)) return false ;
		if(!unlink($source)) return false ;
		return true ;
	}
	
	private function _rollMvFile($source, $dest) {
		if(!copy($source, $dest)) return false ;
		if(!unlink($source)) return false ;
		return true ;
	}

	private function _rollCpFile($source) {
		if(!unlink($source)) return false ;
		return true ;
	}

}

?>