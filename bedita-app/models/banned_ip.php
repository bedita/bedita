<?php
/**
 *
 * @copyright		Copyright (c) 2008 Channelweb Srl, Chialab Srl
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
*/
class BannedIp extends BEAppModel
{
	var $actsAs = array(
			'CompactResult' 		=> array()
	);
	
	public function ban($ipNumber, $status = "ban") {
		$data = array("ip_address" => $ipNumber, "status" => $status);
		$banned = $this->find('first', array(
                	'conditions' => "BannedIp.ip_address='$ipNumber'",
                	'fields' => array('BannedIp.id')));
        if(!empty($banned)) {
        	$data['id'] = $banned['id'];
        }
		
		if(!$this->save($data)) {
	 		throw new BeditaException(__("Error saving IP", true), $this->validationErrors);
	 	}
				
	}
	
	public function isBanned($ipNumber) {
		$banned = $this->find('first', array(
                	'conditions' => array('BannedIp.status' => 'ban', 
                	'BannedIp.ip_address' => $ipNumber), 
                	'fields' => array('BannedIp.id')));
		return !empty($banned);
	}
	
}
?>
