<?php

class XmlOutFilterComponent extends Object {
	var $controller	;

	function startup(&$controller) {
		$this->controller = $controller;
	}
	
	function writeData(&$objects, array $meta = array()) {
		
		$options = array('attributes' => false, 'format' => 'attributes', 'header' => false);
		$out["Bedita"]["Objects"] = $objects;
		App::import("Core", "Xml");
		$xml =& new Xml($out, $options);
		$xmlOut = $xml->toString();
		
		$fileName = !empty($meta["filename"]) ? $meta["filename"] : "bedita_export.xml";
		Configure::write('debug', 0);
		// use readfile
		// TODO: optimizations! use X-Sendfile ? 
		header('Content-Description: File Transfer');
		header("Content-type: text/xml; charset=utf-8");
		header('Content-Disposition: attachment; filename='.$fileName);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . sizeof($xmlOut));
		ob_clean();
   		flush();
		echo $xmlOut;
		exit();
	}
};
