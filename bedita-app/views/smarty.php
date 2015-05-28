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

/**
 * SmartyView
 *
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 *
 * $Id$
 *  */
class SmartyView extends View {

	private $template_dir;
	private $layout_dir;

	private $_smarty = null;

	function __construct (&$controller)	{
		parent::__construct($controller);

		$this->ext = ".tpl";

		$this->template_dir = array(
			VIEWS . $this->viewPath . DS . $this->subDir,
			VIEWS . $this->viewPath,
			VIEWS
		);

		$this->layout_dir = array(
			LAYOUTS . $this->subDir,
			VIEWS
		) ;
		
		App::import('Vendor', 'SmartyClass', array('file' => 'smarty'.DS.'libs'.DS.'Smarty.class.php'));
		$this->_smarty = new Smarty();

		$this->_smarty->setCompileDir(TMP . 'smarty' . DS . 'compile');
		$this->_smarty->setCacheDir(TMP . 'smarty' . DS . 'cache' . DS);
		$this->_smarty->setConfigDir(ROOT . APP_DIR . DS . 'config' . DS . 'smarty' . DS);
		$this->_smarty->compile_id	= $controller->name ;

		// add smarty plugins dir
		$this->_smarty->addPluginsDir(APP . 'vendors' . DS . '_smartyPlugins');
		if(!BACKEND_APP) {
			$this->_smarty->addPluginsDir(BEDITA_CORE_PATH . DS . 'vendors' . DS . '_smartyPlugins');
		}
	}

	// Add by BEdita team - Giangi
	// Change template dir
	function setTemplateDir($path = VIEW) {
		$old = $this->template_dir ;
		$this->template_dir  = $path ;
		return $old ;
	}

	function getTemplateDir() {
		return $this->template_dir ;
	}


	function _render($___viewFn, $___data_for_view, $loadHelpers = true, $cached = false) {
		// used to restore smarty vars
		$prevSmartyVars = $this->_smarty->getTemplateVars();

		// clears all assigned variables to the smarty class
		$this->_smarty->clearAllAssign();

		$loadedHelpers = array();
		if ($this->helpers != false && $loadHelpers === true) {

			$loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);
			$helpers = array_keys($loadedHelpers);
			$helperNames = array_map(array('Inflector', 'variable'), $helpers);

			for ($i = count($helpers) - 1; $i >= 0; $i--) {
				$name = $helperNames[$i];
				$helper =& $loadedHelpers[$helpers[$i]];

				if (!isset($___dataForView[$name])) {
					${$name} =& $helper;
				}
				$this->loaded[$helperNames[$i]] =& $helper;
				$this->{$helpers[$i]} =& $helper;
				$this->_smarty->assignByRef($helperNames[$i], ${$helperNames[$i]});
			}
			$this->_triggerHelpers('beforeRender');
			unset($name, $loadedHelpers, $helpers, $i, $helperNames, $helper);
		}

		// if this is a layout call or a template call and change the template dir accordingly
		if(isset($___data_for_view['content_for_layout'])) {
			$this->_smarty->setTemplateDir($this->layout_dir);
		} else {
			$this->_smarty->setTemplateDir($this->template_dir);
		}

		// load the data variables, being set by controller
		foreach(array_keys($___data_for_view) as $k) {
			if (!is_object($k)) {
				$this->_smarty->assignByRef($k, $___data_for_view[$k]);
			}
		}
		$this->_smarty->assignByRef("view", $this);

		$out = $this->_smarty->fetch($___viewFn);

		if ($loadHelpers === true) {
			$this->_triggerHelpers('afterRender');
		}

		$caching = (
			isset($this->loaded['cache']) &&
			(($this->cacheAction != false)) && (Configure::read('Cache.check') === true)
		);

		if ($caching) {
			if (is_a($this->loaded['cache'], 'CacheHelper')) {
				$cache =& $this->loaded['cache'];
				$cache->base = $this->base;
				$cache->here = $this->here;
				$cache->helpers = $this->helpers;
				$cache->action = $this->action;
				$cache->controllerName = $this->name;
				$cache->layout  = $this->layout;
				$cache->cacheAction = $this->cacheAction;
				$cache->cache($___viewFn, $out, $cached);
			}
		}

		// unset local var (like $params in View::element)
		foreach ($___data_for_view as $key => $value) {
			if (!array_key_exists($key, $prevSmartyVars) && !array_key_exists($key, $this->loaded)) {
				$this->_smarty->clearAssign($key);
			}
		}

		// restore smarty vars setted before clear_all_assign called
		if (!empty($prevSmartyVars)) {
			foreach ($prevSmartyVars as $k => $v) {
				if ($this->_smarty->getTemplateVars($k) === null) {
					$this->_smarty->assignByRef($k, $prevSmartyVars[$k]);
				}
			}
		}

		// force smarty template dir to template_dir
		$this->_smarty->setTemplateDir($this->template_dir);

		return $out;
	}


	/**
	 * Override View::set to set Smarty var too
	 *
	 * @param mixed $one see View::set (cake/libs/view/view.php)
	 * @param mixed $two see View::set
	 * @return unknow
	 */
	public function set($one, $two = null) {
		$data = null;
		if (is_array($one)) {
			if (is_array($two)) {
				$data = array_combine($one, $two);
			} else {
				$data = $one;
			}
		} else {
			$data = array($one => $two);
		}
		if ($data == null) {
			return false;
		}
		$this->viewVars = $data + $this->viewVars;

		foreach ($data as $name => $value) {
			$this->_smarty->assignByRef($name, $value);
		}
	}

	function & getSmarty() {
		return $this->_smarty;
	}

	/**
	* Get the extensions that view files can use.
	*
	* @return array Array of extensions view files use.
	* @access protected
	*/
	function _getExtensions() {
		$exts = array($this->ext);
		if ($this->ext !== '.tpl') {
			array_push($exts, '.tpl');
		}
		return $exts;
	}

}
?>