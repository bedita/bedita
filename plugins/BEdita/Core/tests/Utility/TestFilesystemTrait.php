<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\Utility;

use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Collection\CollectionInterface;
use Cake\Core\Configure;

/**
 * Trait with methods to setup and cleanup filesystem in test cases
 */
trait TestFilesystemTrait
{
    /**
     * List of files to keep in test filesystem, and their contents.
     *
     * @var array
     */
    protected $keep = [
        'default://' => [],
        'thumbnails://' => [],
    ];

    /**
     * Use recursion in directories.
     *
     * @var array
     */
    protected $recursive = [
        'default://' => false,
        'thumbnails://' => true,
    ];

    /**
     * Setup test filesystem.
     * Call this method in test `setUp()`
     * or before tests involving filesystem.
     *
     * @param bool $default Setup `default://`filesystem.
     * @param bool $thumbnails Setup `thumbnails://`filesystem.
     * @return void
     */
    protected function filesystemSetup(bool $default = true, bool $thumbnails = false): void
    {
        FilesystemRegistry::setConfig(Configure::read('Filesystem'));

        if ($default) {
            $this->keep['default://'] = $this->keepCollection('default://');
        }
        if ($thumbnails) {
            $this->keep['thumbnails://'] = $this->keepCollection('thumbnails://');
        }
    }

    /**
     * Retrieve collection of file contents and paths to keep after test execution
     *
     * @param string $directory Directory content to list.
     * @return \Cake\Collection\CollectionInterface
     */
    protected function keepCollection(string $directory): CollectionInterface
    {
        $mountManager = FilesystemRegistry::getMountManager();
        $recursive = $this->recursive[$directory];

        return collection($mountManager->listContents($directory, $recursive))
                ->reject(function (array $object) {
                    return $object['type'] === 'dir';
                })
                ->map(function (array $object) use ($mountManager) {
                    $path = sprintf('%s://%s', $object['filesystem'], $object['path']);
                    $contents = fopen('php://memory', 'wb+');
                    fwrite($contents, $mountManager->read($path));
                    fseek($contents, 0);

                    return compact('contents', 'path');
                })
                ->compile();
    }

    /**
     * Restore original filesystem content amd remove new files.
     * Call this method in test `tearDown()`
     * or after tests involving filesystem.
     *
     * @return void
     */
    protected function filesystemRestore(): void
    {
        foreach ($this->keep as $directory => $items) {
            if (!empty($items)) {
                $keep = $this->restoreFiles($directory);
                $this->removeFiles($directory, $keep);
            }
        }

        FilesystemRegistry::dropAll();
    }

    /**
     * Restore file contents in a directory
     *
     * @param string $directory The Directory.
     * @return array
     */
    protected function restoreFiles(string $directory): array
    {
        $mountManager = FilesystemRegistry::getMountManager();

        return $this->keep[$directory]
            ->each(function (array $object) use ($mountManager) {
                $mountManager->putStream($object['path'], $object['contents']);
            })
            ->map(function (array $object) {
                return $object['path'];
            })
            ->toList();
    }

    /**
     * Remove new files from directory not listed in $keep.
     *
     * @param string $directory Directory content to list.
     * @param array $keep Files to keep.
     * @return void
     */
    protected function removeFiles(string $directory, array $keep): void
    {
        $mountManager = FilesystemRegistry::getMountManager();
        $recursive = $this->recursive[$directory];

        collection($mountManager->listContents($directory, $recursive))
            ->map(function (array $object) {
                return sprintf('%s://%s', $object['filesystem'], $object['path']);
            })
            ->reject(function ($uri) use ($keep) {
                return in_array($uri, $keep);
            })
            ->each([$mountManager, 'delete']);
    }
}
