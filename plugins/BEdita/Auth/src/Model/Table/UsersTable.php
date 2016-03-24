<?php
namespace BEdita\Auth\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $ExternalAuth
 */
class UsersTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('users');
        $this->primaryKey('id');
        $this->displayField('username');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always',
                ],
                'Users.login' => [
                    'last_login' => 'always',
                ],
                'Users.loginError' => [
                    'last_login_err' => 'always',
                ],
            ],
        ]);

        $this->hasMany('ExternalAuth', [
            'foreignKey' => 'user_id',
            'className' => 'BEdita/Auth.ExternalAuth',
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

            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->requirePresence('username')
            ->notEmpty('username')

            ->allowEmpty('password')

            ->boolean('blocked')
            ->allowEmpty('blocked')

            ->dateTime('last_login')
            ->allowEmpty('last_login')

            ->dateTime('last_login_err')
            ->allowEmpty('last_login_err')

            ->naturalNumber('num_login_err')
            ->allowEmpty('num_login_err');

        return $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['username']));

        return $rules;
    }
}
