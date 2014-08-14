<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
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
namespace BEdita\View;

use Smarty;
use Cake\View\View;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Event\EventManager;
use Cake\Core\Configure;

/**
 * SmartyView class for CakePHP
 */
class SmartyView extends View {

    /**
     * The Smarty instance
     *
     * @var \Smarty
     */
    public $smarty = null;

    /**
     * Array of template directories
     *
     * @var array
     */
    public $templateDir = [];

    /**
     * File extension. Defaults to sMARTY's template ".tpl"
     *
     * @var string
     */
    protected $_ext = '.tpl';

    public function __construct (Request $request = null, Response $response = null, EventManager $eventManager = null, array $viewOptions = [])    {
        parent::__construct($request, $response, $eventManager, $viewOptions);
        $this->templateDir = Configure::read('App.paths.templates');
        $this->smarty = new Smarty();
        $this->smarty->setCompileDir(TMP . 'smarty' . DS . 'compile' . DS);
        $this->smarty->setCacheDir(TMP . 'smarty' . DS . 'cache' . DS);
        $this->smarty->setConfigDir(ROOT .  DS . 'config' . DS . 'smarty' . DS);
        $this->smarty->setTemplateDir($this->templateDir);
        //$this->smarty->compile_id = $controller->name; compile_id needed??

        // add smarty plugins dir
        $this->smarty->addPluginsDir(ROOT . DS . 'vendor' . DS . 'smartyPlugins');
        //if (!BACKEND_APP) {
        //    $this->smarty->addPluginsDir(BEDITA_CORE_PATH . DS . 'vendor' . DS . 'smartyPlugins');
        //}
    }

    /**
     * Override parent sandbox method to evaluate a template / view script in.
     * Assign Smarty vars and fetch template.
     * Fallback to CakePHP View if $viewFile is *.ctp
     *
     * @param string $viewFile Filename of the view
     * @param array $dataForView Data to include in rendered view.
     *                           If empty the current View::$viewVars will be used.
     * @return string Rendered output
     */
    protected function _evaluate($viewFile, $dataForView) {
        $isCtpFile = (substr($viewFile, -3) === 'ctp');
        if ($isCtpFile) {
            return parent::_evaluate($viewFile, $dataForView);
        }

        $this->__viewFile = $viewFile;
        $smartyData = null;
        // if element assign $dataForView in specific scope
        if ($this->_currentType == static::TYPE_ELEMENT) {
            $smartyData = $this->smarty->createData();
        }
        foreach ($dataForView as $key => $value) {
			if (!is_object($key)) {
                if ($this->_currentType == static::TYPE_ELEMENT) {
                    $smartyData->assign($key, $value);
                } else {
                    $this->smarty->assign($key, $value);
                }
			}
		}
        $this->smarty->assignByRef('this', $this);
        $content = $this->smarty->fetch($viewFile, $smartyData);

        unset($this->__viewFile);
        return $content;
    }

}
