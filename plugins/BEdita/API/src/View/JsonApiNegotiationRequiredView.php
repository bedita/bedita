<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\View;

use Cake\View\NegotiationRequiredView;

/**
 * A view class that responds to any content-type and can be used to create
 * a 406 status code response with JSON API error body.
 *
 * @since 6.0.0
 */
class JsonApiNegotiationRequiredView extends NegotiationRequiredView
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $response = $this->getResponse()->withType('application/json');
        $this->setResponse($response);
    }

    /**
     * List of acceptable content types
     *
     * @return array
     */
    protected function acceptableTypes(): array
    {
        return [
            JsonApiView::contentType(),
            JsonApiFallbackView::contentType(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function render(?string $template = null, $layout = null): string
    {
        return json_encode([
            'error' => [
                'status' => '406',
                'title' => 'Not Acceptable',
                'detail' => __d('bedita', 'Content types supported are: {0}', implode(', ', $this->acceptableTypes())),
            ],
        ]);
    }
}
