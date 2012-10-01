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
 * Object permission helper
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 * 
 * BEDITA_PERMS_READ,			0x1
 * BEDITA_PERMS_MODIFY,			0x2
 * BEDITA_PERMS_READ_MODIFY,	BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY
 */
class PermsHelper extends AppHelper {

	/**
	 * check whether user has read perms
	 * 
	 * @param string $user
	 * @param array $groups
	 * @param array $permissions
	 * @return boolean
	 */
	public function isReadable($user,$groups,$permissions) {
		$conf = Configure::getInstance();
		return $this->checkPerm($user,$groups,$permissions,$conf->BEDITA_PERMS_READ);
	}

	/**
	 * check whether user has write perms
	 * 
	 * @param string $user
	 * @param array $groups
	 * @param array $permissions
	 * @return boolean
	 */
	public function isWritable($user,$groups,$permissions) {
		$conf = Configure::getInstance();
		return $this->checkPerm($user,$groups,$permissions,$conf->BEDITA_PERMS_MODIFY);
	}

	/**
	 * check user perms with bitwise AND operator.
	 * perms are defined in bedita-app/config/bedita.ini.php
	 * 
	 * @param string $u user
	 * @param array $g_arr
	 * @param array $p_arr
	 * @param number $p
	 */
	private function checkPerm($u,$g_arr,$p_arr,$p) {
		if(empty($p_arr))
			return true;
		$res = false;
		foreach($p_arr as $k => $v) {
			if($v['switch']=='user' && $v['name']==$u) {
				if($v['flag'] & $p) {
					$res = true;
				}
			} else {
				if(!empty($g_arr)) {
					foreach($g_arr as $key => $gname) {
						if($v['switch']=='group' && $v['name']==$gname) {
							if($v['flag'] & $p) {
								$res = true;
							}
						}
					}
				}
			}
		}
		return $res ;
	}
}

?>