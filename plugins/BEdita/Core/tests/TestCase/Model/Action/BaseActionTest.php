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

use BEdita\Core\Model\Action\BaseAction;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * Test class to test `statusCondition` method
 */
class StatusTestAction extends BaseAction
{
    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        return $this->statusCondition();
    }
}

/**
 * {@see \BEdita\Core\Model\Action\BaseAction} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\BaseAction
 */
class BaseActionTest extends TestCase
{

    /**
     * Test constructor method.
     *
     * @return void
     *
     * @covers ::__construct()
     * @covers ::initialize()
     */
    public function testConstruct()
    {
        $config = [
            'key' => 'value',
        ];

        $baseAction = $this->getMockForAbstractClass(BaseAction::class, [$config]);

        static::assertAttributeEquals($config, '_config', $baseAction);
    }

    /**
     * Test magic method for invoking command.
     *
     * @return void
     *
     * @covers ::__invoke()
     */
    public function testInvoke()
    {
        $baseAction = $this->getMockForAbstractClass(BaseAction::class, [[]]);

        $baseAction->method('execute')
            ->willReturnArgument(0);

        $data = [
            'key' => 'value',
        ];

        $result = $baseAction->__invoke($data);

        static::assertEquals($data, $result);
    }

    /**
     * Test `statusCondition` method.
     *
     * @return void
     *
     * @covers ::statusCondition()
     */
    public function testStatusCondition()
    {
        $action = new StatusTestAction();
        $result = $action();
        static::assertEquals([], $result);

        Configure::write('Status.level', 'draft');
        $result = $action();
        $expected = [
            'status IN' => ['on', 'draft'],
        ];
        static::assertEquals($expected, $result);
    }
}
