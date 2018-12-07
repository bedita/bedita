<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
 * Video stream
 */
class Video extends BeditaStreamModel
{
    public $useTable = 'videos';

    public $actsAs = array(
        'Captions',
    );

    public $objectTypesGroups = array('multimedia', 'leafs', 'related');

    /**
     * Transform captions
     *
     * @param array $options The transform options
     * @return array
     */
    public function apiTransformer(array $options = array()) {
        $transformer = parent::apiTransformer($options);
        $transformer['captions'] = ClassRegistry::init('Caption')->apiTransformer($options);

        return $transformer;
    }
}
