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
use Exception;

/**
 *  {@see \BEdita\Core\Model\Action\ActionTrait} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\ActionTrait
 */
class ActionTraitTest extends TestCase
{
    use ActionTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        // 'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        // 'plugin.BEdita/Core.Profiles',
        // 'plugin.BEdita/Core.Users',
        // 'plugin.BEdita/Core.AsyncJobs',
        // 'plugin.BEdita/Core.Roles',
        // 'plugin.BEdita/Core.Trees',
        // 'plugin.BEdita/Core.RolesUsers',
        // 'plugin.BEdita/Core.ExternalAuth',
        // 'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        // 'plugin.BEdita/Core.ObjectRelations',
        // 'plugin.BEdita/Core.History',
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
            'override' => [
                'BEdita\Core\Model\Action\SignupUserAction',
                SignupUserAction::class,
            ],
            'fail' => [
                new \Error("Class '\My\Class' not found"),
                SignupUserAction::class,
                [],
                ['Signup.myAction' => '\My\Class'],
            ],
        ];
    }

    /**
     * Test getter for meta.
     *
     * @return void
     *
     * @dataProvider createActionProvider
     * @covers ::createAction()
     */
    public function testCreateAction($expected, string $action, array $options = [], ?array $config = null)
    {
        if (!empty($config)) {
            Configure::write($config);
            $config = key($config);
        }

        if ($expected instanceof \Throwable) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $action = $this->createAction($action, $options, $config);
        static::assertEquals($expected, get_class($action));
    }
}
