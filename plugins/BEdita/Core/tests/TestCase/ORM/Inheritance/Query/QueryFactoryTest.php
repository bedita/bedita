<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\ORM\Inheritance\Query;

use BEdita\Core\ORM\Inheritance\Query\DeleteQuery;
use BEdita\Core\ORM\Inheritance\Query\InsertQuery;
use BEdita\Core\ORM\Inheritance\Query\QueryFactory;
use BEdita\Core\ORM\Inheritance\Query\SelectQuery;
use BEdita\Core\ORM\Inheritance\Query\UnhydratedSelectQuery;
use BEdita\Core\ORM\Inheritance\Query\UpdateQuery;
use Cake\ORM\Query\DeleteQuery as CakeDeleteQuery;
use Cake\ORM\Query\InsertQuery as CakeInsertQuery;
use Cake\ORM\Query\SelectQuery as CakeSelectQuery;
use Cake\ORM\Query\UpdateQuery as CakeUpdateQuery;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\ORM\Inheritance\Query\QueryFactory} Test Case
 */
#[CoversClass(QueryFactory::class)]
class QueryFactoryTest extends TestCase
{
    /**
     * Test select() method.
     *
     * @return void
     */
    public function testSelect(): void
    {
        $table = $this->getStubBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getStub();

        $queryFactory = new QueryFactory();
        $selectQuery = $queryFactory->select($table);
        static::assertInstanceOf(SelectQuery::class, $selectQuery);
        static::assertInstanceOf(CakeSelectQuery::class, $selectQuery);
    }

    /**
     * Test unhydratedSelect() method.
     *
     * @return void
     */
    public function testUnhydrateSelect(): void
    {
        $table = $this->getStubBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getStub();

        $queryFactory = new QueryFactory();
        $unhydratedSelectQuery = $queryFactory->unhydratedSelect($table);
        static::assertInstanceOf(UnhydratedSelectQuery::class, $unhydratedSelectQuery);
        static::assertInstanceOf(CakeSelectQuery::class, $unhydratedSelectQuery);
    }

    /**
     * Test insert() method.
     *
     * @return void
     */
    public function testInsert(): void
    {
        $table = $this->getStubBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getStub();

        $queryFactory = new QueryFactory();
        $insertQuery = $queryFactory->insert($table);
        static::assertInstanceOf(InsertQuery::class, $insertQuery);
        static::assertInstanceOf(CakeInsertQuery::class, $insertQuery);
    }

    /**
     * Test update() method.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $table = $this->getStubBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getStub();

        $queryFactory = new QueryFactory();
        $updateQuery = $queryFactory->update($table);
        static::assertInstanceOf(UpdateQuery::class, $updateQuery);
        static::assertInstanceOf(CakeUpdateQuery::class, $updateQuery);
    }

    /**
     * Test delete() method.
     *
     * @return void
     */
    public function testDelete(): void
    {
        $table = $this->getStubBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getStub();

        $queryFactory = new QueryFactory();
        $deleteQuery = $queryFactory->delete($table);
        static::assertInstanceOf(DeleteQuery::class, $deleteQuery);
        static::assertInstanceOf(CakeDeleteQuery::class, $deleteQuery);
    }
}
