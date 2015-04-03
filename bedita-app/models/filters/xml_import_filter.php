<?php

/* -----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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
 * XmlImportFilter: class to import objects from XML
 */
class XmlImportFilter extends BeditaImportFilter
{
    protected $typeName = "BE-Xml";
    protected $mimeTypes = array("text/xml", "application/xml");

    /**
     * Import BE objects from XML source string.
     *
     * @param string $filename XML source file name
     * @param array $options Import options
     * @return array
     * @see DataTransfer::import()
     * @deprecated
     */
    public function import($filename, array $options = array()) {
        $options['type'] = 'XML';
        return ClassRegistry::init('DataTransfer')->import(@file_get_contents($filename), $options);
    }
}
