<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Resource shell commands:
 *
 * - ls: list entities
 *
 * @since 4.0.0
 */
abstract class ResourcesShell extends Shell
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        if (empty($this->modelClass)) {
            $this->abort('Empty modelClass for shell');
        }
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('ls', [
            'help' => 'list entities',
            'parser' => [
                'description' => [
                    'List entities.',
                    'Option --filter (optional) provides listing filtered by comma separated key=value pairs.'
                ],
                'options' => [
                    'filter' => ['help' => 'List entities filtered by comma separated key=value pairs', 'required' => false],
                ]
            ]
        ]);

        return $parser;
    }

    /**
     * List entities
     *
     * @return void
     */
    public function ls()
    {
        $query = TableRegistry::get($this->modelClass)->find('all');
        if (!empty($this->params['filter'])) {
            $pairs = explode(',', $this->params['filter']);
            $pairs = array_filter(array_map(function ($pair) {
                return array_filter(explode('=', $pair, 2));
            }, $pairs));
            foreach ($pairs as $p) {
                if (!empty($p[1])) {
                    $query->where([$p[0] => $p[1]]);
                }
            }
        }
        $results = $query->toArray();
        $this->out($results ?: 'empty set');
    }
}
