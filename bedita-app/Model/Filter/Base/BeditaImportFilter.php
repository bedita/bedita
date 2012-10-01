<?php

App::uses('BEAppModel', 'Model');

/**
 * Base model for import filters.
 */
abstract class BeditaImportFilter extends BEAppModel {

	public $useTable = false;

	protected $typeName = "";
	protected $mimeTypes = array();

	/**
	 * Import BE objects from XML source string
	 *
	 * @param string $sourcePath, path to source to import, e.g. path to local files, urls...
	 * @param array $options, import options: "sectionId" => import objects in this section
	 * @return array , result array containing
	 * 	"objects" => number of imported objects
	 *  "message" => generic message (optional)
	 *  "error" => error message (optional)
	 * @throws BeditaException
	 */
	public function import($sourcePath, array $options = array())  {
		throw new BeditaException(__("Missing method"));
	}

	/**
	 * Supported mime types
	 *
	 * @return array , array of supported mime types like
	 * 	"text/xml", "application/xml"
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

};

?>