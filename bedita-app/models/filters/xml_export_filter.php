<?php

class XmlExportFilter extends BEAppModel 
{
	var $useTable = false;

	/**
	 * Export objects in XML format
	 * 
	 * @param unknown_type $objects
	 * @param array $meta, export options
	 * @return array containing
	 * 	"content" - export content
	 *  "contentType" - content mime type
	 *  "size" - content length
	 */
	function export(&$objects, array $options = array()) {
		$res = array();
		$xmlOptions = array('attributes' => false, 'format' => 'attributes', 'header' => false);
		$out["Bedita"]["Objects"] = $objects;
		App::import("Core", "Xml");
		$xml =& new Xml($out, $xmlOptions);
		$xmlOut = $xml->toString();
		$res["content"] = $xmlOut;
		$res["size"] = strlen($xmlOut);
		$res["contentType"] = "text/xml";
		return $res;
	}
};
