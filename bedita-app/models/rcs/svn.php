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
 * Revision Control System Model for SVN
 */

if (interface_exists('RCS') != true) {
    APP::import('Vendor', 'rcs');
}

class Svn implements RCS {

    public $path = null;
    public $username = '';
    public $passwd = '';

    public $lastCommandCode = null;
    public $lastCommand = null;
    public $lastError = null;

    /**
     * Get the repository type for this model
     *
     * @return string: the repository type
     */
    public function type() {
        return 'svn';
    }

    /**
     * Startup the model initializing a path.
     *
     * @param string $path: the repository path
     * @return void
     */
    public function startup($path) {
        $this->path = $path;
    }

    /**
     * Set authorization params for the model
     *
     * @param string $user: the SVN username
     * @param string $passwd: the SVN user password
     * @return void
     */
    public function authorize($user, $passwd) {
        $this->username = $user;
        $this->passwd = $passwd;
    }

    /**
     * Checkout a repository
     *
     * @param string $url: the repository remote url
     * @param string $path: where the repository should be checkouted
     * @param null $branch: SVN does not use branches
     * @return array: the checkout command results
     */
    public function cloneRemote($url, $path = null, $branch = null) {
        if (empty($path)) {
            $path = $this->path;
        }

        $cmd = "svn checkout $url -b $path";
        return $this->exec($cmd);
    }

    /**
     * Update a repository
     *
     * @param string $path: optional. Can be a repository path to update
     * @return array: the update command results
     */
    public function up($path = null) {
        if (empty($path)) {
            $path = $this->path;
        }

        $cmd = 'cd ' . $path . '; ';
        $cmd .= 'svn up --username ' . $this->username . ' --password ' . $this->passwd . ' 2>&1';
        $res = $this->command($cmd);
        return $res;
    }

    /**
     * Get the current status of the repository
     *
     * @param string $path: optional. Can be a repository path
     * @return array: the status command results
     */
    public function status($path = null) {
        if (empty($path)) {
            $path = $this->path;
        }

        if (empty($path)) {
            $path = $this->path;
        }

        $cmd = 'cd ' . $path . '; ';
        $cmd .= 'svn status ' . $path . ' 2>&1';
        $status = $this->command($cmd);
        $status = (!empty($status))? implode("\n", $status) : null;
        return $status;
    }

    /**
     * SVN does not use branches
     *
     * @return boolean: false
     */
    public function branch($path = null) {
        return false;
    }

    /**
     * SVN does not use branches
     *
     * @return boolean: false
     */
    public function branches($path = null, $remote = false) {
        return false;
    }

    /**
     * Check if the current or given path is a valid SVN repository
     *
     * @param string $path: optional. Can be a repository path
     * @return boolean: is a valid repository or not
     */
    public function valid($path = null) {
        $status = $this->status($path);
        return strpos($status, 'is not a working copy') === false;
    }

    /**
     * Get last commit data
     *
     * @param string $path: optional. Can be a repository path
     * @return array: a list of properties for the last commit
     */
    public function lastCommit($path = null) {
        if (empty($path)) {
            $path = $this->path;
        }

        $cmd = 'cd ' . $path . '; ';
        $cmd .= 'svn log -l 1 --username ' . $this->username . ' --password ' . $this->passwd . ' 2>&1';
        $output = $this->command($cmd);
        if (!empty($output)) {
            $data = explode(' | ', $output[1]);
            if (!empty($data) && $this->lastCommandCode == 0) {
                return array(
                    'hash' => $data[0],
                    'date' => $data[2],
                    'author' => $data[1],
                    'message' => $output[3]
                );
            }
        }
        return null;
    }

    /**
     * Exec a given SVN command
     * fill `lastCommand` and eventually `lastCommandCode` and `lastError` attributes.
     *
     * @param string $cmd: the command to exec
     * @return array: the result of the exec
     */
    public function command($cmd) {
        $this->lastCommand = $cmd;
        exec($cmd, $res, $this->lastCommandCode);
        if ($this->lastCommandCode != 0) {
            $this->lastError = $res;
        }
        return $res;
    }

}

?>
