<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Annotations Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @method \BEdita\Core\Model\Entity\Annotation get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AnnotationsTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('annotations');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Users'
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
            ->allowEmpty('description');

        $validator
            ->allowEmpty('params');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['object_id'], 'Objects'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
