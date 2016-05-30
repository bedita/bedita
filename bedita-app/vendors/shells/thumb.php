<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
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

require_once 'bedita_base.php';

/**
 * Shell script to create thumbnails 
 */
class ThumbShell extends BeditaBaseShell {

    /**
     * Create thumb from image id with basic options 
     */
    public function create() {
        $id = $this->mandatoryArgument('id', 'image id is mandatory, use -id');
        $image = ClassRegistry::init('Image');
        $imgData = $image->findById($id);
        if (empty($imgData['uri'])) {
            $this->out('No stream uri found for image id: ' . $id);
            return;
        }
        $beThumb = BeLib::getObject('BeThumb');
        $options = $this->readThumbOptions();
        $thumbUri = $beThumb->image($imgData, $options);
        $this->out('Thumbnail created: ' . $thumbUri);
        $this->out('Done');
    }

    private function readThumbOptions() {
        $res = array();
        if (!empty($this->params['-thumb-options'])) {
            $thumbOpts = $this->params['-thumb-options'];
            $opts = explode('|', $thumbOpts);
            foreach ($opts as $v) {
                $opt = explode('=', $v);
                if (count($opt) != 2) {
                    $this->out('bad input parameter: ' . $thumbOpts);
                    return false;
                }
                if ($opt[1] === 'true') {
                    $opt[1] = true;
                } elseif ($opt[1] === 'false') {
                    $opt[1] = false;
                }
                $res[$opt[0]] = $opt[1];
            }
        }
        return $res;
    }

    function help() {
        $this->out('Available functions:');
        $this->out(' ');
        $this->out('1. create: thumbnail from image id');
        $this->out(' ');
        $this->out('   Usage: create -id <object-id> [--thumb-options <thumb-options>]');
        $this->out(' ');
        $this->out("    -id <object-id>\t source image id");
        $this->out("    --thumb-options \t thumb options in the form width=100|height=100|...");
        $this->out(' ');
    }
}
