<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Job\Service;

use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Job\JobService;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Service to handle asynchronous generation of thumbnails.
 *
 * @since 4.0.0
 */
class ThumbnailService implements JobService
{

    /**
     * {@inheritDoc}
     */
    public function run(array $payload, array $options = [])
    {
        try {
            /** @var \BEdita\Core\Model\Table\StreamsTable $table */
            $table = TableRegistry::get('Streams');
            $stream = $table->get(Hash::get($payload, 'uuid'));

            $generator = Thumbnail::getGenerator(Hash::get($payload, 'generator', 'default'));
            $generator->generate($stream, (array)Hash::get($payload, 'options'));

            return true;
        } catch (RecordNotFoundException $e) {
            // Stream not found, mark job as complete anyway.
            return true;
        } catch (\Exception $e) {
            // Another error occurred. Log the error, and mark job as failed.
            Log::error($e);

            return false;
        }
    }
}
