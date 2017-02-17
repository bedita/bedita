<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ObjectProperties Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Properties
 * @property \Cake\ORM\Association\BelongsTo $Objects
 *
 * @method \BEdita\Core\Model\Entity\ObjectProperty get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ObjectProperty findOrCreate($search, callable $callback = null, $options = [])
 */
class ObjectPropertiesTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('object_properties');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Properties', [
            'foreignKey' => 'property_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Properties'
        ]);
        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('property_value', 'create')
            ->notEmpty('property_value');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['property_id'], 'Properties'));
        $rules->add($rules->existsIn(['object_id'], 'Objects'));

        return $rules;
    }
}
