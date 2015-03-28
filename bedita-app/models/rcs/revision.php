<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
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
 * Revision Control System Generic Model for Git/SVN repositories
 */

class Revision {

    /**
     * Check if the provided path is under Git or SVN control.
     *
     * @param string $path: the path to check
     * @return stdClass|null: the Git or SVN scr model related to the $path
     */
    public function getRepository($path) {
        $rcs = ClassRegistry::init('Git');
        $rcs->startup($path);
        if ($rcs->valid()) {
            return $rcs;
        } else {
            $rcs = ClassRegistry::init('Svn');
            $rcs->startup($path);
            if ($rcs->valid()) {
                return $rcs;
            }
        }
        return null;
    }

    /**
     * Extract an array of data of the current repository status
     *
     * @param stdClass $repo: the Git or SVN scr model related to the $path
     * @return array: a list of repository properties
     */
    public function getData($repo) {
        return array(
            'path' => realpath($repo->path),
            'branch' => $repo->branch(),
            'history' => $repo->lastCommit(),
            'type' => $repo->type()
        );
    }

}

?>
