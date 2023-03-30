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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\ActionTrait;
use BEdita\Core\Model\Action\SignupUserAction;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 *  {@see \BEdita\Core\Model\Action\ActionTrait} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\ActionTrait
 */
class ActionTraitTest extends TestCase
{
    use ActionTrait;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('Actions.ListObjectsAction', 'MyPlugin.MyListAction');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        Configure::delete('Actions');
    }

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
    ];

    /**
     * Data provider for `testCreateAction`
     *
     * @return void
     */
    public function createActionProvider()
    {
        return [
            'simple' => [
                'BEdita\Core\Model\Action\SignupUserAction',
                SignupUserAction::class,
            ],
            'syntax' => [
                'BEdita\Core\Model\Action\SignupUserAction',
                'SignupUserAction',
            ],
            'prefix' => [
                'BEdita\Core\Model\Action\GetObjectAction',
                'GetObjectAction',
                [],
                'BEdita/Core',
            ],
            'fail with config' => [
                new \RuntimeException('Unable to find class "MyPlugin.MyListAction"'),
                'ListObjectsAction',
            ],
            'direct fail' => [
                new \RuntimeException('Unable to find class "BEdita/Core.\My\Class'),
                '\My\Class',
            ],
        ];
    }

    /**
     * Test `createAction` method
     *
     * @return void
     * @param string|\Exception $expected Expected result
     * @param string $class Class name
     * @param array $options Class options
     * @param string $prefix Class prefix
     * @dataProvider createActionProvider
     * @covers ::createAction()
     */
    public function testCreateAction($expected, string $class, array $options = [], string $prefix = 'BEdita/Core')
    {
        if ($expected instanceof \Throwable) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $class = $this->createAction($class, $options, $prefix);
        static::assertEquals($expected, get_class($class));
    }
}
