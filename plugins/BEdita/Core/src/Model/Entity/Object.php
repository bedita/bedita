<?php
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

use Cake\ORM\Entity;

/**
 * Object Entity.
 *
 * @property int $id
 * @property int $object_type_id
 * @property \BEdita\Core\Model\Entity\ObjectType $object_type
 * @property string $status
 * @property string $uname
 * @property bool $locked
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $published
 * @property string $title
 * @property string $description
 * @property string $body
 * @property string $extra
 * @property string $lang
 * @property int $created_by
 * @property int $modified_by
 * @property \Cake\I18n\Time $publish_start
 * @property \Cake\I18n\Time $publish_end
 *
 * @since 4.0.0
 */
class Object extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'locked' => false,
        'created' => false,
        'modified' => false,
        'published' => false,
        'created_by' => false,
        'modified_by' => false
    ];

    public function __construct(array $properties = [], array $options = [])
    {
        $this->initialize();
        parent::__construct($properties, $options);
    }

    public function initialize()
    {
        // debug($this->_accessible);
    }
}
