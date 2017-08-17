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
namespace BEdita\API\Shell;

use Cake\Console\Shell;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Symfony\Component\Yaml\Yaml;

/**
 * Generate OpenAPI 2 YAML file merging multiple yaml files in /spec folder
 * @since 4.0.0
 */
class SpecShell extends Shell
{

    /**
     * Default YAML OpenAPI file
     *
     * @var string
     */
    const YAML_SPEC_FILE = 'be4.yaml';

    /**
     * Yaml specifications folder path
     *
     * @var string
     */
    public $specDir = null;

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function initialize()
    {
        parent::initialize();
        $this->specDir = Plugin::path('BEdita/API') . 'spec' . DS;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('generate', [
            'help' => 'Generate global YAML OpenAPI 2 file.',
            'parser' => [
                'description' => [
                    'This command to generates a single YAML OpenAPI 2 spec file.',
                    'File is built merging yaml files in /spec foler.',
                ],
                'options' => [
                    'output' => [
                        'help' => 'Specifiy output file path',
                        'short' => 'o',
                        'required' => false,
                        'default' => $this->specDir . self::YAML_SPEC_FILE,
                    ],
                ],
            ],
        ]);

        return $parser;
    }

    /**
     * Save YAML OpenAPI 2 spec file
     * Generated file has  default path BEdita/API/spec/be4.yaml)
     *
     * @return void
     */
    public function generate()
    {
        $yamlFile = $this->params['output'];
        if (file_exists($yamlFile)) {
            $res = $this->in('Overwrite yaml file "' . $yamlFile . '"?', ['y', 'n'], 'n');
            if ($res != 'y') {
                $this->info('Yaml file not updated');

                return;
            }
        }
        $dir = new Folder($this->specDir);
        $files = $dir->find('.*\.yaml', true);
        $be4Spec = ['paths' => [], 'definitions' => []];
        foreach ($files as $file) {
            if ($file !== self::YAML_SPEC_FILE) {
                $yamlData = Yaml::parse(file_get_contents($dir->pwd() . DS . $file));
                $be4Spec = array_merge($yamlData, $be4Spec);
                if (!empty($yamlData['paths'])) {
                    $be4Spec['paths'] += $yamlData['paths'];
                }
                $be4Spec['definitions'] = array_merge(
                    empty($yamlData['definitions']) ? [] : $yamlData['definitions'],
                    $be4Spec['definitions']
                );
            }
        }
        $be4Yaml = Yaml::dump($be4Spec, 2, 4, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);

        file_put_contents($yamlFile, $be4Yaml);

        $this->info('Yaml file updated ' . $yamlFile);
    }
}
