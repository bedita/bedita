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
 * JsonExportFilter: class to export objects to JSON format
 */
class JsonExportFilter extends BeditaExportFilter
{
    protected $typeName = 'BE-Json';
    protected $mimeTypes = array('text/json', 'application/json');
    public $defaultExtension = 'json';
    public $label = 'JSON data';
    public $options = array();

    /**
     * Export objects in JSON format
     *
     * @param array $objects
     * @param array $options Export options
     * @return array
     * @see DataTransfer::export()
     */
    public function export(array $objects, array $options = array()) {
        $options['returnType'] = 'JSON';
        $options['no-media'] = true;
        $dataTransfer = ClassRegistry::init('DataTransfer');
        $res = array();
        $res['content'] = $dataTransfer->export($objects, $options);
        $res['contentType'] = 'text/json';
        $res['size'] = strlen($res['content']);
        $res = array_merge($dataTransfer->getResult(), $res);
        return $res;
    }
}