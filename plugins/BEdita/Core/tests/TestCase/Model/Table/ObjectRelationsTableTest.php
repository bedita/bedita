<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\ObjectRelationsTable Test Case
 */
class ObjectRelationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectRelationsTable
     */
    public $ObjectRelations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.object_relations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ObjectRelations') ? [] : ['className' => 'BEdita\Core\Model\Table\ObjectRelationsTable'];
        $this->ObjectRelations = TableRegistry::get('ObjectRelations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ObjectRelations);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
