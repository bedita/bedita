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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\ObjectConditionsTrait;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * Test class to test trait methods.
 */
class TestConditions
{
    use ObjectConditionsTrait;

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return $this->statusCondition();
    }
}

/**
 * {@see \BEdita\Core\Model\Action\ObjectConditionsTrait} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\ObjectConditionsTrait
 */
class ObjectConditionsTraitTest extends TestCase
{
    /**
     * Test `statusCondition` method.
     *
     * @return void
     *
     * @covers ::statusCondition()
     */
    public function testStatusCondition()
    {
        $test = new TestConditions();
        $result = $test->execute();
        static::assertEquals([], $result);

        Configure::write('Status.level', 'draft');
        $result = $test->execute();
        $expected = [
            'status IN' => ['on', 'draft'],
        ];
        static::assertEquals($expected, $result);
    }
}
