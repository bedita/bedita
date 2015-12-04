<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2015 ChannelWeb Srl, Chialab Srl
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

App::import(array(
    'type' => 'File',
    'name' => 'MediaProviderInterface',
    'search' => array(BEDITA_CORE_PATH . DS .'views' . DS . 'helpers')
));

/**
 * Vimeo helper class
 */
class VimeoHelper extends AppHelper implements MediaProviderInterface {

    var $helpers = array('Html');

    /**
     * Is Vimeo video source available?
     *
     * @param array $obj a representation of Video BEdita object
     * @return boolean
     */
    public function isSourceAvailable(array $obj) {
        return false;
    }

    /**
     * Return the thumbnail supplied by Vimeo as <img> tag
     * If $URLonly is true return only the url to img
     *
     * @param array $obj a representation of Video BEdita object
     * @param array $htmlAttributes HTML attributes to set in <img> tag
     * @param boolean $URLonly
     * @return string
     */
    public function thumbnail(array $obj, array $htmlAttributes, $URLonly) {
        $url = rawurlencode($obj['uri']);
        $vimeoParams = Configure::read('media_providers.vimeo.params');
        $url = sprintf($vimeoParams['urlembed'], $url);
        if (!$oEmbed = $this->oEmbedInfo($url)) {
            return false;
        }
        $src = $oEmbed['thumbnail_url'];
        return (!$URLonly)? $this->Html->image($src, $htmlAttributes) : $src;
    }

    /**
     * Embed Vimeo video
     *
     * @param array $obj a representation of Video BEdita object
     * @param array $attributes HTML attributes
     * @return string|boolean
     */
    public function embed(array $obj, array $attributes) {
        $conf = Configure::getInstance();
        $url = $obj['uri'];
        if (empty($attributes['width']) && empty($attributes['height'])) {
            $attributes['width'] = $conf->media_providers['vimeo']['params']['width'];
            $attributes['height'] = $conf->media_providers['vimeo']['params']['height'];
        }
        foreach ($attributes as $key => $val) {
            $url .= '&' . $key . '=' . $val;
        }
        $vimeoParams = Configure::read('media_providers.vimeo.params');
        $url = sprintf($vimeoParams['urlembed'], $url);
        if (!$oEmbed = $this->oEmbedInfo($url)) {
            return false;
        }
        return $oEmbed['html'] ;
    }

    /**
     * Source it's not available so it returns an empty array
     *
     * @param array $obj a representation of Video BEdita object
     * @return array
     */
    public function source(array $obj) {
        return array();
    }

}
