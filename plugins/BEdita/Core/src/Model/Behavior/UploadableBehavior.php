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

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Filesystem\FilesystemRegistry;
use Cake\Event\Event;
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
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'files' => [
            [
                'path' => 'path',
                'contents' => 'contents',
            ],
        ],
    ];

    /**
     * Write file contents.
     *
     * @param \League\Flysystem\MountManager $manager Mount manager.
     * @param string $path File path.
     * @param mixed $contents File contents.
     * @return bool
     */
    protected function write(MountManager $manager, $path, $contents)
    {
        if ($contents instanceof StreamInterface) {
            $contents = $contents->detach();
        }

        if (is_resource($contents)) {
            return $manager->putStream($path, $contents);
        }

        return $manager->put($path, $contents);
    }

    /**
     * Process upload of a single file.
     *
     * @param \Cake\ORM\Entity $entity Entity.
     * @param string $pathField Name of field in which path is stored.
     * @param string $contentsField Name of field in which contents are stored.
     * @return bool
     */
    protected function processUpload(Entity $entity, $pathField, $contentsField)
    {
        if (!$entity->isDirty($pathField) && !$entity->isDirty($contentsField)) {
            // Nothing to do.
            return true;
        }

        $manager = FilesystemRegistry::getMountManager();
        $path = $entity->get($pathField);
        $originalPath = $entity->getOriginal($pathField);
        if ($entity->isDirty($pathField) && $originalPath !== $path) {
            if ($entity->isDirty($contentsField)) {
                // Delete and re-upload.
                return $manager->delete($originalPath) && $this->write($manager, $path, $entity->get($contentsField));
            }

            // Move to new location.
            return $manager->move($originalPath, $path);
        }

        // Updated contents.
        return $this->write($manager, $path, $entity->get($contentsField));
    }

    /**
     * Process delete of a single file.
     *
     * @param \Cake\ORM\Entity $entity Entity.
     * @param string $pathField Name of field in which path is stored.
     * @return bool
     */
    protected function processDelete(Entity $entity, $pathField)
    {
        $manager = FilesystemRegistry::getMountManager();
        $path = $entity->get($pathField);

        return !$manager->has($path) || $manager->delete($path);
    }

    /**
     * Process upload.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\ORM\Entity $entity Entity.
     * @return void
     */
    public function afterSave(Event $event, Entity $entity)
    {
        foreach ($this->getConfig('files') as $file) {
            $this->processUpload($entity, $file['path'], $file['contents']);
        }
    }

    /**
     * Process delete.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\ORM\Entity $entity Entity.
     * @return void
     */
    public function afterDelete(Event $event, Entity $entity)
    {
        foreach ($this->getConfig('files') as $file) {
            $this->processDelete($entity, $file['path']);
        }
    }
}
