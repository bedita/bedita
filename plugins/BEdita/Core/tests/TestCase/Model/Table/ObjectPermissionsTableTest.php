<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Utility\LoggedUser;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\ObjectPermissionsTable Test Case
 */
class ObjectPermissionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectPermissionsTable
     */
    public $ObjectPermissions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.ObjectPermissions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ObjectPermissions = $this->fetchTable('ObjectPermissions');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ObjectPermissions);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     * @coversNothing
     */
    public function testInitialize()
    {
        $this->assertEquals('object_permissions', $this->ObjectPermissions->getTable());
        $this->assertEquals('id', $this->ObjectPermissions->getPrimaryKey());
        $this->assertEquals('id', $this->ObjectPermissions->getDisplayField());

        $this->assertInstanceOf(BelongsTo::class, $this->ObjectPermissions->CreatedByUsers);
        $this->assertInstanceOf(BelongsTo::class, $this->ObjectPermissions->Objects);
        $this->assertInstanceOf(BelongsTo::class, $this->ObjectPermissions->Roles);
        $this->assertInstanceOf(TimestampBehavior::class, $this->ObjectPermissions->behaviors()->get('Timestamp'));
    }

    /**
     * Data provider for `testBuildRules` test case.
     *
     * @return array
     */
    public function buildRulesProvider()
    {
        return [
            'invalidObject' => [
                false,
                [
                    'object_id' => 1234,
                    'role_id' => 1,
                    'created_by' => 1,
                ],
            ],
            'invalidRole' => [
                false,
                [
                    'object_id' => 2,
                    'role_id' => 1234,
                    'created_by' => 1,
                ],
            ],
            'invalidUser' => [
                false,
                [
                    'object_id' => 2,
                    'role_id' => 1,
                    'created_by' => 1234,
                ],
            ],
        ];
    }

    /**
     * Test build rules validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider buildRulesProvider
     * @coversNothing
     */
    public function testBuildRules($expected, array $data): void
    {
        $entity = $this->ObjectPermissions->newEntity($data, ['accessibleFields' => ['created_by' => true]]);
        $success = $this->ObjectPermissions->save($entity);
        $this->assertEquals($expected, (bool)$success, print_r($entity->getErrors(), true));
    }

    /**
     * Test that save object permission with admin user is allowed
     * also with another permission set on it.
     *
     * @return void
     * @covers ::beforeSave
     */
    public function testBeforeSaveAdmin(): void
    {
        LoggedUser::setUserAdmin();
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        $ot = $ObjectTypes->get('documents');
        $ot->permissions_enabled = true;
        $ObjectTypes->saveOrFail($ot);
        static::assertTrue($this->ObjectPermissions->exists(['object_id' => 2]));
        $entity = $this->ObjectPermissions->newEntity([
            'role_id' => 2,
            'object_id' => 2,
        ]);

        $this->ObjectPermissions->saveOrFail($entity);
        static::assertEquals(2, $entity->id);
    }

    /**
     * Test save is ok when no permission is set on object.
     *
     * @return void
     * @covers ::beforeSave()
     */
    public function testBeforeSaveNoPermissionSet(): void
    {
        $user = $this->fetchTable('Users')->get(5, ['contain' => 'Roles']);
        LoggedUser::setUser($user->toArray());
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        $ot = $ObjectTypes->get('documents');
        $ot->permissions_enabled = true;
        $ObjectTypes->saveOrFail($ot);
        static::assertFalse($this->ObjectPermissions->exists(['object_id' => 3]));
        $entity = $this->ObjectPermissions->newEntity([
            'role_id' => 2,
            'object_id' => 3,
        ]);

        $this->ObjectPermissions->saveOrFail($entity);
        static::assertEquals(2, $entity->id);
    }

    /**
     * Test save is ok when permission is set on object and user has grant.
     *
     * @return void
     * @covers ::beforeSave()
     */
    public function testBeforeSaveWithPermissionOk(): void
    {
        $user = $this->fetchTable('Users')->get(5, ['contain' => 'Roles']);
        LoggedUser::setUser($user->toArray());
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        $ot = $ObjectTypes->get('documents');
        $ot->permissions_enabled = true;
        $ObjectTypes->saveOrFail($ot);
        static::assertFalse($this->ObjectPermissions->exists(['object_id' => 3]));
        $entity = $this->ObjectPermissions->newEntity([
            'role_id' => 2,
            'object_id' => 3,
        ]);

        $this->ObjectPermissions->saveOrFail($entity);
        static::assertEquals(2, $entity->id);

        $entity = $this->ObjectPermissions->newEntity([
            'role_id' => 1,
            'object_id' => 3,
        ]);
        $this->ObjectPermissions->saveOrFail($entity);
        static::assertEquals(3, $entity->id);
    }

    /**
     * Test save is forbidden when permission is set on object and user hasn't grant.
     *
     * @return void
     * @covers ::beforeSave()
     */
    public function testBeforeSaveWithPermissionForbidden(): void
    {
        $this->expectException(ForbiddenException::class);

        $user = $this->fetchTable('Users')->get(5, ['contain' => 'Roles']);
        LoggedUser::setUser($user->toArray());
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        $ot = $ObjectTypes->get('documents');
        $ot->permissions_enabled = true;
        $ObjectTypes->saveOrFail($ot);
        static::assertTrue($this->ObjectPermissions->exists(['object_id' => 2]));
        $entity = $this->ObjectPermissions->newEntity([
            'role_id' => 2,
            'object_id' => 2,
        ]);

        $this->ObjectPermissions->save($entity);
    }
}
