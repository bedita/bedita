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
 * Gravatar helper
 *
 * Class to easily handle Gravatar image
 */
class GravatarHelper extends AppHelper {

    /**
     * Helpers used
     * @var array
     */
    public $helpers = array('Html');

    /**
     * Gravatar image endpoint
     * @var string
     */
    private $imageEndpoint = 'https://secure.gravatar.com/avatar/';

    /**
     * Image options used in url query string
     *
     * - 'd' is the default image used if missing gravatar user image
     * - 's' the size of the image
     *
     * @see https://it.gravatar.com/site/implement/images
     * @var array
     */
    private $imageOptions = array(
        'd' => 'identicon',
        's' => null
    );

    /**
     * Constructor to override self::$imageOptions
     *
     * @param array $options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        $this->setImageOptions($options);
    }

    /**
     * Get self::$imageOptions
     *
     * @param array $options
     * @return array
     */
    public function getImageOptions() {
        return $this->imageOptions;
    }

    /**
     * Set self::$imageOptions
     *
     * @param array $options
     * @return void
     */
    public function setImageOptions(array $options = array()) {
        $this->imageOptions = array_merge(
            $this->imageOptions,
            array_intersect_key($options, $this->imageOptions)
        );
        return $this->imageOptions;
    }

    /**
     * Starting from user return the url of gravatar image
     *
     * @param string|array $user an email or an array containing 'email' key
     * @param array $options override self::$imageOptions
     * @return string
     */
    public function imageUrl($user, array $options = array()) {
        $email = $this->userEmail($user);
        $hash = md5(strtolower(trim($email)));
        $options = array_intersect_key($options, $this->imageOptions);
        $options += $this->imageOptions;
        $url = $this->imageEndpoint . $hash . Router::queryString($options);
        return $url;
    }

    /**
     * Return a well formatted <img> tag using gravatar image.
     * It can be configured with $options that can contain key to override self::imageOptions
     * - override of self::$imageOptions
     * - 'html' an array of HTML attribute to use in <img>
     *
     * @see self::$imageOptions
     * @param string|array $user an email or an array containing 'email' key
     * @param array $options image options
     * @return string
     */
    public function image($user, array $options = array()) {
        $options += array('html' => array());
        $url = $this->imageUrl($user, $options);
        return $this->Html->image($url, $options['html']);
    }

    /**
     * Given a $user return an email
     * $user is expected to be a string (email) or an array with 'email' key
     * @param string|u $user
     * @return [type] [description]
     */
    private function userEmail($user) {
        $email = '';
        if (is_string($user)) {
            $email = $user;
        } elseif (is_array($user) && !empty($user['email'])) {
            $email = $user['email'];
        }
        return $email;
    }

}
