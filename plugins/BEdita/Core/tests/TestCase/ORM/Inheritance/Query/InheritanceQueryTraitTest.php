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
namespace BEdita\Core\Test\TestCase\ORM\Inheritance\Query;

use BEdita\Core\ORM\Inheritance\Query\SelectQuery;
use BEdita\Core\Test\TestCase\ORM\Inheritance\FakeAnimalsTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\Query\InheritanceQueryTrait} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\Query\InheritanceQueryTrait
 */
class InheritanceQueryTraitTest extends TestCase
{
    use FakeAnimalsTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setupTables();
        $this->setupAssociations();
    }

    /**
     * Test adding default types of inherited columns to type map.
     *
     * @return void
     * @covers ::addDefaultTypes()
     */
    public function testAddDefaultTypes(): void
    {
        $this->fakeAnimals->getSchema()->setColumnType('name', 'json');
        $query = new SelectQuery($this->fakeFelines);

        $defaults = $query->getTypeMap()->getDefaults();
        static::assertArrayHasKey('name', $defaults);
        static::assertArrayHasKey('FakeFelines.name', $defaults);
        static::assertArrayHasKey('FakeFelines__name', $defaults);

        static::assertSame('json', $defaults['name']);
        static::assertSame('json', $defaults['FakeFelines.name']);
        static::assertSame('json', $defaults['FakeFelines__name']);
    }
}
