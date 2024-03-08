<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Event;

use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Model\Entity\Stream;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\LogTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

/**
 * Event listener for image thumbs events.
 */
class ImageThumbsHandler implements EventListenerInterface
{
    use LogTrait;
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    public function implementedEvents(): array
    {
        return [
            'Associated.afterSave' => 'afterSaveAssociated',
        ];
    }

    /**
     * Handle 'Associated.afterSave' and create thumbs using presets on streams
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @return void
     */
    public function afterSaveAssociated(Event $event): void
    {
        $data = $event->getData();
        $stream = Hash::get($data, 'entity');
        if (!$stream instanceof Stream) {
            return;
        }
        $image = Hash::get($data, 'relatedEntities.0');
        $type = empty($image) ? null : (string)$image->get('type');
        if ($type !== 'images') {
            return;
        }
        $presets = (array)Configure::read('Thumbnails.presets');
        $this->updateThumbs($image, $stream, $presets);
    }

    /**
     * Update all preset thumbnails of a stream
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $image The image object.
     * @param \BEdita\Core\Model\Entity\Stream $stream The stream resource.
     * @param array $presets Preset configurations.
     * @return void
     */
    public function updateThumbs(ObjectEntity $image, Stream $stream, array $presets): void
    {
        $result = [];
        foreach ($presets as $key => $value) {
            $info = Thumbnail::get($stream, $value);
            unset($info['ready']);
            $result[$key] = $info;
        }
        $extra = (array)$image->get('extra');
        $extra['thumbs'] = $result;
        $image->set('extra', $extra);
        $this->fetchTable('Images')->saveOrFail($image);
    }
}
