<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
/**
 * Cleanup task
 */
class CleanupTask extends BeditaBaseShell {

   public function execute() {
		$basePath = TMP;
    	if (isset($this->params['frontend'])) {
    		$basePath = $this->params['frontend'].DS."tmp".DS;
			if(!file_exists($basePath)) {
    			$this->out("Directory $basePath not found");
				return;
			}
    		$this->out('Cleaning dir: '.$basePath);
            $this->__clean($basePath . 'cache', false);
    	}
        if (isset($this->params['logs'])) {
    	   $this->__clean($basePath . 'logs');
            $this->out('Logs cleaned.');
        }
        Cache::clear();
        $this->__clean($basePath . 'cache' . DS . 'models');
        $this->__clean($basePath . 'cache' . DS . 'persistent');        
        $this->__clean($basePath . 'cache' . DS . 'views');        
        $this->out('Cache cleaned.');
        $this->__clean($basePath . 'smarty' . DS . 'compile');
        $this->__clean($basePath . 'smarty' . DS . 'cache');
        $this->out('Smarty compiled/cache cleaned.');

        if (isset($this->params['media'])) {
       		$this->removeMediaFiles();
        }
    }    

    private function removeMediaFiles() {
		$mediaRoot = Configure::read("mediaRoot");
		$folder= new Folder($mediaRoot);
        $dirs = $folder->ls();
        foreach ($dirs[0] as $d) {
            $folder->delete($mediaRoot . DS. $d);
        }
        $this->out('Media files cleaned.');
    	
    }

}
?>
