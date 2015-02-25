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
 * Json View
 */
class JsonView extends View {

    public function __construct($controller) {
        parent::__construct($controller);
        $this->viewPath = 'pages';
    }

    public function render($action = null, $layout = null, $file = null) {
        if (isset($this->viewVars['_serialize'])) {
            $this->serialize($this->viewVars['_serialize']);
            return parent::render('json', 'ajax');
        }
        return parent::render($action, 'ajax', $file);
    }

    protected function serialize($serialize) {
        $data = array();
        if (!is_array($serialize)) {
            $serialize = array($serialize);
        }
        foreach ($serialize as $varName) {
            if (array_key_exists($varName, $this->viewVars)) {
                $data[$varName] = $this->viewVars[$varName];
            }
        }
        $this->viewVars['data'] = $data;
    }

}
