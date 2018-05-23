<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Shell\Task;

use BEdita\Core\Shell\Task\RecoverTreeTask;
use Cake\Console\Shell;
use Cake\Database\Expression\Comparison;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestCase;

/**
 * @covers \BEdita\Core\Shell\Task\RecoverTreeTask
 */
class RecoverTreeTaskTest extends ConsoleIntegrationTestCase
{

    /**
     * Trees table.
     *
     * @var \BEdita\Core\Model\Table\TreesTable
     */
    public $Trees;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.trees',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Trees = TableRegistry::get('Trees');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Trees);

        parent::tearDown();
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
        $this->exec(RecoverTreeTask::class);

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertOutputContains('Tree recovery completed');

        // Assert that tree returned to a valid state.
        $actual = $getTreeState();
        static::assertEquals($expected, $actual);
    }
}
