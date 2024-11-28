<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Utility;

use Cake\Database\Schema\TableSchemaInterface;

class Schema
{
    /**
     * Get primary fields from schema.
     *
     * @param \Cake\Database\Schema\TableSchemaInterface $schema Table schema.
     * @return array
     */
    public static function getPrimaryFields(TableSchemaInterface $schema): array
    {
        $fields = [];
        foreach ($schema->constraints() as $name) {
            $constraint = $schema->getConstraint($name);
            if ($constraint['type'] === 'primary' && count($constraint['columns']) === 1) {
                $fields = array_merge($fields, $constraint['columns']);
            }
        }

        return $fields;
    }

    /**
     * Get unique fields from schema.
     *
     * @param \Cake\Database\Schema\TableSchemaInterface $schema Table schema.
     * @return array
     */
    public static function getUniqueFields(TableSchemaInterface $schema): array
    {
        $fields = [];
        foreach ($schema->constraints() as $name) {
            $constraint = $schema->getConstraint($name);
            if ($constraint['type'] === 'unique' && count($constraint['columns']) === 1) {
                $fields = array_merge($fields, $constraint['columns']);
            }
        }

        return $fields;
    }

    /**
     * Get nullable fields from schema.
     *
     * @param \Cake\Database\Schema\TableSchemaInterface $schema Table schema.
     * @return array
     */
    public static function getNullableFields(TableSchemaInterface $schema): array
    {
        $fields = [];
        foreach ($schema->columns() as $name) {
            $column = $schema->getColumn($name);
            if ($column['null']) {
                $fields[] = $name;
            }
        }

        return $fields;
    }
}
