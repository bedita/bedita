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
 * Video caption
 */
class Caption extends BeditaAnnotationModel
{
    public $useTable = 'annotations';

    public $actsAs = array();

    public $validate = array(
        // 'description' => array(
        //     'rule' => 'notEmpty',
        //     'message' => 'This field cannot be left blank'
        // ),
    );

    public $objectTypesGroups = array('nodashboard');

    /**
     * Convert SRT to VTT before entity is persisted to datasource.
     *
     * @return void
     */
    public function beforeValidate()
    {
        parent::beforeValidate();

        if (empty($this->data[$this->alias]['title'])) {
            // Use language label as default title.
            $lang = Configure::read('defaultLang');
            if (!empty($this->data[$this->alias]['lang'])) {
                $lang = $this->data[$this->alias]['lang'];
            }

            $this->data[$this->alias]['title'] = Configure::read(sprintf('langOptions.%s', $lang)) ?: '';
        }

        if (!empty($this->data[$this->alias]['description'])) {
            // Convert SRT to VTT.
            $this->data[$this->alias]['description'] = static::srtToVtt($this->data[$this->alias]['description']);
        }
    }

    /**
     * Convert SRT to VTT.
     *
     * @param string $contents SRT caption to convert to VTT. 
     * @return string|null
     */
    public static function srtToVtt($contents)
    {
        if (!is_string($contents)) {
            // WTF!?
            return null;
        }

        if (preg_match('/^WEBVTT\n\n/', $contents) != false) {
            // Already a VTT.
            return $contents;
        }

        // Add header.
        $contents = 'WEBVTT' . PHP_EOL . PHP_EOL . $contents;

        // Replace commas with dots in timings.
        $contents = preg_replace_callback(
            '/^(?P<start>\d+:\d{2}:\d{2},\d+) --> (?P<end>\d+:\d{2}:\d{2},\d+)$/m',
            function ($match) {
                $start = str_replace(',', '.', $match['start']);
                $end = str_replace(',', '.', $match['end']);

                return sprintf('%s --> %s', $start, $end);
            },
            $contents
        );

        return $contents;
    }
}
