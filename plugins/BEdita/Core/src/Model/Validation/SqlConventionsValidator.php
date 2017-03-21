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

namespace BEdita\Core\Model\Validation;

use BEdita\Core\Plugin;
use Cake\Database\Schema\TableSchema;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;

/**
 * Validator for SQL conventions.
 *
 * This validator validates a single field named `symbol`. Additional validation context
 * can be passed via providers.
 *
 * For instance, if you're validating an index name, you might want to pass `table` and `type`:
 *
 * ```php
 * $validator = new SqlConventionsValidator();
 * $validator->setProvider('table', 'my_table_name');
 * $validator->setProvider('type', 'index');
 *
 * $symbol = 'my_index_name';
 * $errors = $validator->errors(compact('symbol'));
 * ```
 *
 * Validating a column can be achieved in a similar way. Also, `allColumns` provider can be a list of
 * columns and the table they were first found in.
 *
 * ```php
 * $validator = new SqlConventionsValidator();
 * $validator->setProvider('table', 'my_table_name');
 * $validator->setProvider('allColumns', ['my_column' => 'table_one', 'my_other_column' => 'table_two']);
 *
 * $symbol = 'my_column_name';
 * $errors = $validator->errors(compact('symbol'));
 * ```
 *
 * @since 4.0.0
 */
class SqlConventionsValidator extends Validator
{

    /**
     * Comma-separated list of reserved words to be allowed anyway.
     *
     * @var string
     */
    const ALLOWED_RESERVED_WORDS = 'NAME,STATUS';

    /**
     * Comma-separated list of columns that can be duplicated across several tables.
     *
     * @var string
     */
    const ALLOWED_DUPLICATES = 'created,description,enabled,id,modified,name,params,label';

    /**
     * List of reserved words.
     *
     * @var array
     */
    protected static $reservedWords = [];

    /**
     * Get list of reserved words.
     *
     * @return array
     */
    protected static function getReservedWords()
    {
        if (!empty(static::$reservedWords)) {
            return static::$reservedWords;
        }

        $fileName = Plugin::configPath('BEdita/Core') . DS . 'schema' . DS . 'sql_reserved_words.txt';
        $allowed = explode(',', static::ALLOWED_RESERVED_WORDS);
        static::$reservedWords = array_filter(
            array_map(
                function ($word) {
                    return strtoupper(trim($word));
                },
                file($fileName)
            ),
            function ($word) use ($allowed) {
                return !empty($word) && substr($word, 0, 1) !== '#' && !in_array($word, $allowed);
            }
        );

        return static::$reservedWords;
    }

    /**
     * Get expected prefix for a symbol.
     *
     * @param array $context Context.
     * @return string
     */
    protected static function getPrefix(array $context)
    {
        $table = Hash::get($context, 'providers.table', '');

        return str_replace('_', '', $table) . '_';
    }

    /**
     * Get expected suffix for symbol.
     *
     * @param array $context Context.
     * @return string
     */
    protected static function getSuffix(array $context)
    {
        $type = Hash::get($context, 'providers.type', '');
        switch ($type) {
            case TableSchema::CONSTRAINT_PRIMARY:
                return '_pk';

            case TableSchema::CONSTRAINT_FOREIGN:
                return '_fk';

            case TableSchema::CONSTRAINT_UNIQUE:
                return '_uq';

            case TableSchema::INDEX_FULLTEXT:
            case TableSchema::INDEX_INDEX:
            default:
                return '_idx';
        }
    }

    /**
     * Check that a symbol is a string.
     *
     * @param mixed $symbol Symbol being checked.
     * @return bool
     */
    public static function isString($symbol)
    {
        return is_string($symbol);
    }

    /**
     * Check if a symbol is not a reserved word.
     *
     * @param string $symbol Symbol being checked.
     * @return bool
     */
    public static function reservedWord($symbol)
    {
        return !in_array(strtoupper($symbol), static::getReservedWords());
    }

    /**
     * Check if a symbol is in underscored form.
     *
     * @param string $symbol Symbol being checked.
     * @return bool
     */
    public static function underscored($symbol)
    {
        return Inflector::underscore($symbol) === $symbol;
    }

    /**
     * Check that a symbol does not start with an underscore.
     *
     * @param string $symbol Symbol being checked.
     * @return bool
     */
    public static function noLeadingUnderscore($symbol)
    {
        return substr($symbol, 0, 1) !== '_';
    }

    /**
     * Check that a symbol does not end with an underscore.
     *
     * @param string $symbol Symbol being checked.
     * @return bool
     */
    public static function noTrailingUnderscore($symbol)
    {
        return substr($symbol, -1) !== '_';
    }

    /**
     * Check that a symbol does not contain a double underscore.
     *
     * @param string $symbol Symbol being checked.
     * @return bool
     */
    public static function noDoubleUnderscore($symbol)
    {
        return strpos($symbol, '__') === false;
    }

    /**
     * Check that a symbol does not start with a digit.
     *
     * @param string $symbol Symbol being checked.
     * @return bool
     */
    public static function noLeadingDigit($symbol)
    {
        return !is_numeric(substr($symbol, 0, 1));
    }

