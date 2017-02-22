<?php
namespace BEdita\Core\Model\Table;

use BEdita\Core\ORM\Inheritance\Table;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Locations Model
 *
 * @method \BEdita\Core\Model\Entity\Location get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Location newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Location findOrCreate($search, callable $callback = null, $options = [])
 */
class LocationsTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('locations');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->extensionOf('Objects', [
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
            ->allowEmpty('coords_system');

        $validator
            ->allowEmpty('address');

        $validator
            ->allowEmpty('locality');

        $validator
            ->allowEmpty('postal_code');

        $validator
            ->allowEmpty('country_name');

        $validator
            ->allowEmpty('region');

        return $validator;
    }
}
