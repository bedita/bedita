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
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Symfony\Component\Yaml\Yaml;

/**
 * Generate OpenAPI 2/3 YAML file merging multiple YAML files in /spec folder
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
            'help' => 'Generate global YAML OpenAPI 2/3 file.',
            'parser' => [
                'description' => [
                    'This command to generates a single YAML OpenAPI 2/3 spec file.',
                    'File is built merging yaml files in /spec folder.',
                ],
                'options' => [
                    'subdir' => [
                        'help' => 'Subdirectory of the plugin to search for documentation files',
                        'short' => 's',
                        'required' => false,
                        'default' => 'spec/',
                    ],
                    'output' => [
                        'help' => 'Specify output file name',
                        'short' => 'o',
                        'required' => false,
                        'default' => 'be4.yaml',
                    ],
                ],
            ],
        ]);

        return $parser;
    }

    /**
     * Save YAML OpenAPI 2/3 spec file.
     *
     * @return void
     */
    public function generate()
    {
        $pluginLoaded = Plugin::loaded();

        foreach ($pluginLoaded as $plugin) {
            $path = $this->generatePluginDocs($plugin, $this->param('subdir'), $this->param('output'));

            if (!$path) {
                $this->warn("Could not generate docs for '$plugin' plugin");
                continue;
            }

            $this->success("Generated documentation for '$plugin' plugin in $path");
        }
    }

    /**
     * Generate a single documentation file for a plugin.
     *
     * Note that the output file will be written in the source directory (default to 'spec/' in the plugin directory)
     *
     * @param string $pluginName Name of the plugin
     * @param string $docSubDir Subdirectory of the plugin to search for documentation files (default to 'spec/')
     * @param string $outputFile Output documentation file name (default to 'be4.yaml')
     * @return bool|string The output file absolute path, or `false` on failure
     */
    protected function generatePluginDocs($pluginName, $docSubDir = 'spec' . DS, $outputFile = 'be4.yaml')
    {
        $files = $this->getPluginDocFiles($pluginName, $docSubDir);

        if (empty($files)) {
            return false;
        }

        $outputDir = dirname($files[0]);
        $outputPath = rtrim($outputDir, DS) . DS . $outputFile;
        $openApiVersion = $this->getFileOpenApiVersion($files[0]);
        echo "\nGot API version $openApiVersion for file $files[0]\n";
        $mergeFunction = "mergeDeepOpenApi$openApiVersion";
        $result = ['paths' => []];

        foreach ($files as $filePath) {
            $file = new File($filePath);

            if ($file->path === $outputPath) {
                continue;
            }

            $data = Yaml::parse($file->read());
            $result = $this->{$mergeFunction}($data, $result);
        }

        $result = Yaml::dump($result, 2, 4, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);

        if (!$this->createFile($outputPath, $result)) {
            return false;
        }

        return $outputPath;
    }

    /**
     * Get a list of plugin documentation files.
     *
     * @param string $pluginName Name of the plugin
     * @param string $docSubDir Subdirectory of the plugin to search for documentation files (default to 'spec/')
     * @return string[] An array of absolute paths to files. Empty array if none found
     */
    protected function getPluginDocFiles($pluginName, $docSubDir = 'spec' . DS)
    {
        $path = Plugin::path($pluginName);
        $dir = new Folder(rtrim($path, DS) . DS . $docSubDir);
        $files = $dir->find('.*\.(yaml|yml)', true);

        return array_reduce($files, function ($result, $file) use ($dir) {
            $result[] = $dir->realpath($file);

            return $result;
        }, []);
    }

    /**
     * Find OpenAPI version from a file.
     *
     * @param string $filePath Absolute path of the file
     * @return bool|string The API version, or `false` if unknown
     */
    protected function getFileOpenApiVersion($filePath)
    {
        $file = new File($filePath);
        $data = Yaml::parse($file->read());

        if (!empty($data['openapi'])) {
            return explode('.', $data['openapi'])[0];
        }

        if (!empty($data['swagger'])) {
            return explode('.', $data['swagger'])[0];
        }

        return false;
    }

    /**
     * Deep merge YAML spec file for OpenAPI 2.
     *
     * @param array $data    The new file data
     * @param array $current The current data
     * @return array
     */
    protected function mergeDeepOpenApi2($data, $current)
    {
        $current = array_merge($data, $current);

        if (!empty($data['paths'])) {
            $current['paths'] += $data['paths'];
        }

        if (!empty($data['definitions'])) {
            if (!array_key_exists('definitions', $current) || $current['definitions'] === null) {
                $current['definitions'] = [];
            }

            $current['definitions'] = array_merge($data['definitions'], $current['definitions']);
        }

        return $current;
    }

    /**
     * Deep merge YAML spec file for OpenAPI 3.
     *
     * @param array $data    The new file data
     * @param array $current The current data
     * @return array
     */
    protected function mergeDeepOpenApi3($data, $current)
    {
        $current = array_merge($data, $current);

        if (!empty($data['paths'])) {
            $current['paths'] += $data['paths'];
        }

        if (!empty($data['components'])) {
            if (!array_key_exists('components', $current)) {
                $current['components'] = [];
            }

            $current['components'] = array_merge($data['components'], $current['components']);

            if (!empty($data['components']['schemas'])) {
                $current['components']['schemas'] = array_merge($data['components']['schemas'], $current['components']['schemas']);
            }
        }

        return $current;
    }
}
