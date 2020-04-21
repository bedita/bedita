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
use Cake\Core\Configure;

/**
 * Trait with methods to setup and cleanup filesystem in test cases
 */
trait TestFilesystemTrait
{
    /**
     * List of files to keep in test filesystem, and their contents.
     *
     * @var \Cake\Collection\Collection
     */
    protected $keep = [];

    /**
     * Setup test filesystem.
     * Call this method in test `setUp()`
     * or before tests involving filesystem.
     *
     * @return void
     */
    protected function filesystemSetup(): void
    {
        FilesystemRegistry::setConfig(Configure::read('Filesystem'));

        $mountManager = FilesystemRegistry::getMountManager();
        $this->keep = collection($mountManager->listContents('default://'))
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
     * Setup test filesystem.
     * Call this method in test `tearDown()`
     * or after tests involving filesystem.
     *
     * @return void
     */
    protected function filesystemCleanup(): void
    {
        // Cleanup test filesystem.
        $mountManager = FilesystemRegistry::getMountManager();
        $keep = $this->keep
            ->each(function (array $object) use ($mountManager) {
                $mountManager->putStream($object['path'], $object['contents']);
            })
            ->map(function (array $object) {
                return $object['path'];
            })
            ->toList();
        collection($mountManager->listContents('default://'))
            ->map(function (array $object) {
                return sprintf('%s://%s', $object['filesystem'], $object['path']);
            })
            ->reject(function ($uri) use ($keep) {
                return in_array($uri, $keep);
            })
            ->each([$mountManager, 'delete']);

        FilesystemRegistry::dropAll();
    }
}
