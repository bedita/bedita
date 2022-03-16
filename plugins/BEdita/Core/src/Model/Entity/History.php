<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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

/**
 * History Entity
 *
 * @property int $id
 * @property string $resource_id
 * @property string $resource_type
 * @property \Cake\I18n\Time $created
 * @property int $user_id
 * @property int $application_id
 * @property string|null $user_action
 * @property array|null $changed
 *
 * @property \BEdita\Core\Model\Entity\User $user
 * @property \BEdita\Core\Model\Entity\Application $application
 */
class History extends Entity implements JsonApiSerializable
{
    use JsonApiTrait;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => false,
    ];
}
