<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * BeSystem class handle BEdita system utility methods
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
	class BeSystem {

		public function checkAppDirPresence($dirPath) {
			return (is_dir($dirPath));
		}

		public function checkAppFilePresence($filePath) {
			return file_exists($filePath);
		}

		public function checkAppDirPerms($dirPath,$mask = 511) {
			return sprintf("%o",(fileperms($dirPath) & $mask));
		}

		public function createFileFromSample($filePath) {
			$sampleFile = $filePath.'.sample';
			return @copy($sampleFile, $filePath);
		}

		public function checkWritable($filename) {
			return is_writable($filename);
		}
	}
?>