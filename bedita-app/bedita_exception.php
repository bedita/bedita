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
 * Bedita exceptions definitions, loaded from bootstrap.php
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeditaException extends Exception
{
	public $result;
	protected $errorDetails; // details for log file
	const ERROR 	= 'ERROR' ;
	
	public function __construct($message = NULL, $details = NULL, $res  = self::ERROR, $code = 0) {
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}
   		$this->errorDetails = $message;
   		if(!empty($details)) {
   			if(is_array($details)) {
   				foreach ($details as $k => $v) {
					$this->errorDetails .= "; [$k] => $v";
				}
   			} else {
   				$this->errorDetails = $this->errorDetails . ": ".$details; 
   			}
   		}
   		$this->result = $res;
        parent::__construct($message, $code);
    }
    
    public function  getDetails() {
    	return $this->errorDetails;
    }
    
    public function errorTrace() {
        return get_class($this)." - ".$this->getDetails()." \nFile: ". 
            $this->getFile()." - line: ".$this->getLine()." \nTrace:\n".
            $this->getTraceAsString();   
    }
}

class BeditaRuntimeException extends BeditaException 
{	
}
/**
 * 
 */
class BeditaAjaxException extends BeditaException
{
	private $outputType = "html";
	
	public function __construct($message = NULL, $details = NULL, $res  = self::ERROR, $code = 0) {
		if (!empty($details["output"])) {
			$this->outputType = $details["output"];
		}
		parent::__construct($message,$details,$res,$code);
	}
	
	public function getOutputType() {
		return $this->outputType;
	}
}

/**
 * 		BEditaIOException		// Generic I/O Error
 */
class BEditaIOException extends BeditaException
{
} ;

/**
 * 		BEditaAllowURLException		// Remote files not allowed
 */
class BEditaAllowURLException extends BeditaException
{
} ;

/**
 * 		BEditaFileExistException
 */
class BEditaFileExistException extends BeditaException
{
}

/**
 * 		BEditaMIMEException				// MIME type not found or wrong
 */
class BEditaMIMEException extends BeditaException
{
}

/**
 * 		BEditaURLException				// URL rules violation
 */
class BEditaURLException extends BeditaException
{
} ;

/**
 * 		BEditaInfoException				// Information not available
 */
class BEditaInfoException extends BeditaException
{
} ;

class BEditaSaveStreamObjException extends BeditaException
{
} ;

/**
 * 	 BEditaDeleteStreamObjException		// Error removing stream obj
 */
class BEditaDeleteStreamObjException extends BeditaException
{
}

class BEditaMediaProviderException extends BeditaException
{
}

/**
 * 		BEditaUploadPHPException	// handle php upload errors
 */
class BEditaUploadPHPException extends BeditaException
{
	private $phpError = array(
							UPLOAD_ERR_INI_SIZE		=> "The uploaded file exceeds the upload_max_filesize directive in php.ini",
							UPLOAD_ERR_FORM_SIZE	=> "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
							UPLOAD_ERR_PARTIAL		=> "The uploaded file was only partially uploaded",
							UPLOAD_ERR_NO_FILE		=> "No file was uploaded",
							UPLOAD_ERR_NO_TMP_DIR	=> "Missing a temporary folder",
							UPLOAD_ERR_CANT_WRITE	=> "Failed to write file to disk",
							UPLOAD_ERR_EXTENSION	=> "File upload stopped by extension"
							); 
	
	public function __construct($numberError, $details = NULL, $res  = self::ERROR, $code = 0) {
		parent::__construct($this->phpError[$numberError], $details, $res, $code);
	}
}

/**
 * BeditaMailException
 *
 */
class BeditaMailException extends BeditaException
{
	private $smtp_error = "";

	public function __construct($message = NULL, $smtp_err = "") {
		$this->smtp_error = $smtp_err;
		parent::__construct($message,$smtp_err);
	}
	
	public function getSmtpError() {
		return $this->smtp_error;
	}
}

/** ###########################
 *	FRONTEND specific Exception 
 */

/**
 * BeditaPublication specific Exception
 */
class BeditaPublicationException extends BeditaException {
	private $layout = "draft";

	public function __construct($message = NULL, $details = NULL, $res  = self::ERROR, $code = 0) {
		if (!empty($details["layout"])) {
			$this->layout = $details["layout"];
		}
		parent::__construct($message,$details,$res,$code);
	}
	
	public function getLayout() {
		return $this->layout;
	}
}

class BeditaFrontAccessException extends BeditaException {
	
	private $errorType;
	
	public function __construct($message = NULL, $details = NULL, $res  = self::ERROR, $code = 0) {
		if (!empty($details["errorType"])) {
			$this->errorType = $details["errorType"];
		}
		
		if (empty($message)) {
			if ($this->errorType == "unlogged")
				$messages = __("You have to be logged to access to this item",true);
			elseif ($this->errorType == "unauthorized")
				$messages = __("You aren't authorized to access to this item",true);
		}
		
		parent::__construct($message,$details,$res,$code);
	}
	
	public function getErrorType() {
		return $this->errorType;
	}
	
}
?>