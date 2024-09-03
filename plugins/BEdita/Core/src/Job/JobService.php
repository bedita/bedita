<?php
declare(strict_types=1);

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

namespace BEdita\Core\Job;

/**
 * Interface to run async jobs services.
 *
 * @since 4.0.0
 */
interface JobService
{
    /**
     * Run an async job using $payload input data and optional $options.
     *
     * It can return:
     * - a boolean i.e. `true` on success, `false` on failure
     * - an array with keys:
     *   - 'success' (required) => `true` on success, `false` on failure
     *   - 'messages' (optional) => array of messages
     *
     * @param array $payload Input data for running this job.
     * @param array $options Options for running this job.
     * @return bool|array
     */
    public function run(array $payload, array $options = []);
}
