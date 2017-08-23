<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Utility\LoggedUser;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property \Cake\ORM\Association\BelongsToMany $Users
 *
 * @method \BEdita\Core\Model\Entity\Role get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Role newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Role[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Role|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Role patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Role[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Role findOrCreate($search, callable $callback = null, $options = [])
 *
 * @since 4.0.0
 */
class RolesTable extends Table
{

    /**
     * Administrator role id
     *
     * @var int
     */
    const ADMIN_ROLE = 1;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setDisplayField('name');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Users', [
            'through' => 'RolesUsers',
        ]);
        $this->hasMany('EndpointPermissions', [
            'dependent' => true,
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->naturalNumber('id')
            ->allowEmpty('id', 'create')

            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->requirePresence('name')
            ->notEmpty('name')

            ->allowEmpty('description')

            ->boolean('unchangeable')
            ->allowEmpty('unchangeable')

            ->boolean('backend_auth')
            ->allowEmpty('backend_auth');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name']));

        return $rules;
    }

    /**
     * Finder for my roles (i.e.: roles of currently logged users).
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findMine(Query $query)
    {
        return $query
            ->where(function (QueryExpression $exp) {
                $junction = $this->Users->junction();
                $subQuery = $junction->find()
                    ->select([$junction->aliasField($this->Users->getForeignKey())])
                    ->where(function (QueryExpression $exp) use ($junction) {
                        return $exp->eq($junction->aliasField($this->Users->getTargetForeignKey()), LoggedUser::id());
                    });

                return $exp->in($this->aliasField((string)$this->getPrimaryKey()), $subQuery);
            });
    }
}
