<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */
namespace BEdita\Model\Table\BaseObject;

use BEdita\Model\Table\ObjectsTable;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\Validation\Validator;

/**
 * Abstract base class for BEdita object types.
 * Every object type Table class has to extend this class.
 */
abstract class BaseObject extends ObjectsTable {

    /**
     * The object type name
     *
     * @var string
     */
    protected $objectType = null;

    /**
     * Contains the list of main object type tables.
     * These tables have hasOne association with objects and have the same id.
     * The association is created in self::initialize()
     *
     * Example: ['Contents', 'Streams']
     *
     * @var array
     */
    protected $objectChain = [];

    /**
     * Initialize the table instance
     *
     * @param  array  $config Configuration options
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);
        $this->addBehavior('BEditaObject');
        $this->initChain();
    }

    /**
     * beforeValidate method
     * set object_type_id and call parent::beforeValidate()
     *
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     * @param \Cake\Validation\Validator $validator
     * @return void
     */
    public function beforeValidate(Event $event, Entity $entity, \ArrayObject $options, Validator $validator) {
        if (empty($entity->object_type_id)) {
            $entity->set('object_type_id', $this->objectTypeId());
        }
        parent::beforeValidate($event, $entity, $options, $validator);
    }

    /**
     * return the list of object chain
     *
     * @return array
     */
    public function getObjectChain() {
        return $this->objectChain;
    }

    /**
     * Initialize the object type chain set hasOne associations
     *
     * @return void
     */
    protected function initChain() {
        foreach ($this->objectChain as $tableObject) {
            $this->hasOne($tableObject, ['foreignKey' => 'id']);
        }
    }

    /**
     * Return the object type id
     *
     * @return null|integer
     */
    public function objectTypeId() {
        // temporary get object type id from db then it will be in config
        $objectTypes = TableRegistry::get('ObjectTypes');
        $res = $objectTypes->find()
            ->where(['name' => $this->objectType])
            ->first();
        return ($res)? $res->id : null;
    }

}
