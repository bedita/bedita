<?php
namespace BEdita\Core\Model\Table;

use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * ObjectPermissions Model
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable&\Cake\ORM\Association\BelongsTo $Objects
 * @property \BEdita\Core\Model\Table\RolesTable&\Cake\ORM\Association\BelongsTo $Roles
 * @method \BEdita\Core\Model\Entity\ObjectPermission get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission findOrCreate($search, ?callable $callback = null, $options = [])
 * @property \BEdita\Core\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $CreatedByUsers
 * @method \BEdita\Core\Model\Entity\ObjectPermission newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\ObjectPermission saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectPermission[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ObjectPermissionsTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('object_permissions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects',
        ]);
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Roles',
        ]);
        $this->belongsTo('CreatedByUsers', [
            'foreignKey' => 'created_by',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Users',
        ]);

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.UserModified', [
            'events' => [
                'Model.beforeSave' => [
                    'created_by' => 'new',
                ],
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create')

            ->integer('role_id')
            ->requirePresence('role_id', 'create')
            ->notEmptyString('role_id')

            ->integer('object_id')
            ->requirePresence('object_id', 'create')
            ->notEmptyString('object_id');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        return $rules
            ->add($rules->existsIn(['object_id'], 'Objects'))
            ->add($rules->existsIn(['role_id'], 'Roles'))
            ->add($rules->existsIn(['created_by'], 'CreatedByUsers'));
    }

    /**
     * Check if object permission is editable before saving.
     *
     * @param \Cake\Event\EventInterface $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity data.
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity): void
    {
        if (!$this->isEditable($entity)) {
            throw new ForbiddenException(__d('bedita', 'Save object permission is forbidden for user'));
        }
    }

    /**
     * Check if object permission is editable before deleting.
     *
     * @param \Cake\Event\EventInterface $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity data.
     * @return void
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity): void
    {
        if (!$this->isEditable($entity)) {
            throw new ForbiddenException(__d('bedita', 'Delete object permission is forbidden for user'));
        }
    }

    /**
     * Check if logged user can create, edit or delete the object permission.
     *
     * Object permission is editable if one of below condition is true:
     * - user belongs to admin role
     * - no permission is set on the related object
     * - permissions are set on the related object and user belongs to role that with permission
     *
     * @param \Cake\Datasource\EntityInterface $entity The object permission entity
     * @return bool
     * @throws \LogicException If entity missing of required object_id
     */
    protected function isEditable(EntityInterface $entity): bool
    {
        $user = LoggedUser::getUser();
        $roleIds = Hash::extract($user, 'roles.{n}.id');
        if (in_array(RolesTable::ADMIN_ROLE, $roleIds)) {
            return true;
        }

        $object = $this->Objects->get($entity->object_id);
        $permsRoles = Hash::extract((array)$object->perms, 'roles');
        if (empty($permsRoles)) { // no permission set
            return true;
        }

        $userRolesNames = Hash::extract($user, 'roles.{n}.name');
        if (empty($userRolesNames) && !empty($roleIds)) {
            $userRolesNames = $this->Roles
                ->find('list')
                ->where(['id IN' => $roleIds])
                ->toArray();
        }

        return !empty(array_intersect($permsRoles, $userRolesNames));
    }
}
