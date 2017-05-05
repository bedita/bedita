<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;

/**
 * Task to check if current schema is up to date, and if SQL standards are satisfied.
 *
 * @since 4.0.0
 * @deprecated 4.0.0 Use `bin/cake migrations dump` instead.
 */
class SaveSchemaTask extends Shell
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->setDescription([
                'Use this command to generate a dump schema file.',
                'File is generated using current database connection.',
            ])
            ->addOption('connection', [
                'help' => 'Connection name to use',
                'short' => 'c',
                'required' => false,
                'default' => 'default',
                'choices' => ConnectionManager::configured(),
            ])
            ->setEpilog('This command is DEPRECATED! Use `bin/cake migrations dump` instead.');

        return $parser;
    }

    /**
     * Dump current schema.
     *
     * @return bool
     */
    public function main()
    {
        if (!Plugin::loaded('Migrations')) {
            $this->abort('Plugin "Migrations" must be loaded in order to dump current schema');
        }

        $this->out(sprintf('<warning>%s</warning>', $this->getOptionParser()->getEpilog()), 2);

        return $this->dispatchShell([
            'command' => ['migrations', 'dump'],
            'extra' => $this->params,
        ]);
    }
}
