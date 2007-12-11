<?
/**
 * @author giangi@qwerg.com
 * 
 * Componente per la gestione dell'upload dei file, salvataggio, modifica, delete
 * e interfaccia ai file remoti.
 * I file vanno manipolati utilizzando il componente Transaction....
 * 
 * Dati da passare per salvare/modificare un oggetto co n un file:
 * 
 * 		path		Indica dove il file temporaneo con i dati
 * 					o l'URL dove risiede il file.
 * 		name		Nome del file originale
 * 		type		MIME type, se assente cerca di ricavarlo dal nome file o dall'intestazione (@todo)
 * 		size		Dimensione del file se un URL tenta di leggerla da remoto 
 *  
 * Se le operazioni di salvataggio e cancellazione vanno fate utilizzando questo componente:
 * - Gestisce i file in modo transazionale (modifiche definitive con un $Transaction->commit() )
 * - Esegue il controllo di tipo (MIME) e crea un oggetto di tipo corretto
 * - Per gli URL esegue un controllo (regex) sull'URL
 * - torna in modo corretto e trasparente l'URL al file
 * - Torna le seguenti eccezioni:
 * 		BEditaFileExistException		// File gia' presente nel sistema sistema - nella creazione
 * 		BEditaInfoException				// Informazioni del file non accessibili
 * 		BEditaMIMEException				// MIME type del file non trovato o non corrispondente al tipo di obj
 * 		BEditaURLRxception				// Violazione regole dell'URL
 * 		BEditaSaveStreamObjException	// Errore creazione/ modifica oggetto 
 * 		BEditaDeleteStreamObjException	// Errore cancellazione obj
 * 
 * Se paranoid == false. Non tenta di prelevare le informazioni da remoto e quindi non serve
 * 'allow_php_fopen'. Le informazioni di MIME devono essere passate con i dati per gli URL.
 * 
 * I path dei file salvati in locale, sono registrati in DB relativi alla costante MEDIA_ROOT
 * 
 */
class BeFileHandlerComponent extends Object {

	var $uses 		= array('BEObject', 'Stream', 'BEFile', 'Image', 'AudioVideo') ;
	var $components = array('Transaction');
	var $paranoid 	= true ;
	
	/**
	 * Contiene gli errori di salvataggio del modulo
	 *
	 * @var unknown_type
	 */
	var $validateErrors = false ;
	
	function __construct() {
		foreach ($this->uses as $model) {
			if(!class_exists($model)) loadModel($model) ;
			$this->{$model} = new $model() ;
		}
				
		foreach ($this->components as $component) {
			if(isset($this->{$component}))	continue;
			
			$className = $component . 'Component' ;
			if(!class_exists($className)) loadComponent($component);
			$this->{$component} = new $className() ;
		}
	} 
	
	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$conf = Configure::getInstance() ;
		
		$this->controller 	= $controller;
		
