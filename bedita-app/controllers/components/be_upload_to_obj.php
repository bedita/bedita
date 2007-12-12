<?
/**
 * Il componente estende:
 * 
 * SwfUploadComponent - A CakePHP Component to use with SWFUpload
 * Copyright (C) 2006-2007 James Revillini <james at revillini dot com>
 * 
 * Per poter salvare in un oggetto di BEDita il file uploadato.
 * 
 * L'estensione riguarda:
 * - Creazione di un oggetto con il file uploadato
 * 		L'ogetto viene creato con il titolo definito dal filename
 * - Ritorno di codici d'errore html : 5XX
 *   Uno per ogni tipologia d'errore
 * - Aggiunta dei seguenti errori:
 * 	 	file gia' presente
 *   	MIME type non corretto
 * 		salvatggio oggetto
 * - aggiunge la possibilita' di cancellare gli oggetti creati (annulla) 
 * 
 * Dati passati via _FILES in $this->params['Filedata']:
 * name			nome del file
 * tmp_name		nome del file temporaneo dove ci sono i dati
 * error		eventuale errore di upload
 * size			dimensione del file scaricato
 * 
 * via _POST viene passata anche l'informazione:
 * override		true se e' possibile sovrascrivere un file con lo stesso nome gia' presente
 * 
 * 
 * Torna i seguenti codici d'errore, 500 +:
 * 	UPLOAD_ERR_INI_SIZE		1
 * 	UPLOAD_ERR_FORM_SIZE	2
 * 	UPLOAD_ERR_PARTIAL		3
 * 	UPLOAD_ERR_NO_FILE		4
 * 	UPLOAD_ERR_NO_TMP_DIR	6
 * 	UPLOAD_ERR_CANT_WRITE	7
 * 	UPLOAD_ERR_EXTENSION	8
 *  
 *  BEDITA_FILE_EXISIST		30		File gia' presente		
 * 	BEDITA_MIME				31		MIME type non riconosciuto o non accettato
 * 	BEDITA_SAVE_STREAM		32		Errore creazione oggetto
 * 	BEDITA_DELETE_STREAM	33		Errore cancellazione oggetto
 * 
 */

class BeUploadToObjComponent extends SwfUploadComponent {
	var $components	= array('BeFileHandler') ;
	
 	const BEDITA_FILE_EXISIST	= 30 ;
 	const BEDITA_MIME			= 31 ;
 	const BEDITA_SAVE_STREAM 	= 32;
 	const BEDITA_DELETE_STREAM 	= 33	;

 	/**
	 * Contructor function
	 * @param Object &$controller pointer to calling controller
	 */
	function startup(&$controller) {
		//keep tabs on mr. controller's params
		$this->params = $controller->params;
		$this->BeFileHandler->startup($controller) ;
	}

	/**
	 * Uploads a file to location.
	 * NOTA.
	 * FLASH torna come MIME type : application/octect-stream da verificare quello effettivo da estensione
	 * o magic file.
	 * @return boolean true if upload was successful, false otherwise.
	 */
	function upload() {
		$result = false ;
		
		if(!$this->validate()) {
			$this->errorCode = 500 + $this->params['form']['Filedata']['error'] ;
			
			return $result ;
		}
		
		// Scrive l'oggetto
		$data = &$this->params['form']['Filedata'] ;
		$override = (isset($this->params['form']['override'])) ? ((boolean)$this->params['form']['override']) : false ;
		$data['title']	= $data['name'] ;
		$data['path']	= $data['tmp_name'] ;
		
		unset($data['tmp_name']) ;
		unset($data['error']) ;
		
		// FLASH come MIME type torna sempre application/octect-stream		
		if($data['type'] == "application/octet-stream") { 
			$old  = $data['type'] ;
			unset($data['type']) ;
			
			$this->BeFileHandler->getInfoURL($data['path'], $data) ;
		}
		
		try {
			$result = $this->BeFileHandler->save($data) ;
		
		} catch (BEditaFileExistException $e) {			
			if($override) {
				// Non crea un oggetto nuovo ma modifica quello esistente aggiornandone i dati
				if(!($id = $this->BeFileHandler->isPresent($this->params['form']['Filedata']['path']))) return false ;
				$this->params['form']['Filedata']['id'] = $id ;
				
				try {
					$this->BeFileHandler->save($data) ;
				} catch (BEditaSaveStreamObjException $e) {
					$this->errorCode = 500 + self::BEDITA_SAVE_STREAM ;
					throw $e ;
				}
			} else {
				$this->errorCode = 500 + self::BEDITA_FILE_EXISIST ;
				throw $e ;
			}
			
		} catch (BEditaMIMEException $e) {
				$this->errorCode = 500 + self::BEDITA_MIME ;
				throw $e ;
		
		} catch (BEditaInfoException $e) {
				$this->errorCode = 500 + self::BEDITA_MIME ;
				throw $e ;
		
		} catch (BEditaSaveStreamObjException $e) {
				$this->errorCode = 500 + self::BEDITA_SAVE_STREAM ;
				throw $e ;
		} catch (Exception $e) {
			$this->errorCode = 500 ;
			throw $e ;
		}
		
		return ($result);
	}	
} ;
?>