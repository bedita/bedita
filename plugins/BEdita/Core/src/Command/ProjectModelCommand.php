<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Command;

use BEdita\Core\Utility\ProjectModel;
use BEdita\Core\Utility\Resources;
use Cake\Cache\Cache;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\ConventionsTrait;

/**
 * Command to apply project model from input file.
 *
 * @since 4.5.0
 */
class ProjectModelCommand extends Command
{
    use ConventionsTrait;

    /**
     * Project model default file name
     *
     * @var string
     */
    public const PROJECT_MODEL_FILE = 'project_model.json';

    /**
     * {@inheritDoc}
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->addOption('file', [
                'help' => 'Path of JSON file containing project model to apply',
                'short' => 'f',
                'required' => false,
            ])
            ->addOption('plugin', [
                'help' => 'Plugin to use for loading default `project_model.json` file',
                'short' => 'p',
                'required' => false,
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $file = $this->modelFilePath($args, $io);
        if (!file_exists($file)) {
            $io->error(sprintf('File not found %s', $file));

            return self::CODE_ERROR;
        }

        $project = (array)json_decode(file_get_contents($file), true);
        if (empty($project)) {
            $io->error(sprintf('Bad file content in %s', $file));

            return self::CODE_ERROR;
        }

        $diff = ProjectModel::diff($project);
        if (!empty($diff['remove'])) {
            $io->warning('Items to remove: ' . json_encode($diff['remove']));
            unset($diff['remove']);
        }
        if (empty($diff)) {
            $io->success('Project model in sync, exiting.');

            return self::CODE_SUCCESS;
        }

        Resources::save($diff);

        foreach (Cache::configured() as $cache) {
            Cache::clear(false, $cache);
        }
        $io->success('Cache cleared');

        return null;
    }

    /**
     * Retrieve model file path using CLI options and defaults.
     *  - if `--file/-f` is used, this path is used
     *  - if `--plugin/-p` is used default model file is searched in a plugin
     *  - if no option is passed default path is used
     *
     * @param \Cake\Console\Arguments $args Console arguments
     * @param \Cake\Console\ConsoleIo $io Console IO
     * @return string
     */
    protected function modelFilePath(Arguments $args, ConsoleIo $io): string
    {
        $file = $args->getOption('file');
        if (!empty($file)) {
            return $file;
        }
        $plugin = $args->getOption('plugin');
        if (!empty($plugin)) {
            return $this->_pluginPath($plugin) . 'config' . DS . self::PROJECT_MODEL_FILE;
        }

        return CONFIG . self::PROJECT_MODEL_FILE;
    }
}
