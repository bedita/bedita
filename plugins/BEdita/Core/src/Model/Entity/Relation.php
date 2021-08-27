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

namespace BEdita\Core\Model\Entity;

use BEdita\Core\Utility\JsonApiSerializable;
use Cake\ORM\Entity;
use Cake\Utility\Inflector;

/**
 * Relation Entity
 *
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $inverse_name
 * @property string $inverse_label
 * @property string $description
 * @property array $params
 * @property string $alias
 * @property string $inverse_alias
 *
 * @property \BEdita\Core\Model\Entity\ObjectRelation[] $object_relations
 * @property \BEdita\Core\Model\Entity\ObjectType[] $left_object_types
 * @property \BEdita\Core\Model\Entity\ObjectType[] $right_object_types
 */
class Relation extends Entity implements JsonApiSerializable
{
    use JsonApiModelTrait;

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'name' => true,
        'label' => true,
        'inverse_name' => true,
        'inverse_label' => true,
        'description' => true,
        'params' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'object_relations',
    ];

    /**
     * Default JSON schema.
     *
     * @var string
     */
    const DEFAULT_SCHEMA = 'http://json-schema.org/draft-06/schema#';

    /**
     * Magic setter for relation name.
     *
     * @param string $name Relation name.
     * @return string
     */
    protected function _setName($name)
    {
        return Inflector::underscore($name);
    }

    /**
     * Magic setter for relation inverse name.
     *
     * @param string $inverseName Relation inverse name.
     * @return string
     */
    protected function _setInverseName($inverseName)
    {
        return Inflector::underscore($inverseName);
    }

    /**
     * Magic getter for relation alias.
     *
     * @return string
     */
    protected function _getAlias()
    {
        return Inflector::camelize($this->name);
    }

    /**
     * Magic getter for relation inverse alias.
     *
     * @return string
     */
    protected function _getInverseAlias()
    {
        return Inflector::camelize($this->inverse_name);
    }

    /**
     * Magic setter for params.
     *
     * @param array $params Relation params.
     * @return array
     */
    protected function _setParams($params)
    {
        if (is_array($params) && !empty($params)) {
            $params = array_merge([
                'definitions' => new \stdClass(),
                '$schema' => self::DEFAULT_SCHEMA,
                'type' => 'object',
            ], $params);
        }

        return $params;
    }
}
