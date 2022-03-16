<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;

/**
 * Trees shell command.
 *
 * @since 4.0.0
 * @property \BEdita\Core\Shell\Task\RecoverTreeTask $Recover
 * @property \BEdita\Core\Shell\Task\CheckTreeTask $Check
 */
class TreeShell extends Shell
{
    /**
     * @inheritDoc
     */
    public $tasks = [
        'Recover' => ['class' => 'BEdita/Core.RecoverTree', 'config' => []],
        'Check' => ['class' => 'BEdita/Core.CheckTree', 'config' => []],
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $parser
            ->addSubcommand('recover', [
                'help' => 'Recover objects\' tree from corruption.',
                'parser' => $this->Recover->getOptionParser(),
            ])
            ->addSubcommand('check', [
                'help' => 'Objects-aware sanity checks on tree.',
                'parser' => $this->Check->getOptionParser(),
            ]);

        return $parser;
    }
}