		if(isset($conf->validate_resorce['paranoid'])) $this->paranoid  = (boolean) $conf->validate_resorce['paranoid'] ;
	}

	/**
	 * Salva l'oggetto indicato.
	 * Se presente l'id, modifica altrimenti crea. 
	 * Se il file da inserire con un nuovo oggetto  gia' presente torna un'eccezione, accettato 
	 * se sostituisce se stesso.
	 * I dati del file sono:
	 * 	path	o un path locale o un URL (\.+//:\.+), in questo caso indica un file remoto
	 * 			la sua accettazione dipende dalla variabile "allow_url_fopen" e attivata
	 * name		Nome del file originale. Assente se path == URL
	 * type		MIME type. Puo' essere assente se path == URL
	 * size		Dimensione del file. Puo' essere assente se path == URL
	 *
	 * @param array $dati	dati dell'oggetto
	 * @param string $model	Se presente crea l'oggetto di tipo specifico
	 * 						altrimenti usa il MIME type
	 * 
	 * @return integer o false l'id dell'oggetto creato o salvato
	 */
	function save(&$dati, $model = null) {
		if(isset($dati['id']) && !empty($dati['id'])) {
			// Modifica
			if(!isset($dati['path']) || @empty($dati['path'])) {
				return $this->_modify($dati['id'], $dati) ;
				
			} else if($this->_isURL($dati['path'])) {
				return $this->_modifyFromURL($dati['id'], $dati) ;
				
			} else {
				return $this->_modifyFromFile($dati['id'], $dati) ;
			}
		} else {
			// Creazione
			if($this->_isURL($dati['path'])) {
				return $this->_createFromURL($dati, $model) ;	
			} else {
				return $this->_createFromFile($dati, $model) ;
			}
		}
	}	

	/**
	 * Cancella l'oggetto indicato
	 *
	 * @param integer $id	ID dell'oggetto
	 */
	function del($id) {
		if(!($path = $this->Stream->read("path", $id))) return true ;
		$path = (isset($path['Stream']['path']))?$path['Stream']['path']:$path ;
		
		// Se il path punta ad un file su file locale, cancella
		if(!$this->_isURL($path)) {
			if(!$this->Transaction->rm(MEDIA_ROOT.$path)) return false ;
		}
		
		$model = $this->BEObject->getType($id) ;
		if(!class_exists($model)) {
			loadModel($model) ;
		}
		$mod = new $model() ;
		
	 	if(!$mod->del($id)) {
			throw new BEditaDeleteStreamObjException() ;	
	 	}
	 	
	 	return true ;
	}
	
	/**
	 * Torna l'URL al file dell'oggetto
	 *
	 * @param integer $id		ID dell'oggetto
	 */
	function url($id) {
		if(!($ret = $this->Stream->read("path", $id))) return false ;
		$path = $ret['Stream']['path'] ;
		
		// Se il path punta ad un file su file locale, cancella
		if($this->_isURL($path)) {
			return $path ;
		} else {
			return (MEDIA_URL.$path) ;
		}
	}
	
	/**
	 * Torna il path completo dell'oggetto se e' un ifle remoto
	 * tonra l'URL
	 *
	 * @param integer $id		ID dell'oggetto
	 */
	function path($id) {
		if(!($ret = $this->Stream->read("path", $id))) return false ;
		$path = $ret['Stream']['path'] ;
		
		// Se il path punta ad un file su file locale, cancella
		if($this->_isURL($path)) {
			return $path ;
		} else {
			return (MEDIA_ROOT.$path) ;
		}
	}

	/**
	 * Torna l'id dell'oggetto che contiene il file passato, se presente
	 *
	 * @param string $path	Nome del file o URL
	 * 
	 * @todo DA VERIFICARE
	 */
	function isPresent($path) {
		if(!$this->_isURL($path)) {
			$path = $this->_getPathTargetFile($path);
		}
		
		$clausoles = array() ;
		$clausoles[] = array("path" => trim($path)) ;
		if(isset($id)) $clausoles[] = array("id " => "not {$id}") ;
		
		$ret = $this->Stream->find($clausoles, 'id') ;
		if(!count($ret)) return false ;
		
		return $ret[0]['Stream']['id'] ;
	}
	
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	
	private function _createFromURL(&$dati, $model = null) {
		if(!isset($dati['path'])) return false ;
		
		// URL accettabile
		if(!$this->_regularURL($dati['path'])) throw new BEditaURLException($this->controller) ;

		if($this->paranoid) {
			// Permesso di usare file remoti
			if(!ini_get('allow_url_fopen')) throw  new BEditaAllowURLException($this->controller) ;
			
			// Preleva MIME type e dimensioni
			if(!$this->_getInfoURL($dati['path'], $dati)) throw new BEditaInfoException($this->controller) ;
		}
			
		// Il file/URL non deve essere presente
		if($this->_isPresent($dati['path'])) throw new BEditaFileExistException($this->controller) ;
		
		return $this->_create($dati, $model) ;
	}

	private function _createFromFile(&$dati, $model = null) {
		if(!isset($dati['path'])) return false ;
		
		// Crea il path dove inserire il file
		$sourcePath = $dati['path'] ;
		$targetPath	= $this->_getPathTargetFile($dati['name']); 
		
		// Il file non deve essere presente
		if($this->_isPresent($targetPath)) throw new BEditaFileExistException($this->controller) ;
		
		// Crea il file
		if(!$this->_putFile($sourcePath, $targetPath)) return false ;
		$dati['path'] = $targetPath ;
		
		// Crea l'oggetto
		return $this->_create($dati, $model) ;
	}

	private function _create(&$dati, $model = null) {
		// Crea
		$model = false ;

		switch($this->_getTypeFromMIME($dati['type'], $model)) {
			case 'BEFile':		$model = 'BEFile' ; break ;
			case 'Image':		$model = 'Image' ; break ;
			case 'AudioVideo':	$model = 'AudioVideo' ; break ;
			default:
				throw new BEditaMIMEException($this->controller) ;
		}
		
		$this->{$model}->id = false ;
		
		if(!($ret = $this->{$model}->save($dati))) {
			$this->validateErrors = $this->{$model}->validateErrors ;
			
			throw new BEditaSaveStreamObjException($this->controller) ;
		}
		
		return ($this->{$model}->{$this->{$model}->primaryKey}) ;
	}
	

	private function _modifyFromURL($id, &$dati) {
		// URL accettabile
		if(!$this->_regularURL($dati['path'])) throw new BEditaURLException($this->controller) ;
			
		if($this->paranoid) {
			// Permesso di usare file remoti
			if(!ini_get('allow_url_fopen')) throw  new BEditaAllowURLException($this->controller) ;
			
			// Preleva MIME type e dimensioni
			if(!$this->_getInfoURL($dati['path'], $dati)) throw new BEditaInfoException($this->controller) ;
		}
	
		// se il file e' presente in un altro oggetto torna un eccezione
		if(!$this->_isPresent($dati['path'], $id)) throw new BEditaFileExistException($this->controller) ;
		
		// Se e' presente un path ad file su file system, cancella
		if(($ret = $this->Stream->read('path', $id) && !$this->_isURL($ret['path']))) {
			$this->_removeFile($ret['path']) ;		
		}
		
		return $this->_modify($id, $dati) ;
	}

	private function _modifyFromFile($id, &$dati) {
		$sourcePath = $dati['path'] ;
		$targetPath	= $this->_getPathTargetFile($dati['name']); 
		
		// se il file e' presente in un altro oggetto torna un eccezione
		if(!$this->_isPresent($targetPath, $id)) throw new BEditaFileExistException($this->controller) ;
		
		// Se e' presente un path ad file su file system, cancella
		if(($ret = $this->Stream->read('path', $id) && !$this->_isURL($ret['path']))) {
			$this->_removeFile($ret['path']) ;		
		}
		
		// Crea il file
		if(!$this->_putFile($sourcePath, $targetPath)) return false ;
		$dati['path'] = $targetPath ;

		return $this->_modify($id, $dati) ;
	}
	
	private function _modify($id, &$dati) {
		$conf 		= Configure::getInstance() ;

		if(isset($dati['type']) && !empty($dati['type'])) {
			// Se il MIME type esiste e non e' == torna errore
			$ret = $this->Stream->read('type', $id) ;
			if(!isset($ret['type']) || empty($ret['type'])) break ;
			
			if($ret['type'] != $dati['type']) throw new BEditaMIMEException($this->controller) ;
		}
		
		// Preleva il tipo di oggetto da salvare e salva
		$rec = $this->BEObject->recursive ;
		$this->BEObject->recursive = -1 ;
		if(!($ret = $this->BEObject->read('object_type_id', $id)))  throw new BEditaMIMEException($this->controller) ;
		$this->BEObject->recursive = $rec ;
		$model = $conf->objectTypeModels[$ret['Object']['object_type_id']] ;
		
		$this->{$model}->id =  $id ;
		if(!($ret = $this->{$model}->save($dati))) {
			throw new BEditaSaveStreamObjException($this->controller) ;
		}
		
		return $ret ;
	}	
	
	/**
	 * Torna TRUE se il path e' un URL
	 *
	 * @param unknown_type $path
	 */
	private function _isURL($path) {
		$conf 		= Configure::getInstance() ;
		
		if(preg_match($conf->validate_resorce['URL'], $path)) return true ;
		else return false ;
	}

	/**
	 * Torna true se l'URL supera le regole definite in configurazione
	 */
	private function _regularURL($URL) {
		$conf 		= Configure::getInstance() ;
		
		foreach ($conf->validate_resorce['allow'] as $reg) {
			if(preg_match($reg, $URL)) return true ;
		}

		return false ;	
	}
			
	/**
	 * Torna il nome del model a cui MIME corrisponde
	 *
	 * @param string $mime	MIME  tyep da cercare 
	 * @param string $model	Se presente verifica se puo' tornare il tipo di oggetto dato
	 */
	private function _getTypeFromMIME($mime, $model = null) {
		$conf 		= Configure::getInstance() ;
		
		if(@empty($mime))	return false ;

		if(isset($model) && isset($conf->validate_resorce['mime'][$model] )) {
			$regs = $conf->validate_resorce['mime'][$model] ;

			foreach ($regs as $reg) {
				if(preg_match($reg, $path)) return $model ;
			}
		} else {
			$models = $conf->validate_resorce['mime'] ;
			foreach ($models as $model => $regs) {
				foreach ($regs as $reg) {
					if(preg_match($reg, $mime)) return $model ;
				}	
			}
		}
			
		return false ;
	}

	function getInfoURL($path, &$dati) {
		return $this->_getInfoURL($path, $dati) ;
	}
	
	/**
	 * Preleva il MIME type e le dimensioni da un URL remoto e il nome del file
	 */
	private function _getInfoURL($path, &$dati) {
		
		if(!(isset($dati['name']) && !empty($dati['name']))) {
			$dati['name']  = basename($path) ;
		}
		
		/**
		 * Preleva il MIME type
		 */
		if(!(isset($dati['type']) && !empty($dati['type']))) {			
			// Cerca tramite l'estensione del path
			$dati['type']= $this->_mimeByFInfo($path) ;
			
			if(!(isset($dati['type']) && !empty($dati['type']))) {
				if(!@empty($dati['name'])) {
					$extension = pathinfo($dati['name'], PATHINFO_EXTENSION);
				} else {
					$extension = pathinfo(parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION);
				}
				if(@empty($extension)) return false ;
				$dati['type']= $this->_mimeByExtension($extension) ;	

			}
			
			if(!(isset($dati['type']) && !empty($dati['type']))) {
				// Cerca tramite implementazione ricerca in magic
				$magic 			= new MimeByMagic() ;
				$dati['type']	= $magic->getMime($path) ;
			}
		}
		
		if(!(isset($dati['size']) && !empty($dati['size']))) {
			// Preleva le dimensioni del file
			if(($info = @stat($path))) {
				$dati['size'] = $info[7] ;
			}
		}
		
		return $dati['type'] ;
	}
	
	/**
	 * Crea target con source (file temporaneo) con l'oggetto transazionale
	 *
	 * @param string $sourcePath
	 * @param string $targetPath
	 */
	private function _putFile($sourcePath, $targetPath) {
		if(@empty($targetPath)) return false ;
		
		// Determina quali directory creare per registrare il file
		$tmp = MEDIA_ROOT . $targetPath ;
		$stack = array() ;
		$dir = dirname($tmp) ;
		
		while($dir != MEDIA_ROOT) {
			if(is_dir($dir)) break ;
			
			array_push($stack, $dir) ;
			
			$dir = dirname($dir) ;
		} 
		unset($dir) ;
		
		// Crea le directory non ancora presenti
		while(($current = array_pop($stack))) {
			if(!$this->Transaction->mkdir($current)) return false ;
		}
		
		return $this->Transaction->makeFromFile($tmp, $sourcePath) ;
	}	

	/**
	 * Cancella un file da file system con l'oggetto transazionale
	 *
	 * @param string $path
	 */
	private function _removeFile($path) {
		$path = MEDIA_ROOT . $path ;
		
		// Cancella
		if(!$this->Transaction->rm($path)) return false ;
		
		// Se la directory contenitore e' vuota, la cancella
		$dir = dirname($path) ;
		while($dir != MEDIA_ROOT) {
			// Verifica che sia vuota
			$vuota = true ;
			if($handle = opendir($dir)) {
			    while (false !== ($file = readdir($handle))) {
        			if ($file != "." && $file != "..") {
        				$vuota = false ;
		            	break ;
		        	}
    			}
    			closedir($handle);				
			}
			
			// Se vuota cancella altrimenti interrompe
			if($vuota) {
				if(!$this->Transaction->rmdir($dir)) return false ;
			}else {
				break ;
			}
			
			$dir = dirname($dir) ;
		} 

		return true ;
	}

	/**
	 * Torna TRUE e' gia' presente ad eccezione dell'oggetto indicato in id
	 *
	 * @param string $path
	 * @param intger $id
	 */
	private function _isPresent($path, $id = null) {
		
		$clausoles = array() ;
		$clausoles[] = array("path" => trim($path)) ;
		if(isset($id)) $clausoles[] = array("id " => "not {$id}") ;
		
		$ret = $this->Stream->find($clausoles, 'id') ;
		
		return ((is_array($ret))?((boolean)count($ret)):false) ;
	}
	
  	private function _mimeByExtension($ext) {
  		$conf 		= Configure::getInstance() ;
		
  		$lines = file($conf->validate_resorce['mime.types']) ;
    	foreach($lines as $line) {
      		if(preg_match('/^([^#]\S+)\s+.*'.$ext.'.*$/',$line,$m)) {
      			return $m[1];
      		}
    	}
    	return false ;
  	}		

  	private function _mimeByFInfo($file) {
  		if(!function_exists("finfo_open")) return false ;
		
  		$conf 	= Configure::getInstance() ;
  		$finfo 	= finfo_open(FILEINFO_MIME, $conf->validate_resorce['magic']); // return mime type alla mimetype extension
		if (!$finfo) return false ;
				
		$mime = finfo_file($finfo, $file);
		finfo_close($finfo);
        
	    return $mime ;
  	}		

  	/**
  	 * Torna il path dove inserire il file uploadato
  	 *
  	 * @param string $name 	Nome del file
  	 */
	function _getPathTargetFile($name)  {
   		$conf 		= Configure::getInstance() ;
		
   		// Determina le directory dove salvare il file
		$md5 = md5($name) ;
		preg_match("/(\w{2,2})(\w{2,2})(\w{2,2})(\w{2,2})/", $md5, $dirs) ;
		array_shift($dirs) ;
		$path =  DS . implode(DS, $dirs) . DS . $name ;
		
		return $path ;
	}
   
} ;


