<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Middleware;

use Cake\Http\Middleware\BodyParserMiddleware as CakeBodyParserMiddleware;
use Closure;

/**
 * BodyParser middleware
 */
class BodyParserMiddleware extends CakeBodyParserMiddleware
{
    /**
     * {@inheritDoc}
     *
     * Add JSON API content type parser
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $options += ['json' => true, 'form' => true];
        if ($options['json']) {
            $this->addParser(
                ['application/vnd.api+json'],
                Closure::fromCallable([$this, 'decodeJson'])
            );
        }
        if ($options['form']) {
            $this->addParser(
                ['application/x-www-form-urlencoded'],
                Closure::fromCallable([$this, 'decodeForm'])
            );
        }
    }

    /**
     * Decode `nto an array.
     *
     * @param string $body The request body to decode
     * @return array
     */
    protected function decodeForm(string $body): array
    {
        if ($body === '') {
            return [];
        }
        $result = [];
        parse_str(urldecode($body), $result);

        return $result;
    }
}
