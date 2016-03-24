<?php
namespace BEdita\Auth\Model\Table;

use Cake\Database\Schema\Table as Schema;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AuthProviders Model
 *
 * @property \Cake\ORM\Association\HasMany $ExternalAuth
 */
class AuthProvidersTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('auth_providers');
        $this->primaryKey('id');
        $this->displayField('name');

        $this->hasMany('ExternalAuth', [
            'foreignKey' => 'auth_provider_id',
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

            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->requirePresence('name', 'create')
            ->notEmpty('name')

            ->url('url')
            ->requirePresence('url', 'create')
            ->notEmpty('url')

            ->allowEmpty('params');

        return $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name']));

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
