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
 * Base class for bedita shell scripts: provides common filesystem related methods.
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeditaBaseShell extends Shell {

	protected function check_sys_get_temp_dir() {
		if ( !function_exists('sys_get_temp_dir') ) {
		    // Based on http://www.phpit.net/
		    // article/creating-zip-tar-archives-dynamically-php/2/
		    function sys_get_temp_dir()
		    {
		        // Try to get from environment variable
		        if ( !empty($_ENV['TMP']) )
		        {
		            return realpath( $_ENV['TMP'] );
		        }
		        else if ( !empty($_ENV['TMPDIR']) )
		        {
		            return realpath( $_ENV['TMPDIR'] );
		        }
		        else if ( !empty($_ENV['TEMP']) )
		        {
		            return realpath( $_ENV['TEMP'] );
		        }
		
		        // Detect by creating a temporary file
		        else
		        {
		            // Try to use system's temporary directory
		            // as random name shouldn't exist
		            $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
		            if ( $temp_file )
		            {
		                $temp_dir = realpath( dirname($temp_file) );
		                unlink( $temp_file );
		                return $temp_dir;
		            }
		            else
		            {
		                return FALSE;
		            }
		        }
		    }
		}
	}

    protected function setupTempDir() {
    	$basePath = sys_get_temp_dir().DS."bedita-shell-tmp".DS;
		if(!is_dir($basePath)) {
			if(!mkdir($basePath))
				throw new Exception("Error creating temp dir: ".$basePath);
		} else {
    		$this->__clean($basePath);
		}
    	return $basePath;
    }

    protected function cleanTempDir() {
    	$exportPath = sys_get_temp_dir().DS."bedita-shell-tmp".DS;
    	$folder= new Folder();
    	if(!$folder->delete($exportPath)) {
			throw new Exception("Error deleting dir $exportPath");
        }
    }
	
    /**
     * Read mandatory shell argument ($opt), 
     * exit with err message if parameter not present
     *
     * @param unknown_type $opt
     * @param unknown_type $errMsg
     */
    protected function mandatoryArgument($opt, $errMsg) {
    	if (isset($this->params[$opt])) {
            return $this->params[$opt];
    	} else {
    		$this->error("Missing parameters" , $errMsg);
    	}
    }
   
    public function test() {
		pr($this->params);
		pr($this->args);
    }

    protected function checkExportFile($expFile) {
    	if(file_exists($expFile)) {
			$res = $this->in("$expFile exists, overwrite? [y/n]");
			if($res == "y") {
				if(!unlink($expFile)){
					throw new Exception("Error deleting $expFile");
				}
			} else {
				$this->out("Export aborted. Bye.");
				exit;
			}
		}
    }

    protected function __clean($path, $removeDirs=true) {
        
        $folder = new Folder($path);
        $list = $folder->read();

		if($removeDirs) {
	        foreach ($list[0] as $d) {
	        	if($d[0] != '.') { // don't delete hidden dirs (.svn,...)
		        	if(!$folder->delete($folder->path.DS.$d)) {
		                throw new Exception("Error deleting dir $d");
		            }
	        	}
	        }
		}
        foreach ($list[1] as $f) {
        	$file = new File($folder->path.DS.$f);
        	if(!$file->delete()) {
                throw new Exception("Error deleting file $f");
            }
        }
        return ;
    }    
     
}

?>
