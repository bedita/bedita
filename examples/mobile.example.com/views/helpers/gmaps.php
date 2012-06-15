<?php

class GmapsHelper extends AppHelper {

	public $helpers = array("Html");

	protected $staticUrl = "http://maps.googleapis.com/maps/api/staticmap?";

	public function staticMap($object, $params = array()) {
		if (empty($object["GeoTag"][0]["latitude"]) || empty($object["GeoTag"][0]["longitude"])) {
			return;
		}
		
		$View = ClassRegistry::getObject("View");
		$pipe = urlencode("|");
		$url = "size=900x400";
		$url .= "&sensor=false";
		$url .= "&maptype=satellite";
		$url .= "&markers=icon:" . $View->viewVars["publication"]["public_url"] . "/img/marker-adriatica.png" .
				$pipe . $object["GeoTag"][0]["latitude"] . "," . $object["GeoTag"][0]["longitude"];
		if (!empty($object["GeoTag"][0]["gmaps_lookat"]["zoom"])) {
			$url .= "&zoom=" . $object["GeoTag"][0]["gmaps_lookat"]["zoom"];
		}

		$url = $this->staticUrl . $url;

		return $this->Html->image($url, array("width" => "100%"));

	}

}

?>