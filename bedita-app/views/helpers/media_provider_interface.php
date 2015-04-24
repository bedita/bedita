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
 * interface used by media provider helper as (youtube, vimeo, blip.tv)
 */
interface MediaProviderInterface {

    /**
     * Return true if source is available for $object else return false
     *
     * @param array $object a representation of BEdita object
     * @return boolean
     */
    public function isSourceAvailable(array $object);

    /**
     * Return the url to the source if it's available else it should return an empty string
     *
     * @param array $object a representation of BEdita object
     * @return string
     */
    public function source(array $object);

    /**
     * Return the html to embed using provider UI
     *
     * @param array $object a representation of BEdita object
     * @param array $attributes HTML attributes
     * @return string|boolean return false if it fails to get data
     */
    public function embed(array $object, array $attributes);

    /**
     * Return the thumbnail supplied by provider as <img> tag
     * If $URLonly is true return only the url to img
     *
     * @param array $object a representation of BEdita object
     * @param array $htmlAttributes HTML attributes to set in <img> tag
     * @param boolean $URLonly true to return only the image url
     * @return string
     */
    public function thumbnail(array $object, array $htmlAttributes, $URLonly);

}
