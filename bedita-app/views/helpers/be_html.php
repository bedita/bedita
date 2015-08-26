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

App::import('Helper', 'Html');

/**
 * BeHtmlHelper class.
 * Extends CakePHP HtmlHelper adding some functionality
 */
class BeHtmlHelper extends HtmlHelper {
    /**
     * Current language.
     *
     * @var string
     */
    protected $currLang = null;

    public function __construct(array $options = array()) {
        if (!empty($options['currLang'])) {
            $this->currLang = $options['currLang'];
        }
    }

    /**
     * Parses a URL involving Router.
     *
     * @see Router::parse()
     * @param mixed $url URL.
     * @param array $mergeParams Array of URL parameters to be merged to URL.
     * @return mixed Parsed URL.
     */
    private function parse ($url = null, array $mergeParams = null) {
        if (!is_array($url) && !preg_match('/^(https?|mailto|s?ftps?)/i', $url)) {
            $url = Router::parse($url);
            if (array_key_exists('named', $url) && is_array($url['named'])) {
                foreach ($url['named'] as $key => $val) {
                    array_push($url, $key . ':' . urlencode($val));
                }
                unset($url['named']);
            }
            if (array_key_exists('pass', $url)) {
                $url = array_merge($url, $url['pass']);
                unset($url['pass']);
            }
            unset($url['plugin']);
        }
        if (is_array($url) && !empty($mergeParams)) {
            $url = array_merge($url, $mergeParams);
        }
        if ((!empty($url['lang']) ? $url['lang'] : $this->currLang) == Configure::read('frontendLang')) {
            $url['lang'] = '';
        }
        return $url;
    }

    /**
     * Returns a formatted link, forcing Router to be involved in URL generation.
     *
     * @see HtmlHelper::link()
     * @param string $title Text to be wrapped within `<a>` tags.
     * @param mixed $url URL.
     * @param array $options Additional HTML attributes.
     * @param mixed $confirmMessage JS confirm message.
     * @param array $mergeParams Array of URL parameters to be merged to URL.
     * @return string HTML tag `<a>`.
     */
    public function link ($title, $url = null, array $options = null, $confirmMessage = false, array $mergeParams = null) {
        return parent::link($title, $this->parse($url, $mergeParams), $options, $confirmMessage);
    }

    /**
     * Returns a URL for the given action, forcing Router to be involved.
     *
     * @see Helper::url()
     * @param mixed $url URL.
     * @param boolean $full Full URL.
     * @param array $mergeParams Array of URL parameters to be merged to URL.
     * @return string URL.
     */
    public function url ($url = null, $full = false, array $mergeParams = null) {
        return parent::url($this->parse($url, $mergeParams), $full);
    }
}
