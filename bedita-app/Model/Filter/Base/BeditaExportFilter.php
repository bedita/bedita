<?php

App::uses('BEAppModel', 'Model');

/**
 * Base model for export filters.
 */
abstract class BeditaExportFilter extends BEAppModel {

	public $useTable = false;

	protected $typeName = "";
	protected $mimeTypes = array();

	/**
	 * Export objects in XML format
	 *
	 * @param array $objects, object to export array
	 * @param array $options, export options
	 * @return array containing
	 * 	"content" - export content
	 *  "contentType" - content mime type
	 *  "size" - content length
	 * @throws BeditaException
	 */
	public function export(array &$objects, array $options = array()) {
		throw new BeditaException(__("Missing method"));
	}

	/**
	 * Supported mime types
	 *
	 * @return array , result array containing supported mime types in the form
	 * 	"xml" => "text/xml", "zip" => "application/zip",....
	 */
	public function mimeTypes() {
		return $this->mimeTypes;
	}

	/**
	 * Filter logical name
	 */
	public function name() {
		return $this->typeName;
	}

	/**
	 * Validate resource (after export)
	 */
	public function validate($resource, array $options = array()){
		return __("No 'validate' method found");
	}

}

?>