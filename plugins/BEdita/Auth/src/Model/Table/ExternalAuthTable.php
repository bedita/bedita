<?php
namespace BEdita\Auth\Model\Table;

use Cake\Database\Schema\Table as Schema;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ExternalAuth Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $AuthProviders
 */
class ExternalAuthTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('external_auth');
        $this->primaryKey('id');
        $this->displayField('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Auth.Users',
        ]);
        $this->belongsTo('AuthProviders', [
            'foreignKey' => 'auth_provider_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Auth.AuthProviders',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->naturalNumber('id')
            ->allowEmpty('id', 'create')

            ->requirePresence('username')
            ->notEmpty('username')

            ->allowEmpty('params');

        return $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['auth_provider_id'], 'AuthProviders'));

        $rules->add($rules->isUnique(['auth_provider_id', 'username']));

        return $rules;
    }

    /**
     * {@inheritDoc}
     */
    protected function _initializeSchema(Schema $schema)
    {
        $schema->columnType('params', 'json');

        return $schema;
    }
}
