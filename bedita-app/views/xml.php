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
 * Xml View used to create XML response
 */
class XmlView extends View {

    /**
     * [__construct description]
     * @param [type] $controller [description]
     */
    public function __construct($controller) {
        parent::__construct($controller);
        $this->viewPath = 'pages/xml';
        if (empty($this->helpers)) {
            $this->helpers = array();
        }
        if (!in_array('Xml', $this->helpers)) {
            $this->helpers[] = 'Xml';
        }
    }

    public function render($action = null, $layout = null, $file = null) {
        if (array_key_exists('_serialize', $this->viewVars)) {
            // empty response
            if ($this->viewVars['_serialize'] === null) {
                return parent::render(false, false);
            }
            $this->serialize($this->viewVars['_serialize']);
            return parent::render('xml', 'xml/default');
        }
        return parent::render($action, 'xml/default', $file);
    }

    protected function serialize($serialize) {
        $rootNode = isset($this->viewVars['_rootNode']) ? $this->viewVars['_rootNode'] : 'response';
        $data = array();
        if (!is_array($serialize)) {
            $serialize = array($serialize);
        }
        foreach ($serialize as $varName) {
            if (array_key_exists($varName, $this->viewVars)) {
                $data[$varName] = $this->viewVars[$varName];
            }
        }
        if (count($data) > 1 || empty($data[$rootNode])) {
            $data = array($rootNode => $data);
        }
        $this->viewVars['data'] = $data;
    }

}
