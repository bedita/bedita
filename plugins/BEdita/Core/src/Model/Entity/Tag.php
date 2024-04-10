<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use Cake\Utility\Hash;

/**
 * Tag Entity
 *
 * @property int $id
 * @property string $name
 * @property array|null $labels
 * @property bool $enabled
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \BEdita\Core\Model\Entity\ObjectTag[] $object_tags
 */
class Tag extends Entity implements JsonApiSerializable
{
    use JsonApiModelTrait;

    /**
     * @inheritDoc
     */
    protected $_accessible = [
        '*' => true,
        'created' => false,
        'modified' => false,
    ];

    /**
     * @inheritDoc
     */
    protected $_hidden = [
        '_joinData',
    ];

    /**
     * @inheritDoc
     */
    protected $_virtual = [
        'label',
    ];

    /**
     * Getter for `label` virtual property.
     *
     * @return string|null
     */
    protected function _getLabel(): ?string
    {
        $labels = is_array($this->labels) ? $this->labels : (array)json_decode((string)$this->labels, true);
        $label = (string)Hash::get($labels, 'default');

        return empty($label) ? null : $label;
    }

    /**
     * Setter for `label` virtual property.
     *
     * @param string $label Label to set.
     * @return void
     */
    protected function _setLabel(string $label): void
    {
        $this->labels = array_merge((array)$this->labels, ['default' => $label]);
    }
}
