<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Search\Adapter;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Search\BaseAdapter;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Simple search adapter.
 * Execute search via `LIKE` query.
 *
 * @since 5.14.0
 */
class SimpleAdapter extends BaseAdapter
{
    /**
     * Get validator.
     *
     * @param array $config Search configuration
     * @return \Cake\Validation\Validator
     */
    protected function getValidator(array $config = []): Validator
    {
        $validator = new Validator();

        return $validator
            ->isArray('words')
            ->add('words', 'checkWords', [
                'rule' => function ($value) use ($config) {
                    $value = (array)$value;
                    $minLength = Hash::get($config, 'minLength');
                    $maxWords = Hash::get($config, 'maxWords');
                    if (count($value) === 0) {
                        return __d('bedita', 'query strings must be at least {0} characters long', $minLength);
                    }

                    if ($maxWords > 0 && count($value) > $maxWords) {
                        return __d('bedita', 'query string too long');
                    }

                    return true;
                },
            ]);
    }

    /**
     * Prepare text before search.
     *
     * @param string $text The text to search
     * @param array $options The search options
     * @param array $config Search configuration
     * @return array
     */
    protected function prepareText(string $text, array $options = [], array $config = []): array
    {
        $minLength = Hash::get($config, 'minLength');
        $words = [$text];
        if (filter_var(Hash::get($options, 'exact'), FILTER_VALIDATE_BOOLEAN) !== true) {
            $words = preg_split('/\W+/', $text); // Split words.
        }
        $words = array_unique(array_map( // Escape `%` and `\` characters in words.
            fn (string $word): string => str_replace(
                ['%', '\\'],
                ['\\%', '\\\\'],
                $word,
            ),
            array_filter( // Filter out words that are too short.
                $words,
                fn (string $word): bool => mb_strlen($word) >= $minLength,
            )
        ));

        return $words;
    }

    /**
     * @inheritDoc
     */
    public function search(Query $query, string $text, array $options = [], array $config = []): Query
    {
        $words = $this->prepareText($text, $options, $config);
        $errors = $this->getValidator($config)->validate(compact('words'));
        if (!empty($errors)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => Hash::get($errors, 'text.0'),
            ]);
        }

        $tableFields = (array)Hash::get($config, 'fields');

        // Concat all fields into a single, lower-cased string.
        $fields = [];
        /** @var \Cake\ORM\Table $table */
        $table = $query->getRepository();
        foreach (array_keys($tableFields) as $field) {
            $fields[] = $query->func()->coalesce([
                $table->aliasField($field) => 'identifier',
                '',
            ]);
            $fields[] = ' '; // Add a spacer.
        }
        array_pop($fields); // Remove last spacer.
        $field = $query->func()->concat($fields);

        // Build query conditions.
        return $query
            ->where(function (QueryExpression $exp) use ($field, $words) {
                foreach ($words as $word) {
                    $exp->like(
                        $field,
                        sprintf('%%%s%%', $word)
                    );
                }

                return $exp;
            });
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function indexResource(EntityInterface $entity, string $operation): void
    {
    }
}
