<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Entity;

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Config Entity.
 *
 * @property string $name
 * @property string $context
 * @property string $content
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $application_id
 *
 * @property \BEdita\Core\Model\Entity\Application|null $application
 * @since 4.0.0
 */
class Config extends Entity implements JsonApiSerializable
{
    use JsonApiAdminTrait;

    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => true,
        'created' => false,
        'modified' => false,
    ];

    /**
     * Setter for `application` virtual property.
     *
     * @param string|null $application The application to set
     * @return string|null
     */
    protected function _setApplication(?string $application): ?string
    {
        if ($application === null) {
            $this->application_id = null;

            return null;
        }

        $table = TableRegistry::getTableLocator()->get('Applications');
        $this->application_id = $table
            ->find('list', ['valueField' => 'id'])
            ->where([
                $table->aliasField('name') => $application,
            ])
            ->firstOrFail();

        return $application;
    }
}
