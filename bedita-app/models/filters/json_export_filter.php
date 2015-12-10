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
        $tmpDir = TMP . 'json' . DS . md5(time());
        if(!is_dir($tmpDir)) {
            if(!is_dir(TMP . 'json')) {
                if(@mkdir(TMP . 'json', 0755, true) === false) {
                    throw new BeditaException("Unable to create TMP json dir");
                }
            }
            if(@mkdir($tmpDir, 0755, true) === false) {
                throw new BeditaException("Unable to create $tmpDir");
            }
        }
        $fileName = $tmpDir . DS . $options['filename'];
        if (!strrpos($options['filename'],'.json') || strrpos($options['filename'],'.json') === (strlen($options['filename'])-4)) {
            $fileName.= '.json';
        }
        // export options
        $options['filename'] = $fileName;
        $options['returnType'] = 'JSON';
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
        $res['contentType'] = 'text/json';
        $res['size'] = strlen($res['content']);
        $res = array_merge($dataTransfer->getResult(), $res);
        return $res;
    }
}