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
namespace BEdita\Core\Model\Entity;

/**
 * {@inheritDoc}
 *
 * App Config Entity.
 *
 * @since 5.0.0
 * @property int $id
 * @property string $name
 * @property string $context
 * @property string $content
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property int|null $application_id
 * @property \BEdita\Core\Model\Entity\Application|null $application
 */
class AppConfig extends Config
{
    /**
     * @inheritDoc
     */
    protected array $_hidden = [
        'application_id',
        'context',
    ];
}
