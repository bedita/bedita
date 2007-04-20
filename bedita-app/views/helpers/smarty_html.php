<?php
/**
 * SmartyHtml Helper class for wrapping HtmlHelper methods
 *
 * requires SmartyView from http://cakeforge.org/snippet/detail.php?type=snippet&id=6 (>= 1.0.0.3)
 * also requires php5 for reflection
 *  - for php4 - find older version (0.0.0.1alpha) for (a more tedius) example to work with
 *
 * @link		http://bakery.cakephp.org/articles/view/138
 * @author		tclineks, sbarre
 * @version		0.0.2
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package 	cake
 * @subpackage	app.views.helpers
 */
class SmartyHtmlHelper extends Helper {

/**
 * Included helpers.
 *
 * @var array
 */
	var $helpers = array('Html');

/**
 * function to register wrappers with Smarty object
 *  - called from SmartyView
 */
	function _register_smarty_functions(&$smarty) {
		$smarty->register_function('html', array(&$this, 'html'));
	}

/**
 * Smarty wrapper for HtmlHelper
 *
 * @param mixed $params params from Smarty template call
 * @param Smarty $smarty Smarty object
 * @return mixed
 */
	function html($params, &$smarty) {
		// sanity check for php version
		if (!class_exists('ReflectionClass')) {
			$smarty->trigger_error("SmartyHtml: Error - requires php 5.0 or above", E_USER_NOTICE);
			return;
		}

		$function_name = $params['func'];
		$assign = $params['assign'];
		$show_call = $params['__show_call'];
		unset($params['func']);
		unset($params['assign']);
		unset($params['__show_call']);

		$parameters = array(); // our final array of function parameters

		if (empty($function_name)) {
			$smarty->trigger_error("SmartyHtml: missing 'func' parameter", E_USER_NOTICE);
			return;
		}

		// process our params array to look for array representations
		// based on key names separated by underscores
		$processedParams = $this->_process_params($params);

		$arrayParams = array();

		$classReflector = new ReflectionClass($this->Html);

		if ($classReflector->hasMethod($function_name)) { // quick sanity check

			$funcReflector = $classReflector->getMethod($function_name);
			
			$funcParams = $funcReflector->getParameters(); // returns an array of parameter names
			
			foreach ($funcParams as $param) {
				$paramName = $param->getName();
				if (isset($processedParams[$paramName])) {
					$parameters[$paramName] =  $processedParams[$paramName];
					unset($processedParams[$paramName]);
				} else {
					if ($param->isDefaultValueAvailable()) {
						$parameters[$paramName] = $param->getDefaultValue();
						// mark the index of array parameters for potential later population
						if (is_array($parameters[$paramName])) {
							$arrayParams[] = $paramName;
						}
					} else if (!$param->isOptional()) {
						$smarty->trigger_error("SmartyHtml: Error ".$paramName." parameter is required for method ".$function_name, E_USER_NOTICE);
					} else {
						$parameters[$paramName] = null;
					}
				}
			}

		// check for unfilled array parameters and populate the first with remaining $params
		if (count($arrayParams)) {
			$parameters[$arrayParams[0]] = $processedParams;
		}
		
		} else {
			$smarty->trigger_error("SmartyHtml: Error " . $classReflector->name . "::" . $function_name . " is not defined", E_USER_NOTICE);
			return;
		}

		if ($show_call) {
			echo '<pre>SmartyHtml calling $html->' . $function_name . ' with these parameters: <br />';
			var_dump($parameters);
			echo '</pre>';
		}

		$result = call_user_func_array(array($this->Html,$function_name),$parameters);
		
		if (!empty($assign)) {
			$smarty->assign($assign, $result);
		} else {
			return $result;
		}
	}

	/**
	 * scans an associative array looking for array keys
	 * that represent nested arrays through the use of the delimiter
	 * parameter (by default an underscore)
	 *
	 * @param array associative array of values
	 * @param string delimiter
	 * @return array
	 */
	function _process_params($params = array(), $delimiter = '_') {
		$result = array();
		foreach ($params as $key => $value) {
			$a = explode($delimiter,$key);
			if (count($a) > 1) {
				$this->_recursively_assign($result,$a,$value);
			} else {
				$result[$key] = $value;
			}
		}
		return $result;
	}

	/**
	 * recursive method to build nested associative arrays
	 * from delimited key names.  fancy!
	 *
	 * @param array result array, passed by reference
	 * @param array array of key name components, split by the delimiter in _process_params
	 * @param string the value to ultimately assign to the nested array
	 */
	function _recursively_assign(&$result,$keyArray,$value) {
		$k = array_shift($keyArray);
		if (count($keyArray) > 1) {
			$this->_recursively_assign($result[$k],$keyArray,$value);
		} else {
			$kk = $keyArray[0];
			$result[$k][$kk] = $value;
		}
	}

}
?>