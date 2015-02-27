<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2015 ChannelWeb Srl, Chialab Srl
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

App::import('Helper', 'Form');

/**
 * BeFormHelper class
 *
 * extends CakePHP FormHelper adding some functionality
 */
class BeFormHelper extends FormHelper {

    /**
     * Return an input type hidden with a csrf token
     * To use in combination with CsrfComponent to prevent CSRF attack
     *
     * @return string
     */
    public function csrf() {
        $output = '';
        if (isset($this->params['_csrfToken']) && !empty($this->params['_csrfToken'])) {
            $output = $this->hidden('_csrfToken.key', array(
                'value' => $this->params['_csrfToken']['key'], 'id' => 'csrfToken' . mt_rand())
            );
        }
        return $output;
    }

    /**
     * Returns an HTML FORM element.
     * Use parent FormHelper::create() and optionally add csrf input hidden
     *
     * @see FormHelper::create()
     * @param string $model The model object which the form is being defined for
     * @param array $options An array of html attributes and options.
     * @return string An formatted opening FORM tag.
     */
    function create($model = null, $options = array()) {
        $output = parent::create($model, $options);
        $output .= $this->csrf();
        return $output;
    }

}
