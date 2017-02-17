<?php
namespace BEdita\Core\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Media Model
 *
 * @method \BEdita\Core\Model\Entity\Media get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Media newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Media patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Media findOrCreate($search, callable $callback = null, $options = [])
 */
class MediaTable extends Table
{

     /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('media');
        $this->displayField('name');
        $this->primaryKey('id');
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
            ->requirePresence('uri', 'create')
            ->notEmpty('uri');

        $validator
            ->allowEmpty('name');

        $validator
            ->requirePresence('mime_type', 'create')
            ->notEmpty('mime_type');

        $validator
            ->integer('file_size')
            ->allowEmpty('file_size');

        $validator
            ->allowEmpty('hash_file');

        $validator
            ->allowEmpty('original_name');

        $validator
            ->integer('width')
            ->allowEmpty('width');

        $validator
            ->integer('height')
            ->allowEmpty('height');

        $validator
            ->allowEmpty('provider');

        $validator
            ->allowEmpty('media_uid');

        $validator
            ->allowEmpty('thumbnail');

        return $validator;
    }
}
