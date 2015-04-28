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
 *
 * Help to embed <video> and <audio> object using video.js
 *
 */
class BeEmbedHtml5Helper extends AppHelper {

    /**
     * Helpers used
     *
     * @var array
     */
    public $helpers = array('Html', 'BeEmbedMedia');

    /**
     * Array of id used in `<video>` and `<audio>` tags.
     * It is used to avoid id collision
     *
     * @var array
     */
    protected $playerIds = array();

    /**
     * Embed generic video/audio object
     *
     * You can customize video.js using `$options['params']`.
     * All `$options['params']` will be encoded and passed as object to video.js
     *
     * @param array $obj BEdita multimedia object
     * @param array $options contains configurations (used from video.js)
     * @param array $attributes HTML attributes to add to <video> or <audio> tag
     * @return mixed string, html code of embed media, or error message
     */
    public function embed(array $obj, array $options = array(), array $attributes = array()) {
        if (empty($obj['uri'])) {
            return __('No file to embed', true);
        }

        $params = empty($options['params']) ? array() : $options['params'];

        if ($obj['object_type_id'] == Configure::read('objectTypes.audio.id')) {
            return $this->embedAudio($obj, $params, $attributes);
        } elseif ($obj['object_type_id'] == Configure::read('objectTypes.video.id')) {
            return $this->embedVideo($obj, $params, $attributes);
        } else {
            return __('Trying to embed a not valid object type');
        }
    }

    /**
     * Setup HTML attributes as width, height, poster, id, class, src
     *
     * If $type is video and is only set width or height then calculate the missing dimension to fit 16/9.
     * The 'id' is set as 'video_nickname' or 'audio_nickname' if $obj['nickname'] exists else video_ or audio_ are followed by an hash
     * If the same $obj is embed many times 'video_nickname' or 'audio_nickname' is suffixed with an hash to avoid id collision
     *
     * If missing $obj['mime_type'] the $attributes['src'] is set.
     * In this way it will be placed in `<video src="">` or `<audio src="">` and video.js will try to use it.
     *
     * @param array $obj BEdita Multimedia object
     * @param array &$attributes HTML attributes
     * @param string $type the media type you want to setup
     * @return void
     */
    protected function setupAttributes(array $obj, array &$attributes = array(), $type = 'video') {
        $posterParams = array(
            'presentation' => 'thumb',
            'mode' => 'fill',
            'upscale' => true,
            'URLonly' => true
        );
        if ($type == 'video') {
             // if no width and height set the default
            if (empty($attributes['width']) && empty($attributes['height'])) {
                $attributes['width'] = Configure::read('media.video.width');
                $attributes['height'] = Configure::read('media.video.height');
            // calculate height to fit 16/9
            } elseif (empty($attributes['height'])) {
                $attributes['height'] = $attributes['width'] * (9 / 16);
            // calculate width to fit 16/9
            } elseif (empty($attributes['width'])) {
                $attributes['width'] = $attributes['height'] * (16 / 9);
            }

            $posterParams['width'] = $attributes['width'];
            $posterParams['height'] = $attributes['width'];
        } elseif ($type == 'audio') {
            if (empty($attributes['width']) && empty($attributes['height'])) {
                $attributes['width'] = Configure::read('media.audio.width');
                if (empty($attributes['poster'])) {
                    if (!empty($obj['relations']['poster'])) {
                        $attributes['height'] =  $attributes['width'];
                        $posterParams['longside'] = $attributes['width'];
                    }
                }
            } else {
                if (!empty($attributes['width']) && empty($attributes['height'])) {
                    $posterParams['longside'] = $attributes['width'];
                } elseif (empty($attributes['width']) && !empty($attributes['height'])) {
                    $posterParams['longside'] = $attributes['height'];
                } else {
                    $posterParams['width'] = $attributes['width'];
                    $posterParams['height'] = $attributes['height'];
                }
            }
        }

        // set poster
        if (empty($attributes['poster'])) {
            if (!empty($obj['relations']['poster'])) {
                $attributes['poster'] = $this->BeEmbedMedia->object($obj['relations']['poster'][0], $posterParams);
            } elseif (!empty($obj['thumbnail'])) {
                $attributes['poster'] = $obj['thumbnail'];
            }
        }

        // set id
        if (empty($attributes['id'])) {
            $idPrefix = $type . '_';
            if (isset($obj['nickname'])) {
                $id = $baseId = $idPrefix . $obj['nickname'];
            } else {
                $baseId = $idPrefix;
                $id = $baseId . md5(rand(10000, 11000) . rand(1, 10000));
            }

            while (in_array($id, $this->playerIds)) {
                $id = $baseId . md5(rand(10000, 11000) . rand(1, 10000));
            }

            $this->playerIds[] = $id;
            $attributes['id'] = $id;
        }

        $class = 'video-js vjs-default-skin';
        $attributes['class'] = (!empty($attributes['class'])) ? $class . ' ' . $attributes['class'] : $class;

        if (empty($obj['mime_type'])) {
            $attributes['src'] = $obj['uri'];
        } elseif (isset($attributes['src'])) {
            unset($attributes['src']);
        }
    }

    /**
     * Embed Video using video.js
     *
     * If `$data` is a string it must be the video url to embed
     *
     * @param array|string $data
     * @param array $params configuration params for video.js
     *                      It will be json encoded and placed in data-setup of video tag
     * @param array $attributes HTML attributes
     * @return string
     */
    public function embedVideo($data, array $params = array(), array $attributes = array()) {
        if (!is_array($data)) {
            $data = array('uri' => $data);
        }

        $this->setupAttributes($data, $attributes, 'video');
        $attr = $this->_parseAttributes($attributes);

        if (!empty($data['mime_type'])) {
            $type = ' type="' . $data['mime_type'] . '"';
        }

        $beditaUrl = Configure::read('beditaUrl');
        $output = '';
        $output .= $this->Html->css($beditaUrl . '/js/libs/video-js/video-js.min.css', null, array('inline' => false));
        $output .= $this->Html->script($beditaUrl . '/js/libs/video-js/video.js', false);

        $output .= '<video ' . $attr . ' controls data-setup=' . json_encode($params) . '>';
        if (isset($type)) {
            $output .= '<source src="' . $data['uri'] . '"' . $type . '/>';
        }
        $output .= '<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
                </video>';
        return $output;
    }

    /**
     * Embed Audio using video.js
     *
     * If `$data` is a string it must be the audio url to embed
     *
     * @param array|string $data
     * @param array $params configuration params for video.js
     *                      It will be json encoded and placed in data-setup of audio tag
     * @param array $attributes HTML attributes
     * @return string
     */
    public function embedAudio($data, array $params = array(), array $attributes = array()) {
        if (!is_array($data)) {
            $data = array('uri' => $data);
        }

        $this->setupAttributes($data, $attributes, 'audio');
        $attr = $this->_parseAttributes($attributes);

        if (!empty($data['mime_type'])) {
            $type = ' type="' . $data['mime_type'] . '"';
        }

        $beditaUrl = Configure::read('beditaUrl');
        $output = '';
        $output .= $this->Html->css($beditaUrl . '/js/libs/video-js/video-js.min.css', false);
        $output .= $this->Html->script($beditaUrl . '/js/libs/video-js/video.js', false);

        $output .= '<audio ' . $attr . ' controls data-setup=' .json_encode($params) .'>';
        if (isset($type)) {
            $output .= '<source src="' . $data['uri'] . '"' . $type . '/>';
        }
        $output .= '</audio>';
        return $output;
    }

}
