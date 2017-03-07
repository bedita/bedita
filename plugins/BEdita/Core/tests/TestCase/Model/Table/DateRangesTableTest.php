<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\DateRangesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\DateRangesTable Test Case
 */
class DateRangesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\DateRangesTable
     */
    public $DateRanges;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.date_ranges',
        // 'plugin.b_edita/core.properties',
        // 'plugin.b_edita/core.property_types',
        // 'plugin.b_edita/core.left_relations',
        // 'plugin.b_edita/core.object_relations',
        // 'plugin.b_edita/core.left_objects',
        // 'plugin.b_edita/core.created_by_user',
        // 'plugin.b_edita/core.external_auth',
        // 'plugin.b_edita/core.users',
        // 'plugin.b_edita/core.roles',
        // 'plugin.b_edita/core.roles_users',
        // 'plugin.b_edita/core.endpoint_permissions',
        // 'plugin.b_edita/core.endpoints',
        // 'plugin.b_edita/core.applications',
        // 'plugin.b_edita/core.profiles',
        // 'plugin.b_edita/core.user_prof',
        // 'plugin.b_edita/core.modified_by_user',
        // 'plugin.b_edita/core.prof_user',
        // 'plugin.b_edita/core.auth_providers',
        // 'plugin.b_edita/core.relations',
        // 'plugin.b_edita/core.left_object_types',
        // 'plugin.b_edita/core.relation_types',
        // 'plugin.b_edita/core.right_relations',
        // 'plugin.b_edita/core.right_object_types',
        // 'plugin.b_edita/core.right_objects'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->DateRanges = TableRegistry::get('DateRanges');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DateRanges);

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
