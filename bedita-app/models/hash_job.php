<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * Hash asynchronous job object
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class HashJob extends BEAppModel {

	private $hashString = "abcdefghijklmnopqrstuvwxyz";
	
	public function beforeSave() {
		if (empty($this->data["HashJob"]) && empty($this->data)) {
			return false;
		} elseif (empty($this->data["HashJob"]) && !empty($this->data)) {
			$this->data["HashJob"] = $this->data;
		}
		
		if (empty($this->data["HashJob"]["hash"])) {
			$this->data["HashJob"]["hash"] = $this->generateHash();
		}
		
		if (empty($this->data["HashJob"]["params"])) {
			$columnTypes = $this->getColumnTypes();
			// set params
			$params = array();
			foreach ($this->data["HashJob"] as $key => $val) {
				if (!array_key_exists($key, $columnTypes)) {
					$params[$key] = $val;
				}
			}
			$this->data["HashJob"]["params"] = serialize($params);
		}
		
		return true;
	} 
	
	public function afterFind($results) {
		if (!empty($results)) {
			foreach ($results as $key => $val) {
				if ($val["HashJob"]["expired"] < date("Y-m-d H:i:s", time())) {
					$this->id = $val["HashJob"]["id"];
					$this->saveField("status", "expired");
					$results[$key]["HashJob"]["status"] = "expired";
				}
                if (!empty($val['HashJob']['params'])) {
                    $params = @unserialize($val['HashJob']['params']);
                    if ($params !== false && is_array($params)) {
                        $results[$key]['HashJob'] = array_merge($results[$key]['HashJob'], $params);
                    }
                }
			}
		}
		return $results;
	}
	
	public function generateHash() {
		return md5(str_shuffle($this->hashString) . microtime());
	}

	/**
	 * get hash expiration date
	 *
	 * @param int $hashExpiredTime expiration time in seconds
	 * @return string expiration date in sql date format
	 */
	public function getExpirationDate($hashExpiredTime=null) {
		if (empty($hashExpiredTime)) {
			$hashExpiredTime = Configure::read("hashExpiredTime");
		}
		return date("Y-m-d H:i:s", time() + $hashExpiredTime);
	}
	
}
?>