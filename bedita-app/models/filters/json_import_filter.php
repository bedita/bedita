<?php

/* -----8<--------------------------------------------------------------------
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
 * ------------------------------------------------------------------->8-----
 */

/**
 * JsonImportFilter: class to import objects from JSON
 */
class JsonImportFilter extends BeditaImportFilter
{
    protected $typeName = 'BE-Json';
    protected $mimeTypes = array('text/json', 'application/json');
    public $label = 'JSON data';

    public $options = array(
        'mediaUri' => array(
            'label' => 'Local path or remote url to media',
            'dataType' => 'text',
            'mandatory' => false,
            'defaultValue' => '',
            'multipleChoice' => false
        )
    );

    /**
     * Import BE objects from JSON source string.
     *
     * @param string $filename JSON source file name
     * @param array $options Import options
     * @return array
     * @see DataTransfer::import()
     */
    public function import($filename, array $options = array()) {
        $options['type'] = 'JSON';
        $dataTransfer = ClassRegistry::init('DataTransfer');
        $jsonStr = @file_get_contents($filename);
        if (!empty($options['mediaUri'])) {
            if (filter_var($options['mediaUri'], FILTER_VALIDATE_URL)) {
                $options['sourceMediaUri'] = $options['mediaUri'];
            } else {
                $options['sourceMediaRoot'] = $options['mediaUri'];
            }
        }
        return $dataTransfer->import($jsonStr, $options);
    }
}
