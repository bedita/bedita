<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Command\TreeRecoverCommand;
use Cake\Console\Command;
use Cake\Database\Expression\Comparison;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ModelAwareTrait;
use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Command\TreeRecoverCommand} Test Case
 *
 * @property \BEdita\Core\Model\Table\TreesTable $Trees
 *
 * @covers \BEdita\Core\Command\TreeRecoverCommand
 */
class TreeRecoverCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;
    use ModelAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Trees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->useCommandRunner();
        $this->loadModel('Trees');
    }

    /**
     * Test main execution.
     *
     * @return void
     */
    public function testExecute()
    {
        /**
         * Helper function to get a "snapshot" describing tree state.
         *
         * @return array
         */
        $getTreeState = function () {
            return $this->Trees->find()
                ->all()
                ->combine('id', function (EntityInterface $node) {
                    return sprintf('%d / %d', $node['tree_left'], $node['tree_right']);
                })
                ->toArray();
        };

        // Get current snapshot. This assumes fixtures cause tree to be in a valid state.
        $expected = $getTreeState();

        // Corrupt tree: `UPDATE * FROM trees SET tree_left = id * 2, tree_right = id * 2 + 1`.
        $this->Trees->updateAll(
            [
                'tree_left' => new Comparison('id', 2, 'integer', '*'),
                'tree_right' => new Comparison(new Comparison('id', 2, 'integer', '*'), 1, 'integer', '+'),
            ],
            []
        );

        // Check that some corruption actually happened.
        $corrupt = $getTreeState();
        static::assertNotEquals($expected, $corrupt, 'Tree hasn\'t been corrupted prior to testing');

        // Recover.
        $this->exec(TreeRecoverCommand::defaultName());

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Tree recovery completed');

        // Assert that tree returned to a valid state.
        $actual = $getTreeState();
        static::assertEquals($expected, $actual);
    }
}
