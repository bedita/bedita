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

namespace BEdita\Core\Model\Validation;

/**
 * Validator for media.
 *
 * @since 4.0.0
 */
class MediaValidator extends ObjectsValidator
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();

        $this
            ->allowEmptyString('name')

            ->ascii('provider')
            ->allowEmptyString('provider')

            ->notEmptyString('provider_uid', null, function ($context) {
                return !empty($context['data']['provider']); // Required if provider is set.
            })

            // Use `add` instead of `urlWithProtocol` to preserve rule name.
            ->add('provider_url', 'url', [
                'rule' => ['url', true],
            ])
            ->allowEmptyString('provider_url')

            // Use `add` instead of `urlWithProtocol` to preserve rule name.
            ->add('provider_thumbnail', 'url', [
                'rule' => ['url', true],
            ])
            ->allowEmptyString('provider_thumbnail');
    }
}
