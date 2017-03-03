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

use Cake\ORM\Association;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Trait for exposing useful properties required for JSON API response formatting at the entity level.
 *
 * @since 4.0.0
 */
trait JsonApiTrait
{

    /**
     * Getter for entity's hidden properties.
     *
     * @return string[]
     */
    abstract public function getHidden();

    /**
     * Getter for source model registry alias.
     *
     * @return string
     */
    abstract public function getSource();

    /**
     * Magic getter for `type` property.
     *
     * @return string
     */
    protected function _getType()
    {
        return TableRegistry::get($this->getSource())->getTable();
    }

    /**
     * Magic getter for `relationships` property.
     *
     * The `relationships` property is supposed to provide a list of available
     * relationships for this entity.
     *
     * @return string[]
     */
    protected function _getRelationships()
    {
        return static::listAssociations(TableRegistry::get($this->getSource()), $this->getHidden());
    }

    /**
     * List all available relationships for a model.
     *
     * @param \Cake\ORM\Table $Table Table object instance.
     * @param array $hidden List of relationships to be excluded.
     * @return array
     */
    protected static function listAssociations(Table $Table, array $hidden = [])
    {
        $associations = $Table->associations();
        $btmJunctionAliases = array_map(
            function (BelongsToMany $val) {
                return $val->junction()->getAlias();
            },
            $associations->type('BelongsToMany')
        );

        $relationships = [];
        foreach ($associations as $association) {
            list(, $associationType) = namespaceSplit(get_class($association));
            $name = $association->property();
            if (!($association instanceof Association) ||
                $associationType === 'ExtensionOf' ||
                in_array($name, $hidden) ||
                ($associationType === 'HasMany' && in_array($association->getTarget()->getAlias(), $btmJunctionAliases))
            ) {
                continue;
            }

            $relationships[] = Inflector::dasherize($name);
        }

        return $relationships;
    }
}
