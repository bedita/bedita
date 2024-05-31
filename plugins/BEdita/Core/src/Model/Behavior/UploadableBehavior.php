<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017-2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use League\Flysystem\MountManager;
use Psr\Http\Message\StreamInterface;

/**
 * Upload files.
 *
 * @since 4.0.0
 */
class UploadableBehavior extends Behavior
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'files' => [
            [
                'path' => 'path',
                'contents' => 'contents',
            ],
        ],
        'implementedMethods' => [
            'copyFiles' => 'copyFiles',
        ],
    ];

    /**
     * Write file contents.
     *
     * @param \League\Flysystem\MountManager $manager Mount manager.
     * @param string $path File path.
     * @param mixed $contents File contents.
     * @return void
     */
    protected function write(MountManager $manager, string $path, $contents): void
    {
        if ($contents instanceof StreamInterface) {
            $contents = $contents->detach();
        }

        if (is_resource($contents)) {
            $manager->writeStream($path, $contents);

            return;
        }

        $manager->write($path, $contents);
    }

    /**
     * Process upload of a single file.
     *
     * @param \Cake\ORM\Entity $entity Entity.
     * @param string $pathField Name of field in which path is stored.
     * @param string $contentsField Name of field in which contents are stored.
     * @return void
     */
    protected function processUpload(Entity $entity, string $pathField, string $contentsField): void
    {
        $manager = FilesystemRegistry::getMountManager();
        if (
            (!$entity->isDirty($pathField) || !$manager->fileExists($entity->getOriginal($pathField)))
            && !$entity->isDirty($contentsField)
        ) {
            // Nothing to do.
            return;
        }

        $path = $entity->get($pathField);
        $originalPath = $entity->getOriginal($pathField);
        if ($entity->isDirty($pathField) && $originalPath !== $path) {
            if ($entity->isDirty($contentsField)) {
                // Delete and re-upload.
                $manager->delete($originalPath);
                $this->write($manager, $path, $entity->get($contentsField));

                return;
            }
            // Move to new location.
            $manager->move($originalPath, $path);

            return;
        }

        // Updated contents.
        $this->write($manager, $path, $entity->get($contentsField));
    }

    /**
     * Set `private` file visibility on `private_url`
     *
     * @param \Cake\ORM\Entity $entity Entity.
     * @param string $pathField Name of field in which path is stored.
     * @return void
     */
    protected function setVisibility(Entity $entity, string $pathField): void
    {
        if (!$entity->get('private_url')) {
            return;
        }
        $path = $entity->get($pathField);
        $manager = FilesystemRegistry::getMountManager();
        $manager->setVisibility($path, 'private');
    }

    /**
     * Process delete of a single file.
     *
     * @param \Cake\ORM\Entity $entity Entity.
     * @param string $pathField Name of field in which path is stored.
     * @return bool
     */
    protected function processDelete(Entity $entity, $pathField): bool
    {
        $manager = FilesystemRegistry::getMountManager();
        $path = $entity->get($pathField);

        return !$manager->fileExists($path) || $manager->delete($path);
    }

    /**
     * Process upload.
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \Cake\ORM\Entity $entity Entity.
     * @return void
     */
    public function afterSave(EventInterface $event, Entity $entity): void
    {
        foreach ($this->getConfig('files') as $file) {
            $this->processUpload($entity, $file['path'], $file['contents']);
            $this->setVisibility($entity, $file['path']);
        }
    }

    /**
     * Process delete.
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \Cake\ORM\Entity $entity Entity.
     * @return void
     */
    public function afterDelete(EventInterface $event, Entity $entity): void
    {
        foreach ($this->getConfig('files') as $file) {
            $this->processDelete($entity, $file['path']);
        }
    }

    /**
     * Copy files from an entity to another.
     *
     * @param \Cake\ORM\Entity $src Source entity. It must have path fields set and referenced files must exist.
     * @param \Cake\ORM\Entity $dest Destination entity. It must have path fields set.
     * @return void
     * @throws \League\Flysystem\FilesystemException
     */
    public function copyFiles(Entity $src, Entity $dest): void
    {
        $manager = FilesystemRegistry::getMountManager();
        foreach ($this->getConfig('files') as $file) {
            $srcPath = $src->get($file['path']);
            $destPath = $dest->get($file['path']);

            $manager->copy($srcPath, $destPath);
        }
    }
}
