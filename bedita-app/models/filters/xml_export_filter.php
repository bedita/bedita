<?php

/* -----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2012 ChannelWeb Srl, Chialab Srl
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
 * XmlExportFilter: class to export objects to XML format
 */
class XmlExportFilter extends BeditaExportFilter
{
    protected $typeName = "BE-Xml";
    protected $mimeTypes = array("text/xml", "application/xml");
    public $defaultExtension = 'xml';
    public $options = array();

    /**
     * Export objects in XML format
     *
     * @param array $objects
     * @param array $options Export options
     * @return array
     * @see DataTransfer::export()
     * @deprecated
     */
    public function export(array $objects, array $options = array()) {
        $options['returnType'] = 'XML';
        $DataTransfer = ClassRegistry::init('DataTransfer');

        $res = array();
        $res['content'] = $DataTransfer->export($objects, $options);
        $res['contentType'] = 'text/xml';
        $res['size'] = strlen($res['content']);
        $res = array_merge($DataTransfer->getResult(), $res);
        return $res;
    }
}
