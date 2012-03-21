<?php

/* -----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2012 ChannelWeb Srl, Chialab Srl
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
 * ------------------------------------------------------------------->8-----
 */

/**
 * XmlExportFilter: class to export objects to XML format
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class XmlExportFilter extends BeditaExportFilter 
{
	protected $typeName = "BE Xml";
	protected $mimeTypes = array("text/xml", "application/xml");
	
	/**
	 * Export objects in XML format
	 * 
	 * @param array $objects
	 * @param array $options, export options
	 * @return array containing
	 * 	"content" - export content
	 *  "contentType" - content mime type
	 *  "size" - content length
	 */
	public function export(array &$objects, array $options = array()) {
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
