<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2014 ChannelWeb Srl, Chialab Srl
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
 * Frontend shell script
 * initialize basic frontend app
 */
class FrontendShell extends BeditaBaseShell {

    private $repositories = array(
        'boilerplate-api' => 'https://github.com/bedita/boilerplate-api.git',
        'boilerplate' => 'https://github.com/bedita/boilerplate.git',
        'web-starter-kit' => 'https://github.com/bedita/web-starter-kit.git',
        'responsive' => 'https://github.com/bedita/responsive.git',
        'bootstrap' => 'https://github.com/bedita/bootstrap.git',
    );

    public function init() {
        $this->title('initialize a new frontend');
        $name = (!empty($this->params['name'])) ? $this->params['name'] : $this->in('Choose a name for your frontend');
        if (empty($name)) {
            $this->error('Missing frontend name', 'Frontend name is mandatory');
        }

        $this->out();
        $this->out('Skeleton available:');
        $this->out();
        $repoNames = array_keys($this->repositories);
        foreach ($repoNames as $i => $repoName) {
            $n = $i + 1;
            $this->out($n . '. ' . $repoName . ' (' . $this->repositories[$repoName] . ')');
        }
        $this->out();
        $choice = $this->in('Choose a frontend skeleton', null, 1);
        $this->out();
        if (empty($repoName[$choice - 1])) {
            $this->error('Wrong skeleton selected');
        }

        $skeleton = $repoNames[$choice - 1];

        if (empty($this->params['path'])) {
            $answer = $this->in('Frontend "' . $name . '" will be created in ' . BEDITA_FRONTENDS_PATH
                . $this->nl() . 'Is it right?', array('y', 'n'), 'y');
            $this->out();
            $answer = strtolower($answer);
            if ($answer != 'y' && $answer != 'n') {
                $this->error($answer . 'is not valid answer');
            }
            if ($answer == 'n') {
                $path = $this->in('Write the frontend path ("' . $name . '" excluded)');
            } else {
                $path = BEDITA_FRONTENDS_PATH;
            }
        } else {
            $path = $this->params['path'];
        }

        $folder = new Folder();
        if (!$folder->cd($path)) {
            $this->error($path . " doesn't exists or it's not a directory or it is not accessible.");
        }

        $frontendPath = $path . DS . $name;

        $this->out();
        $this->title('Frontend ' . $name .' data');
        $this->out('Folder: ' . $frontendPath);
        $this->out('Skeleton: ' . $skeleton . ' (' . $this->repositories[$skeleton] . ')');
        $this->out();
        $answer = $this->in('Do you want to proceed with the above data?', array('y', 'n'), 'y');
        $this->out();
        if ($answer != 'y' && $answer != 'n') {
            $this->error($answer . 'is not valid answer');
        }
        if ($answer == 'n') {
            $path = $this->out('Creation aborted. Bye');
            return;
        }

        $gitClone = 'git clone ' . $this->repositories[$skeleton] . ' ' . $frontendPath;
        $this->out("Git clone command:\n> $gitClone");
        $res = system($gitClone);
        if ($res === false) {
            $this->error("git clone fail. Bye.");
        }
        if (!$folder->delete($frontendPath . DS . '.git')) {
            $this->error('fail to remove ' . $frontendPath . DS . '.git folder.'
                . $this->nl() . 'Anyway the frontend "' . $name . '" was created. Remove .git folder by hand');
        }

        $this->out();
        $answer = $this->in('Do you want to init a new git repository?', array('y', 'n'), 'y');
        $this->out();
        if ($answer != 'y' && $answer != 'n') {
            $this->error($answer . 'is not valid answer');
        }
        if ($answer == 'y') {
            if (system('cd ' . $frontendPath . '; git init') === false) {
                $path = $this->error('git repository init failed in ' . $frontendPath);
            }
        }

        $this->out();
        $this->hr();
        $this->out('Frontend "' . $name . '" created in ' . $path);
    }

    public function help() {
        $this->out('Available functions:');
        $this->out(' ');
        $this->out('1. init: initialize a new BEdita frontend instance from scratch');
        $this->out(' ');
        $this->out('   Usage: init [-name <forntend-folder-name>] [-path <base-frontend-path>');
        $this->out(' ');
    }
}
