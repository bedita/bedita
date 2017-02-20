<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RelationTypes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Relations
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 *
 * @method \BEdita\Core\Model\Entity\RelationType get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType findOrCreate($search, callable $callback = null, $options = [])
 */
class RelationTypesTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('relation_types');
        $this->setDisplayField('relation_id');
        $this->setPrimaryKey(['relation_id', 'object_type_id', 'side']);

        $this->belongsTo('Relations', [
            'foreignKey' => 'relation_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Relations'
        ]);
        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.ObjectTypes'
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
            ->allowEmpty('side', 'create');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['relation_id'], 'Relations'));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));

        return $rules;
    }
}
