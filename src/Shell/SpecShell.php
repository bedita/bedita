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
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Symfony\Component\Yaml\Yaml;

/**
 * Generate OpenAPI 2 YAML file merging multiple YAML files in /spec folder
 *
 * @since 4.0.0
 */
class SpecShell extends Shell
{

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
                    'File is built merging yaml files in /spec folder.',
                ],
                'options' => [
                    'spec' => [
                        'help' => 'Path where spec files are located',
                        'short' => 's',
                        'required' => false,
                        'default' => Plugin::path('BEdita/API') . 'spec' . DS,
                    ],
                    'output' => [
                        'help' => 'Specify output file path',
                        'short' => 'o',
                        'required' => false,
                        'default' => Plugin::path('BEdita/API') . 'spec' . DS . 'be4.yaml',
                    ],
                ],
            ],
        ]);

        return $parser;
    }

    /**
     * Save YAML OpenAPI 2 spec file.
     *
     * @return void
     */
    public function generate()
    {
        $output = new File($this->param('output'));
        $dir = new Folder($this->param('spec'));
        $files = $dir->find('.*\.yaml', true);
        $result = ['paths' => [], 'definitions' => []];
        foreach ($files as $file) {
            $file = new File($dir->realpath($file));
            if ($file->path === $output->path) {
                continue;
            }

            $data = Yaml::parse($file->read());
            $result = array_merge($data, $result);
            if (!empty($data['paths'])) {
                $result['paths'] += $data['paths'];
            }
            if (!empty($data['definitions'])) {
                $result['definitions'] = array_merge($data['definitions'], $result['definitions']);
            }
        }
        $result = Yaml::dump($result, 2, 4, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);

        $this->createFile($output->path, $result);
    }
}
