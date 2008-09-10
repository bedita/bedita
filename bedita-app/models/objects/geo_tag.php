<?php
class GeoTag extends BEAppModel 
{
	var $recursive = 0 ;
	
	// TODO: non va.... perche'???
//	var $validate = array(
//		'latitude' 		=> array(array('rule' => 'numeric', 'required' => false)),
//		'longitude' 	=> array(array('rule' => 'numeric', 'required' => false)),
//		'address' 		=> array(array('rule' => 'alphaNumeric', 'allowEmpty' => false, 'required' => true))
//		'gmaps_lookat' 	=> array(array('rule' => 'alphaNumeric', 'required' => false))
//	) ;

	function beforeValidate() {
		if(isset($this->data[$this->name])) 
			$data = &$this->data[$this->name] ;
		else 
			$data = &$this->data ;
		
	 	if (!empty($data['latitude'])) {
			$data['latitude'] = $this->geoConvert($data['latitude']);
	 	}
	 	if (!empty($data['longitude'])) {
			$data['longitude'] = $this->geoConvert($data['longitude']);
	 	}
	 	return true;
	}
	
	// TODO: convert geo coordinates if necessary....es http://www.phpclasses.org/browse/file/10671.html
	private function geoConvert($str) {
		return $str;
	}
}
?>