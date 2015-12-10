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
        $tmpDir = TMP . 'xml' . DS . md5(time());
        if(!is_dir($tmpDir)) {
            if(!is_dir(TMP . 'xml')) {
                if(@mkdir(TMP . 'xml', 0755, true) === false) {
                    throw new BeditaException("Unable to create TMP xml dir");
                }
            }
            if(@mkdir($tmpDir, 0755, true) === false) {
                throw new BeditaException("Unable to create $tmpDir");
            }
        }
        $fileName = $tmpDir . DS . $options['filename'];
        if (!strrpos($options['filename'],'.xml') || strrpos($options['filename'],'.xml') === (strlen($options['filename'])-3)) {
            $fileName.= '.xml';
        }
        // export options
        $options['filename'] = $fileName;
        $options['returnType'] = 'XML';
        $options['no-media'] = true;
        $options['all'] = false;
        $options['logLevel'] = 3; // DEBUG
        // do export
        $dataTransfer = ClassRegistry::init('DataTransfer');
        $content = $dataTransfer->export($objects, $options);
        // clean tmp dir
        $folder = new Folder($tmpDir);
        if (!$folder->delete($tmpDir)) {
            $this->log('Error deleting temporary folder ' . $tmpDir,'error');
        }
        // return data
        $res = array();
        $res['content'] = $content;
        $res['contentType'] = 'text/xml';
        $res['size'] = strlen($res['content']);
        $res = array_merge($dataTransfer->getResult(), $res);
        return $res;
    }
}
