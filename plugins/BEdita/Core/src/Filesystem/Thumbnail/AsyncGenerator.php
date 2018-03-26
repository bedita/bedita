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

namespace BEdita\Core\Filesystem\Thumbnail;

use BEdita\Core\Filesystem\Thumbnail;
use BEdita\Core\Filesystem\ThumbnailGenerator;
use BEdita\Core\Model\Entity\Stream;
use Cake\ORM\TableRegistry;

/**
 * Asynchronous thumbnail generator.
 *
 * @since 4.0.0
 */
class AsyncGenerator extends ThumbnailGenerator
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'baseGenerator' => 'default',
        'service' => 'thumbnail',
        'max_attempts' => 2,
    ];

    /**
     * Get generator used for actually creating the thumbnail.
     *
     * @return \BEdita\Core\Filesystem\GeneratorInterface
     */
    protected function getBaseGenerator()
    {
        return Thumbnail::getGenerator($this->getConfig('baseGenerator'));
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(Stream $stream, array $options = [])
    {
        return $this->getBaseGenerator()->getUrl($stream, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function generate(Stream $stream, array $options = [])
    {
        /* @var \BEdita\Core\Model\Table\AsyncJobsTable $table */
        $table = TableRegistry::get('AsyncJobs');

        $asyncJob = $table->newEntity();
        $asyncJob->service = $this->getConfig('service');
        $asyncJob->max_attempts = $this->getConfig('max_attempts');
        if ($this->getConfig('priority') !== null) {
            $asyncJob->priority = $this->getConfig('priority');
        }

        $asyncJob->payload = [
            'uuid' => $stream->uuid,
            'generator' => $this->getConfig('baseGenerator'),
            'options' => $options,
        ];

        $table->saveOrFail($asyncJob);

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Stream $stream, array $options = [])
    {
        return $this->getBaseGenerator()->exists($stream, $options);
    }

    /**
     * {@inheritDoc}
     *
     * This method does nothing: any asynchronous job that might be still pending will be marked as complete
     * as soon as it is attempted.
     *
     * @see \BEdita\Core\Job\Service\ThumbnailService::run()
     *
     * @codeCoverageIgnore
     */
    public function delete(Stream $stream)
    {
        // Nothing to see here. Go away!
    }
}