////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Implementa la funzione di cercare il MIME type tramite il magic file dove
 * non e' presente l'estensio FINFO di PHP.
 * 
 * @todo All
 *
 */
class MimeByMagic {
	function __construction() {
		
	}
	
	function getMime($path) {
		
		return "application/octet-stream" ;
	}
} ;

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * 		BEditaAllowURLException		// Non  permesso l'uso di file remoti
 */
class BEditaAllowURLException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(&$controller, $message = "", $code  = 0) {
        // some code
   		$this->controller = $controller;
        $this->controller->setResult(AppController::ERROR);
        
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}

   		// make sure everything is assigned properly
        parent::__construct($message, $code);
    }
} ;

/**
 * 		BEditaFileExistException		// File gia' presente in sistema - nella creazione
 */
class BEditaFileExistException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(&$controller, $message = "", $code  = 0) {
        // some code
   		$this->controller = $controller;
        $this->controller->setResult(AppController::ERROR);
        
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}

   		// make sure everything is assigned properly
        parent::__construct($message, $code);
    }
} ;


/**
 * 		BEditaMIMEException				// MIME type del file non trovato o non corrispondente al tipo di obj
 */
class BEditaMIMEException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(&$controller, $message = "", $code  = 0) {
        // some code
   		$this->controller = $controller;
        $this->controller->setResult(AppController::ERROR);
        
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}

   		// make sure everything is assigned properly
        parent::__construct($message, $code);
    }
}

/**
 * 		BEditaURLException				// Violazione regole dell'URL
 */
class BEditaURLException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(&$controller, $message = "", $code  = 0) {
        // some code
   		$this->controller = $controller;
        $this->controller->setResult(AppController::ERROR);
        
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}

   		// make sure everything is assigned properly
        parent::__construct($message, $code);
    }
} ;

/**
 * 		BEditaInfoException				// Informazioni non accessibili
 */
class BEditaInfoException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(&$controller, $message = "", $code  = 0) {
        // some code
   		$this->controller = $controller;
        $this->controller->setResult(AppController::ERROR);
        
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}

   		// make sure everything is assigned properly
        parent::__construct($message, $code);
    }
} ;

/**
 * 		BEditaDeleteStreamObjException	// Errore cancellazione obj
 */
class BEditaDeleteStreamObjException extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct(&$controller, $message = "", $code  = 0) {
        // some code
   		$this->controller = $controller;
        $this->controller->setResult(AppController::ERROR);
        
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}

   		// make sure everything is assigned properly
        parent::__construct($message, $code);
    }
}

?>