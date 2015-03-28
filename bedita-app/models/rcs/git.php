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
 * Revision Control System Model for Git
 */

if (interface_exists('RCS') != true) {
    APP::import('Vendor', 'rcs');
}

class Git implements RCS {

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
        return 'git';
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
     * @param string $user: the Git username
     * @param string $passwd: the Git user password
     * @return void
     */
    public function authorize($user, $passwd) {
        $this->username = $user;
        $this->password = $passwd;
    }

    /**
     * Clone a repository
     *
     * @param string $url: the repository remote url
     * @param string $path: where the repository should be checkouted
     * @param string $branch: the branch name to checkout
     * @return array: the clone command results
     */
    public function cloneRemote($url, $path = null, $branch = 'master') {
        if (empty($path)) {
            $path = $this->path;
        }

        $cmd = "git clone $url -b $branch $path";
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

        $res = array();
        $currentBranch = $this->branch();
        if ($currentBranch === false) {
            // no version control
            return null;
        } else {
            $findFolder = $this->command('cd ' . $path . '; git rev-parse --show-toplevel');
            if (empty($findFolder)) {
                return null;
            } else {
                $findFolder = $findFolder[0];
            }
            $isWriteable = is_writable($findFolder . DS . '.git' . DS . 'ORIG_HEAD');
            $updateCmd = 'export DYLD_LIBRARY_PATH="/usr/lib/":$DYLD_LIBRARY_PATH &&'; //osx patch
            $updateCmd .= ' cd ' . $path . '; git fetch origin; git merge origin/' . $currentBranch . ' 2>&1';
            $res = $this->command($updateCmd);
            return $res;
        }
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
        $cmd .= 'git status';
        $status = $this->command($cmd);
        $status = (!empty($status[0]))? implode("\n", $status[0]) : null;
        return $status;
    }

    /**
     * Get the current branch
     *
     * @param string $path: optional. Can be a repository path
     * @return string: the current branch name
     */
    public function branch($path = null) {
        if (empty($path)) {
            $path = $this->path;
        }

        $cmd = 'cd ' . $path . '; ';
        $cmd .= 'git rev-parse --abbrev-ref HEAD';
        $branch = $this->command($cmd);
        $branch = (!empty($branch[0]))? $branch[0] : null;
        return $branch;
    }

    /**
     * Get (remote) branches list
     *
     * @param string $path: optional. Can be a repository path
     * @param boolean $remote: should fetch from remote
     * @return array: a list of (remote) branches
     */
    public function branches($path = null, $remote = false) {
        if (empty($path)) {
            $path = $this->path;
        }

        $cmd = 'cd ' . $path . '; ';
        $cmd .= 'git branch';
        if ($remote) {
            $cmd .= ' -r';
        }
        $branches = $this->command($cmd);
        if (!empty($branches)) {
            foreach($branches as &$b) {
                $b = preg_replace('/\s?\*?\s+/', '', $b);
            }
        } else {
            $branches = null;
        }
        return $branches;
    }

    /**
     * Check if the current or given path is a valid Git repository
     *
     * @param string $path: optional. Can be a repository path
     * @return boolean: is a valid repository or not
     */
    public function valid($path = null) {
        $branch = $this->branch($path);
        if (!empty($branch)) {
            return true;
        }
        return false;
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
        $cmd .= 'git log -n 1';
        $output = $this->command($cmd);
        $history = array();
        $commit = array();
        foreach ($output as $line) {
            if (strpos($line, 'commit') === 0) {
                if (!empty($commit)) {
                    array_push($history, $commit);
                    unset($commit);
                    $commit = array();
                }
                $commit['hash'] = substr($line, strlen('commit'));
            } elseif (strpos($line, 'Author') === 0) {
                $commit['author'] = substr($line, strlen('Author:'));
            } elseif (strpos($line, 'Date') === 0) {
                $commit['date'] = substr($line, strlen('Date:'));
            } else {
                if (!empty($commit['message'])) {
                    $commit['message'] .= $line;
                } else {
                    $commit['message'] = $line;
                }
            }
        }
        if(!empty($commit)) {
            array_push($history, $commit);
            unset($commit);
        }
        return (!empty($history)) ? $history[0] : false;
    }

    /**
     * Exec a given Git command
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
