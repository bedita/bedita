<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

if (!class_exists('ClassRegistry')) {
	App::import('Core', array('ClassRegistry'));
}

if (!class_exists('File')) {
    App::import('Core', 'File');
}

/**
 * BEdita libs class. Instantiate and put in the registry other classes
 *
 */

class BeLib {

	/**
	 * used to flatten arrays in BeLib::arrayValues()
	 * @var array
	 */
	private $__arrayFlat = array();

	public static function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new BeLib();
		}
		return $instance[0];
	}

	/**
	 * return an instance of a class (by default search in libs dir)
	 * If class is not instantiated do it and put in CakePHP registry
	 *
	 * @param string $name class name (file has to be underscorized MyClass => my_class.php)
	 * @param string or array $paths paths where search class file (search in libs folder by default)
	 * @return class instance
	 */
	public static function &getObject($name, $paths=null) {
		if (!$libObject = ClassRegistry::getObject($name)) {
			if (!class_exists($name)) {
				$file = Inflector::underscore($name) . ".php";
				if (empty($paths)) {
					App::import("Lib", $name);
				} else {
					$paths = (is_array($paths))? $paths : array($paths);
					if (!App::import("File", $name, true, $paths, $file)) {
						$return = false;
						return $return;
					}
				}
			}
			if (class_exists($name)) {
				$libObject = new $name();
				ClassRegistry::addObject($name, $libObject);
			}
		}
		return $libObject;
	}

	/**
     * Return the instance of BeCallbackManager
     *
     * @return BeCallbackManager
     */
    public static function eventManager() {
        static $eventManager = null;
        if (!$eventManager) {
            $eventManager = self::getObject('BeCallbackManager');
        }
        return $eventManager;
    }

	/**
	 * check if a class name is a BEdita object type
	 *
	 * @param string $name the class name
	 * @param mixed $paths array of paths or string path where searching the class
	 * 					   leave empty to use ClassRegistry
	 * @return boolean
	 */
	public function isBeditaObjectType($name, $paths=null) {
		if (!$paths) {
			$classInstance = ClassRegistry::init($name);
		} else {
			$classInstance = $this->getObject($name, $paths);
		}
		if (!$classInstance) {
			return false;
		}
		$parents = class_parents($classInstance);
		if (empty($parents) || !in_array("BEAppObjectModel", $parents)) {
			return false;
		}
		return true;
	}

	/**
	 * check if a file name is already used in Configure::$type."Paths"
	 *
	 * @param string $filename
	 * @param string $type (models, controllers, ...) see App::path
	 * @param array of path to exclude from search (paths have to end with DS trailing slash)
	 * @return boolean
	 */
	public function isFileNameUsed($filename, $type, $excludePaths=array()) {
		$typePaths = App::path($type);
		if (empty($typePaths)) {
			throw new BeditaException(__("No paths to search for " . $type, true));
		}
		$paths = array_diff($typePaths ,$excludePaths);
		$folder = new Folder();
		foreach ($paths as $p) {
			$folder->cd($p);
			$ls = $folder->read(true, true);
			if (!empty($ls[1]) && in_array($filename, $ls[1])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Modify a string to get friendly url version.
	 * With a regexp you can choose which characters to preserve.
	 *
	 *
	 *
	 * @param string $value
	 * @param string $keep, regexp fragment with characters to keep, e.g. "\." will preserve points,
	 * 						"\.\:" points and semicolons
	 * @return string
	 */
	public function friendlyUrlString($value, $keep = "") {
		if (empty($value)) {
			$value = "";
		}
		if (is_numeric($value)) {
			$value = "n" . $value;
		}

		$value = strtolower(htmlentities($value, ENT_NOQUOTES | ENT_IGNORE, "UTF-8"));

		// replace accent, uml, tilde,... with letter after & in html entities
		$value = preg_replace("/&(.)(uml);/", "$1e", $value);
		$value = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $value);
		// replace special chars and space with dash (first decode html entities)
		// exclude chars in $keep regexp fragment
		$regExp = "/[^a-z0-9\-_" . $keep . "]/i";
		$value = preg_replace($regExp, "-", html_entity_decode($value,ENT_NOQUOTES,"UTF-8" ) ) ;
		// replace two or more consecutive dashes with one dash
		$value = preg_replace("/[\-]{2,}/", "-", $value);

		// trim dashes in the beginning and in the end of nickname
		return trim($value,"-");
	}

	/**
	 * Strip scripts, images, whitespace or all together on $data
	 * using Sanitize::stripScripts, Sanitize::stripImages, Sanitize::stripWhitespace, Sanitize::stripAll methods
	 * see Sanitize class of cakephp for more info
	 *
	 * @param mixed $data string or array
	 * @param array $options, possible values are:
	 *				"what" => "scripts" (default), "images", "whitespace", "all",
	 *				"recursive" => true (default) strip recursively on $data
	 *
	 * @return mixed
	 */
	public function stripData($data, array $options = array()) {
		$options = array_merge(array("what" => "scripts", "recursive" => true), $options);
		$method = "strip".ucfirst($options["what"]);
		App::import("Sanitize");

		if (method_exists("Sanitize", $method)) {
			if (is_array($data)) {
				foreach ($data as $key => $value) {
					if (is_array($value) && $options["recursive"]) {
						$data[$key] = $this->stripData($value, $options);
					} else {
						$data[$key] = Sanitize::$method($value);
					}
				}
			} else {
				$data = Sanitize::$method($data);
			}
		}

		return $data;
	}

	/**
	 * Return array with model name and eventually specific type (see $config[validate_resource][mime][Application])
	 * from mime type
	 *
	 * @param string $mime	mime type
	 * @return mixed array|boolean
	 */
	public static function getTypeFromMIME($mime) {
		$conf 		= Configure::getInstance() ;
		if(empty($mime)) {
			return false ;
		}
		$models = $conf->validate_resource['mime'] ;
		foreach ($models as $model => $regs) {
			foreach ($regs as $key => $reg) {
				if (is_array($reg)) {
					foreach ($reg["mime_type"] as $val) {
						if(preg_match($val, $mime))
							return array("name" => $model, "specificType" => $key) ;
					}
				} elseif(preg_match($reg, $mime)) {
					return array("name" => $model) ;
				}
			}
		}
		return false ;
	}




	/**
	 * return values of multidimensional array
	 *
	 * @param array $array
	 * @param boolean $addStringKeys if it's true add string keys to the returned array
	 * @return array
	 */
	public function arrayValues(array $array, $addStringKeys = false) {
		$this->__arrayFlat = array();
		array_walk_recursive($array , array($this, "arrayValuesCallback"), $this);
		if ($addStringKeys) {
			$keys = $this->arrayKeys($array);
			$this->__arrayFlat = array_merge($this->__arrayFlat, $keys);
		}
		return $this->__arrayFlat;
	}

	/**
	 * callback method used from BeLib::arrayValues
	 *
	 * @param mixed $item
	 * @param mixed $key
	 * @param array $values
	 */
	static private function arrayValuesCallback(&$item, $key, $obj) {
		$obj->__arrayFlat[] = $item;
	}

	/**
	 * return keys of multidimensional array
	 *
	 * @param array $ar
	 * @param boolean $stringKeys if it's true add string keys to the returned array
	 * @return array
	 */
	public function arrayKeys(array $ar, $stringKeys = true) {
		$keys = array();
		foreach($ar as $k => $v) {
			if (!$stringKeys || ($stringKeys && is_string($k))) {
				$keys[] = $k;
			}
			if (is_array($ar[$k])) {
				$keys = array_merge($keys, $this->arrayKeys($ar[$k], $stringKeys));
			}
		}
		return $keys;
	}

	/**
	 * Transform any numeric date in SQL date/datetime string format
	 * Date types accepted: "little-endian"/"middle-endian"/"big-endian"
	 *
	 * if little endian, expected format id dd/mm/yyyy format, or dd.mm.yyyy, or dd-mm-yyyy
	 * if middle endian, expected format is mm/dd/yyyy format, or mm.dd.yyyy (USA standard)
	 * if big endian ==> yyyy-mm-dd
	 * Examples:
	 *
	 *  Little endian
	 *  "22/04/98", "22/04/1998", "22.4.1998", "22-4-98", "22 4 98", "1998", "98", "22.04", "22/4", "22 4"
	 *
	 *  Middle endian
	 *  "4/22/98", "02/22/1998", "4.22.1998", "4-22-98", "4/22", "04.22"
	 *
	 * If format is not valid or string is not parsable, an exception maybe thrown
	 *
	 * @param string $val, string in generic numeric form
	 * @param string $dateType, "little-endian"/"middle-endian"/"big-endian"
	 *
	 */
	public function sqlDateFormat($value, $dateType = "little-endian") {
		// check if it's already in SQL format
		$pattern = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$|^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/";
		if (preg_match($pattern, $value)) {
			return $value;
		}
		$pattern = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$|^[0-9]{4}-[0-9]{2}-[0-9]{2}$/";
		if (preg_match($pattern, $value)) {
			return $value;
		}
		$d = false;

		if($dateType === "little-endian") {
			// dd/mm/yyyy - dd.mm.yyy like formats
			$pattern = "/^([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,4})$/";
			$match = array();
			if (preg_match($pattern, $value, $match)) {
				$d = $match[5] . "-" . $match[3] . "-" . $match[1];
			}
			// dd/mm - dd.mm like formats
			if($d === false) {
				$pattern = "/^([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,2})$/";
				$match = array();
				if (preg_match($pattern, $value, $match)) {
					$d = $match[3] . "/" . $match[1];
				}
			}
		} elseif($dateType === "middle-endian") {
			// mm/dd/yyyy - mm.dd.yyyy like formats
			$pattern = "/^([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,4})$/";
			$match = array();
			if (preg_match($pattern, $value, $match)) {
				$d = $match[5] . "-" . $match[1] . "-" . $match[3];
			}
			// dd/mm - dd.mm like formats
			if($d === false) {
				$pattern = "/^([0-9]{1,2})(\/|-|\.|\s)([0-9]{1,2})$/";
				$match = array();
				if (preg_match($pattern, $value, $match)) {
					$d = $match[1] . "/" . $match[3];
				}
			}
		}

		if($d === false) {
			$pattern = "/^([0-9]{4})$/";
			$match = array();
			if (preg_match($pattern, $value, $match)) {
				$d = $match[1] . "-01-01";
			}
		}

		if($d === false) {
			$pattern = "/^([0-9]{1,2})$/";
			$match = array();
			if (preg_match($pattern, $value, $match)) {
				$y = intval($match[1]);
				$date = new DateTime();
				// which year 08, 12, 18, 28 ??? - if earlier than current year add 2000, otherwise add 1900
				$yNow = intval($date->format("Y"));
				$ys = strval($y + ((2000 + $y > $yNow) ? 1900 : 2000));
				$d = $ys . "-01-01";
			}
		}

		if($d === false) {
			$d = $value; // use $value if pattern not recognized
		}
		$date = new DateTime($d);
		return $date->format('Y-m-d');
	}

	/**
	 * return conventional variable/method name starting from nickname
	 * replacing '-' with '_' and camelizing (not first char)
	 *
	 * example: this-is-my-nickname become thisIsMyNickName
	 *
	 * @param string $nickname
	 * @return string
	 */
	public function variableFromNickname($nickname) {
		$variableName = str_replace("-", "_", $nickname); // example: my-nickname => my_nickname
		$variableName = Inflector::variable($variableName); // example => sitemapXml, myNickname
		return $variableName;
	}

    /**
     * generate an array of frontend folders
     *
     * @return array
     */
    public function getFrontendFolders() {
        $sel = array();
        $folder = new Folder(BEDITA_FRONTENDS_PATH);
        $ls = $folder->read();
        foreach ($ls[0] as $dir) {
            if($dir[0] !== '.' ) {
                $sel[] = BEDITA_FRONTENDS_PATH. DS .$dir;
            }
        }
        return $sel;
    }

    /**
     * generate an array of addon folders
     *
     * @return array
     */
    public function getAddonFolders() {
        $sel = array();
        $folder = new Folder(BEDITA_ADDONS_PATH);
        $ls = $folder->read();
        foreach ($ls[0] as $dir) {
            if($dir[0] !== '.' ) {
                $sel[] = BEDITA_ADDONS_PATH. DS .$dir;
            }
        }
        return $sel;
    }

    /**
     * generate an array of plugin module folders
     *
     * @return array
     */
    public function getPluginModuleFolders() {
        $sel = array();
        $folder = new Folder(BEDITA_MODULES_PATH);
        $ls = $folder->read();
        foreach ($ls[0] as $dir) {
            if($dir[0] !== '.' ) {
                $sel[] = BEDITA_MODULES_PATH. DS .$dir;
            }
        }
        return $sel;
    }

    /**
     * update Addons after project update
     *
     * @param string $path
     * @return array
     */
    public function remoteUpdateAddons($path) {
        // update enabled addons
        if (strstr($path, BEDITA_ADDONS_PATH)) {
            $folder = new Folder(BEDITA_ADDONS_PATH);
            $type = trim(substr($path, strlen(BEDITA_ADDONS_PATH)), DS);
            if ($type != "vendors") {
                $Addon = ClassRegistry::init("Addon");
                $enabledFolder = $Addon->getEnabledFolderByType($type);
                $folder->cd($enabledFolder);
                $list = $folder->read();
                if (!empty($list[1])) {
                    foreach ($list[1] as $addonFile) {
                        if (strstr($addonFile, '.DS_Store') === false) {
                            $Addon->update($addonFile, $type);
                        }
                    }
                }
            }
        }
    }

}

?>
