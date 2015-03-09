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

/**
 * HTML5 embed helper
 */
class BeEmbedHtml5Helper extends AppHelper {

    public $helpers = array("Html");

    /**
     * embed generic flash object (video, swf, audio mp3)
     * file extension supported: mp3, flv, m4v
     *
     * @param array $obj BEdita multimedia object
     * @param array $params contains flashvars, params <param> tag
     * @param array $htmlAttributes
     * @return mixed string|boolean, html code of embed media, or false if file extension is not supported
     */
    public function embed($obj, $params, $htmlAttributes ) {
        if (empty($obj['uri'])) {
            return __("No file to embed");
        }

        $params = empty($params['params']) ? array() : $params['params'];
        $extension = $this->getFileExtension($obj['uri']);

        if ($obj["object_type_id"] == Configure::read("objectTypes.audio.id") && $extension == 'mp3') {
            $fileType = "audio";

            return $this->embedAudio($obj['uri'], $params, $htmlAttributes);

        } else {
            $fileType = "video";

            return $this->embedVideo($obj['uri'], $params, $htmlAttributes);

        }
    }

    /**
     * Embed Video
     *
     * @param
     * @return
     */
    private function embedVideo($urlVideo, $params, $attributes) {
        $beditaUrl = Configure::read('beditaUrl');
        $defaultParams = array(
            'features' => array('playpause', 'loop', 'current', 'progress', 'duration', 'volume')
            );

        //defaults attributes
        if (empty($attributes["width"]))
            $attributes["width"] = Configure::read("media." . $fileType . ".width");
        if (empty($attributes["height"]))
            $attributes["height"] = Configure::read("media." . $fileType . ".height");

        $width = (!empty($attributes['width'])) ? $attributes['width'] : $this->widthDef;
        $height = (!empty($attributes['height'])) ? $attributes['height'] : $this->heightDef;

        if (empty($attributes['id'])) {
            $attributes['id'] = "be_id_" . rand(10000, 11000) . rand(1, 10000);
        }
        if (!empty($attributes['src'])) {
            unset($attributes['src']);
        }
        $attr = $this->_parseAttributes($attributes);

        //player params
        if (empty($params)) {
            $params = $defaultParams;
        } else {
            $params = array_merge($defaultParams, $params);
        }
        $output = "";
        $output .= $this->Html->script(Configure::read('beditaUrl') . "/js/libs/mediaelement/mediaelement-and-player.min.js",false);
        $output .= $this->Html->css(Configure::read('beditaUrl') . "/js/libs/mediaelement/mediaelementplayer.css",false);
        $output .= '<video src="'.$urlVideo.'" '.$attr.' controls="controls" >
                        <!-- Flash fallback for non-HTML5 browsers without JavaScript -->
                        <object '.$attr.' type="application/x-shockwave-flash" data="flashmediaelement.swf">
                            <param name="movie" value="flashmediaelement.swf" />
                            <param name="flashvars" value="controls=true&file='.$urlVideo.'" />
                            <!-- Image as a last resort -->
                            <img src="myvideo.jpg" '.$attr.' title="No video playback capabilities" />
                        </object>
                    </video>';
        $output .= '<script>jQuery(document).ready(function($) {
                        $("video").mediaelementplayer('.json_encode($params).');
                    });</script>';
        return $output;
    }

    /**
     * Embed Audio
     *
     * @param
     * @return
     */
    private function embedAudio($urlAudio, $params, $attributes) {
        $beditaUrl = Configure::read('beditaUrl');

        if (empty($attributes['id'])) {
            $attributes['id'] = "be_id_" . rand(10000, 11000) . rand(1, 10000);
        }
        if (!empty($attributes['src'])) {
            unset($attributes['src']);
        }
        $attr = $this->_parseAttributes($attributes);

        //player params
        if (empty($params)) {
            $params = $defaultParams;
        } else {
            $params = array_merge($defaultParams, $params);
        }

        $output = "";
        $output .= $this->Html->script(Configure::read('beditaUrl') . "/js/libs/mediaelement/mediaelement-and-player.min.js",false);
        $output .= $this->Html->css(Configure::read('beditaUrl') . "/js/libs/mediaelement/mediaelementplayer.css",false);

        $output .= '<audio src="'.$urlAudio.'" '.$attr.'" controls="controls"></audio>';
        $output .= '<script>jQuery(document).ready(function($) {
                        $("audio").mediaelementplayer('.json_encode($params).');
                    });</script>';
        return $output;
    }



    /**
     * get file extension
     *
     * @param string $filePath
     * @return mixed string|boolean, file extension or false (if extension is not recognized through pathinfo)
     */
    private function getFileExtension($filePath) {
        $path_parts = pathinfo($filePath);
        if (empty($path_parts['extension']))
            return false;

        return strtolower($path_parts['extension']);
    }

}
