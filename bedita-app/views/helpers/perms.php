<?php
/**
 * Perms helper library.
 * 
 * BEDITA_PERMS_READ",			0x1
 * BEDITA_PERMS_MODIFY",		0x2
 * BEDITA_PERMS_DELETE",		0x4
 * BEDITA_PERMS_CREATE",		0x8
 * BEDITA_PERMS_READ_MODIFY",	BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY
 */
class PermsHelper extends Helper {

	public function isReadable($user,$groups,$permissions) {
		$conf = Configure::getInstance();
		return $this->checkPerm($user,$groups,$permissions,$conf->BEDITA_PERMS_READ);
	}

	public function isWritable($user,$groups,$permissions) {
		$conf = Configure::getInstance();
		return $this->checkPerm($user,$groups,$permissions,$conf->BEDITA_PERMS_MODIFY);
	}

	public function isDeletable($user,$groups,$permissions) {
		$conf = Configure::getInstance();
		return $this->checkPerm($user,$groups,$permissions,$conf->BEDITA_PERMS_DELETE);
	}

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