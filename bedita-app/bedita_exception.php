<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Bedita exceptions definitions, loaded from bootstrap.php
 *
 */
class BeditaException extends Exception {

	public $result;

	public $httpCode = null;

	protected $errorDetails; // details for log file

    /**
     * the reason why the Exception was thrown
     *
     * @var string
     */
    protected $cause = null;

	const ERROR = 'ERROR';

	public function __construct($message = NULL, $details = NULL, $res  = self::ERROR, $code = 0) {
   		if(empty($message)) {
   			$message = __("Unexpected error, operation failed",true);
   		}
   		$this->errorDetails = $message;
   		if (!empty($details)) {
   			if (is_array($details)) {
   				foreach ($details as $k => $v) {
                    if ($k == 'cause') {
                        $this->cause = $v;
                    }
   					if (is_array($v)) {
   						$this->errorDetails .= " - [$k] : array(" . implode(", ", $v) . ")";
   					} else {
						$this->errorDetails .= " - [$k] : $v";
					}
				}
   			} else {
   				$this->errorDetails = $this->errorDetails . " - ".$details; 
   			}
   		}
   		$this->result = $res;
   		if($code != 0) {
   			$this->httpCode = $code;
   		}
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

    /**
     * Return httpCode for exception (null, if not set)
     * 
     * @return number
     */
    public function getHttpCode() {
        return $this->httpCode;
    }

    /**
     * Handling http status code header, according to $this->httpCode (if not set or code not handled, return null)
     * Http codes handled: 401, 403, 404, 500
     * 
     * @return string|null
     */
    public function getHeader() {
    	if ($this->getHttpCode() == 401) {
    		return 'HTTP/1.1 401 Unauthorized';
    	} elseif ($this->getHttpCode() == 403) {
    		return 'HTTP/1.1 403 Forbidden';
    	} elseif ($this->getHttpCode() == 404) {
    		return 'HTTP/1.1 404 Not Found';
    	} elseif ($this->getHttpCode() == 500) {
    		return 'HTTP/1.1 500 Internal Server Error';
        }  elseif ($this->getHttpCode() == 503) {
            return 'HTTP/1.1 503 Service Unavailable';
    	} // TODO: handle more http status codes...
    	return null;
    }

    /**
     * Return the reason why the Exception was thrown
     *
     * @return string|null
     */
    public function getCause() {
        return $this->cause;
    }
}

/**
 * Runtime exception (default http status code 500)
 */
class BeditaRuntimeException extends BeditaException {
	public function __construct($message, $details = NULL, $res = self::ERROR, $code = 500) {
		parent::__construct($message, $details, $res, $code);
	}
}
/**
 * 
 */
class BeditaAjaxException extends BeditaException
{
	private $outputType = "html";

	private $headers = null;
	
	public function __construct($message = NULL, $details = NULL, $res  = self::ERROR, $code = 0) {
		if (!empty($details["output"])) {
			$this->outputType = $details["output"];
		}
		if (!empty($details["headers"])) {
			$this->headers = $details["headers"];
		}
		parent::__construct($message,$details,$res,$code);
	}
	
	public function getOutputType() {
		return $this->outputType;
	}

	public function getHeaders() {
		return $this->headers;
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
	protected $object_id;

	public function __construct($message, $details = NULL, $res  = self::ERROR, $code = 0) {
		$this->object_id = (!empty($details['id'])) ? $details['id'] : null;
		parent::__construct($message, $details, $res, $code);
	}
	
	public function getObjectId() {
		return $this->object_id;
	}
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

/**
 * BeditaHash specific Exception
 */
class BeditaHashException extends BeditaException
{
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
	private $headers = null;
	
	public function __construct($message = NULL, $details = NULL, $res  = self::ERROR, $code = 0) {
		if (!empty($details["errorType"])) {
			$this->errorType = $details["errorType"];
		}
		if (!empty($details["headers"])) {
			$this->headers = $details["headers"];
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

	public function getHeaders() {
		return $this->headers;
	}
}

/**
 * Exception for http status code 401 Unauthorized
 */
class BeditaUnauthorizedException extends BeditaException {
	public function __construct($message, $details = NULL, $res = self::ERROR, $code = 401) {
		parent::__construct($message, $details, $res, $code);
	}
}

/**
 * Exception for http status code 403 Forbidden
 */
class BeditaForbiddenException extends BeditaException {
	public function __construct($message, $details = NULL, $res = self::ERROR, $code = 403) {
		parent::__construct($message, $details, $res, $code);
	}
}

/**
 * Exception for http status code 404 Not Found
 */
class BeditaNotFoundException extends BeditaException {
	public function __construct($message, $details = NULL, $res = self::ERROR, $code = 404) {
		parent::__construct($message, $details, $res, $code);
	}
}

/**
 * Exception for http status code 503 Service Unavailable
 */
class BeditaServiceUnavailableException extends BeditaException {

    public function __construct($message, $details = NULL, $res = self::ERROR, $code = 503) {
        parent::__construct($message, $details, $res, $code);
    }

}