    /**
     * Chech that a symbol does not match table name.
     *
     * @param string $symbol Symbol being checked.
     * @param array $context Context.
     * @return bool
     */
    public static function differentFromTable($symbol, array $context)
    {
        return $symbol !== Hash::get($context, 'providers.table');
    }

    /**
     * Check that a symbol starts with the expected prefix.
     *
     * @param string $symbol Symbol being checked.
     * @param array $context Context.
     * @return bool|string
     */
    public static function prefix($symbol, array $context)
    {
        $prefix = static::getPrefix($context);
        if (substr($symbol, 0, strlen($prefix)) === $prefix) {
            // Prefix OK.
            return true;
        }

        return sprintf('should start with "%s"', $prefix);
    }

    /**
     * Check that a symbol ends with the expected suffix.
     *
     * @param string $symbol Symbol being checked.
     * @param array $context Context.
     * @return bool|string
     */
    public static function suffix($symbol, array $context)
    {
        $suffix = static::getSuffix($context);
        if (substr($symbol, -strlen($suffix)) === $suffix) {
            // Suffix OK.
            return true;
        }

        return sprintf('should end with "%s"', $suffix);
    }

    /**
     * Check that a symbol has a custom identifier between prefix and suffix.
     *
     * @param string $symbol Symbol being checked.
     * @param array $context Context.
     * @return bool
     */
    public static function uniqueIdentifier($symbol, array $context)
    {
        $prefix = static::getPrefix($context);
        $suffix = static::getSuffix($context);

        return ($symbol !== $prefix . substr($suffix, 1));
    }

    /**
     * Check that a symbol hasn't been used elsewhere.
     *
     * @param string $symbol Symbol being checked.
     * @param array $context Context.
     * @return bool|string
     */
    public static function globalName($symbol, array $context)
    {
        $allowedDuplicates = explode(',', static::ALLOWED_DUPLICATES);
        if (in_array($symbol, $allowedDuplicates) || substr($symbol, -3) === '_id') {
            // Known dupes.
            return true;
        }

        $allColumns = Hash::get($context, 'providers.allColumns', []);

        if (!array_key_exists($symbol, $allColumns)) {
            // Not found.
            return true;
        }

        return sprintf('already defined in "%s"', $allColumns[$symbol]);
    }

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();

        $notPrimary = function ($context) {
            return Hash::get($context, 'data.symbol') !== 'primary' || Hash::get($context, 'providers.type') !== TableSchema::CONSTRAINT_PRIMARY;
        };
        $notTable = function ($context) {
            return Hash::get($context, 'providers.table') !== null;
        };
        $indexOrConstraint = function ($context) use ($notPrimary) {
            return Hash::get($context, 'providers.type') !== null && $notPrimary($context);
        };
        $column = function ($context) {
            return Hash::get($context, 'providers.allColumns') !== null;
        };

        $this->setProvider('sqlConventions', static::class);

        $this
            ->add('symbol', 'string', [
                'rule' => 'isString',
                'provider' => 'sqlConventions',
                'last' => true,
                'message' => 'must be a string',
            ])
            ->ascii('symbol', 'contains non-ASCII characters')
            ->add('symbol', 'reservedWord', [
                'on' => $notPrimary,
                'rule' => 'reservedWord',
                'provider' => 'sqlConventions',
                'message' => 'reserved word',
            ])
            ->add('symbol', 'underscored', [
                'rule' => 'underscored',
                'provider' => 'sqlConventions',
                'message' => 'not underscored',
            ])
            ->add('symbol', 'noLeadingUnderscore', [
                'rule' => 'noLeadingUnderscore',
                'provider' => 'sqlConventions',
                'message' => 'starts with "_"',
            ])
            ->add('symbol', 'noTrailingUnderscore', [
                'rule' => 'noTrailingUnderscore',
                'provider' => 'sqlConventions',
                'message' => 'ends with "_"',
            ])
            ->add('symbol', 'noDoubleUnderscore', [
                'rule' => 'noDoubleUnderscore',
                'provider' => 'sqlConventions',
                'message' => 'contains "__"',
            ])
            ->add('symbol', 'noLeadingDigit', [
                'rule' => 'noLeadingDigit',
                'provider' => 'sqlConventions',
                'message' => 'starts with a digit',
            ])
            ->add('symbol', 'differentFromTable', [
                'on' => $notTable,
                'rule' => 'differentFromTable',
                'provider' => 'sqlConventions',
                'message' => 'same name as table',
            ])
            ->add('symbol', 'prefix', [
                'on' => $indexOrConstraint,
                'rule' => 'prefix',
                'provider' => 'sqlConventions',
                'message' => 'should start with the expected prefix',
            ])
            ->add('symbol', 'suffix', [
                'on' => $indexOrConstraint,
                'rule' => 'suffix',
                'provider' => 'sqlConventions',
                'message' => 'should start with the expected suffix',
            ])
            ->add('symbol', 'uniqueIdentifier', [
                'on' => $indexOrConstraint,
                'rule' => 'uniqueIdentifier',
                'provider' => 'sqlConventions',
                'message' => 'should have a unique identifier between prefix and suffix',
            ])
            ->add('symbol', 'globalName', [
                'on' => $column,
                'rule' => 'globalName',
                'provider' => 'sqlConventions',
                'message' => 'already defined',
            ]);
    }
}
