<?php
class GeoTag extends BEAppModel 
{
	var $recursive = 0 ;
	
	function beforeValidate() {

		$this->checkFloat('latitude');
		$this->checkFloat('longitude');

		return true;
	}
	
	// TODO: convert geo coordinates if necessary....es http://www.phpclasses.org/browse/file/10671.html
	private function geoConvert($str) {
		return $str;
	}
}
?>