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

namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\PublicationsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\PublicationsTable
 */
class PublicationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\PublicationsTable
     */
    public $Publications;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Publications', // temporary fixture, not suitable for actual save/get operations
    ];

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'ok' => [
                [],
                [
                    'title' => 'gustavo blog',
                    'public_name' => 'Gustavo rulez',
                    'public_url' => 'https://www.gustavo.com',
                    'staging_url' => 'https://staging.gustavo.com',
                    'stats_code' => 'abcdef',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param string[] $expected Expected errors.
     * @param array $data Data.
     * @return void
     *
     * @dataProvider validationProvider()
     *
     * @covers ::initialize()
     * @covers ::validationDefault()
     */
    public function testValidation(array $expected, array $data)
    {
        $this->Publications = TableRegistry::getTableLocator()->get('Publications');
        $entity = $this->Publications->newEntity();
        $entity = $this->Publications->patchEntity($entity, $data);
        $errors = array_keys(Hash::flatten($entity->getErrors()));

        static::assertEquals($expected, $errors);
    }
}
