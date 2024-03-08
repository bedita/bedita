<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Channelweb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Command;

use BEdita\Core\Event\ImageThumbsHandler;
use BEdita\Core\Model\Entity\Stream;
use Cake\Collection\Collection;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Command to update/create thumbnails for all images.
 */
class ThumbsCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->addOption('id', ['help' => 'Image id']);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('=====> <info>Update image thumbs...</info>');

        $Images = $this->fetchTable('Images');
        $query = $Images->find()
            ->contain('Streams');

        if ($args->getOption('id')) {
            $query = $query->where(['id' => $args->getOption('id')]);
        }

        $handler = new ImageThumbsHandler();
        $presets = $this->presets();

        $entities = $query->toArray();
        $count = 0;
        foreach ($entities as $image) {
            $stream = Hash::get($image, 'streams.0');
            if ($stream instanceof Stream) {
                $handler->updateThumbs($image, $stream, $presets);
                $count++;
            }
        }
        $io->out('=====> <success>Thumbs updated</success>');
        $io->out(sprintf('=====> <info>Updated %d images</info>', $count));
    }

    /**
     * Retrieve thumbnail presets without 'async' generators
     *
     * @return array
     */
    protected function presets(): array
    {
        $collection = new Collection((array)Configure::read('Thumbnails.presets'));

        return $collection->map(function (array $preset) {
            unset($preset['generator']); // remove 'async' generators

            return $preset;
        })->toArray();
    }
}
