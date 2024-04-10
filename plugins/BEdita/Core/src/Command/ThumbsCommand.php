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
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenTime;

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
        return parent::buildOptionParser($parser)
            ->addOption('id', [
                'short' => 'i',
                'help' => 'Image ID',
                'multiple' => true,
            ])
            ->addOption('start-at', [
                'help' => 'ID to start with, for resuming an interrupted operation',
            ])
            ->addOption('preset', [
                'short' => 'p',
                'help' => 'Preset to generate thumbnail for',
                'multiple' => true,
                'choices' => static::availablePresets(),
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $handler = new ImageThumbsHandler();
        $ids = (array)$args->getOption('id');
        $startAt = filter_var(
            $args->getOption('start-at'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1], 'flags' => FILTER_NULL_ON_FAILURE],
        );
        $presets = (array)$args->getOption('preset') ?: ThumbsCommand::availablePresets();

        $io->out(sprintf(
            '=====> Operation started at <info>%s</info>, using presets: %s',
            FrozenTime::now()->toIso8601String(),
            implode(', ', array_map(fn (string $preset) => sprintf('<comment>%s</comment>', $preset), $presets)),
        ));

        $success = $failed = 0;
        foreach ($this->imagesIterator($ids, $startAt) as $image) {
            $stream = $image->streams[0];
            if (!$stream) {
                $io->warning(sprintf('No stream found for image #%d', $image->id));

                continue;
            }

            try {
                $io->verbose(sprintf('=====> Processing thumbs for image #%d... ', $image->id), 0);
                $handler->updateThumbs($image, $stream, $presets);
                $io->verbose('<success>DONE</success>');
                $success++;
            } catch (\Exception $e) {
                $io->verbose('<error>FAIL</error>');
                $failed++;
            }
        }

        $io->out(sprintf(
            '=====> Operation completed at <info>%s</info>: <success>%d</success> OK, <error>%d</error> failed',
            FrozenTime::now()->toIso8601String(),
            $success,
            $failed,
        ));
    }

    /**
     * Retrieve thumbnail presets without 'async' generators
     *
     * @return string[]
     */
    protected static function availablePresets(): array
    {
        return array_keys(array_filter((array)Configure::read('Thumbnails.presets'), fn (array $preset) => !isset($preset['generator'])));
    }

    /**
     * Iterate through all images.
     *
     * @param string[] $ids IDs to filter by.
     * @param int|null $startAt ID to start with, for resuming an interrupted operation.
     * @return \Generator<\BEdita\Core\Model\Entity\Media>
     */
    protected function imagesIterator(array $ids, ?int $startAt): \Generator
    {
        $table = $this->fetchTable('Images');
        $id = $startAt ?? 0;
        $idField = $table->aliasField('id');

        $query = $table->find('type', ['images'])
            ->matching('Streams')
            ->contain('Streams');
        if (!empty($ids)) {
            $query = $query->where(fn (QueryExpression $exp): QueryExpression => $exp->in($idField, $ids));
        }

        do {
            $results = $query->cleanCopy()
                ->where(fn (QueryExpression $exp): QueryExpression => $exp->gt('id', $id))
                ->limit(100)
                ->orderAsc($idField)
                ->all();

            yield from $results;

            $id = $results->extract('id')->last();
        } while (!$results->isEmpty());
    }
}
