<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Controller;

use BEdita\Core\Model\Action\GetObjectAction;
use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Entity\Tree;
use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Controller for `/trees` endpoint.
 *
 * @since 4.2.0
 */
class TreesController extends AppController
{
    use InstanceConfigTrait;

    /**
     * Objects Table.
     *
     * @var \BEdita\Core\Model\Table\ObjectsTable
     */
    protected $Objects;

    /**
     * Trees Table.
     *
     * @var \BEdita\Core\Model\Table\TreesTable
     */
    protected $Trees;

    /**
     * Request object Table.
     *
     * @var \BEdita\Core\Model\Table\ObjectsBaseTable
     */
    protected $Table;

    /**
     * Path information with ID, object type and uname of each object
     * Associative array having keys:
     *  - 'ids': ID path list
     *  - 'unames': uname path list
     *  - 'types': object types id list
     *
     * @var array
     */
    protected $pathInfo = [
        'ids' => [],
        'unames' => [],
        'types' => [],
    ];

    /**
     * Available configurations are:
     *  - `allowedAssociations`: array of relationships of the loaded object
     *
     * @var array
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [],
    ];

    /**
     * Trees node entity.
     *
     * @var \BEdita\Core\Model\Entity\Tree
     */
    protected $treesNode;

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Objects = TableRegistry::getTableLocator()->get('Objects');
        $this->Trees = TableRegistry::getTableLocator()->get('Trees');
    }

    /**
     * Display object on a given path
     *
     * @param string $path Trees path
     * @return \Cake\Http\Response|null
     */
    public function index(string $path)
    {
        $this->request->allowMethod(['get']);

        // populate idList, unameList
        $this->pathDetails($path);

        $this->loadTreesNode();
        $parents = $this->parents();

        $ids = array_values((array)$this->pathInfo['ids']);
        $entity = $this->loadObject(end($ids));

        $this->checkPath($entity, $parents);

        $entity->set('uname_path', sprintf('/%s', implode('/', $this->pathInfo['unames'])));
        $entity->setAccess('uname_path', false);
        $entity->set('menu', (bool)$this->treesNode->get('menu'));

        $this->set('_fields', $this->request->getQuery('fields', []));
        $this->set(compact('entity'));
        $this->set('_serialize', ['entity']);

        return null;
    }

    /**
     * Check path validity.
     *
     * @param EntityInterface $entity Object entity.
     * @param array $parents Parents ID array.
     * @return void
     */
    protected function checkPath(EntityInterface $entity, array $parents): void
    {
        if ($entity->get('type') === 'folders') {
            $idPath = sprintf('/%s', implode('/', $this->pathInfo['ids']));
            if ($entity->get('path') !== $idPath) {
                throw new NotFoundException(__d('bedita', 'Invalid path'));
            }

            return;
        }

        $pathFound = array_values($parents);
        $pathFound[] = (int)$entity->get('id');
        if ($this->pathInfo['ids'] !== $pathFound) {
            throw new NotFoundException(__d('bedita', 'Invalid path'));
        }
    }

    /**
     * Populate $pathInfo with path details on ID, uname and type:
     *
     * @param string $path Requesed object path
     * @return void
     */
    protected function pathDetails(string $path): void
    {
        $pathList = explode('/', $path);
        foreach ($pathList as $p) {
            if (is_numeric($p)) {
                $item = $this->objectDetails(['id' => (int)$p]);
            } else {
                $item = $this->objectDetails(['uname' => (string)$p]);
            }
            if (empty($item)) {
                throw new NotFoundException(__d('bedita', 'Invalid path'));
            }
            $this->pathInfo['ids'][] = $item['id'];
            $this->pathInfo['unames'][] = $item['uname'];
            $this->pathInfo['types'][] = $item['object_type_id'];
        }
    }

    /**
     * Get object main fields
     *
     * @param array $condition Query conditions
     * @return string
     */
    protected function objectDetails(array $condition): array
    {
        return (array)$this->Objects->find('available')
            ->where($condition)
            ->select(['id', 'uname', 'object_type_id'])
            ->disableHydration()
            ->first();
    }

    /**
     * Get parents object ID array and check object parent existence
     *
     * @return array
     */
    protected function parents(): array
    {
        $parentId = $this->treesNode->get('parent_id');
        if (empty($parentId)) {
            return [];
        }

        return $this->Trees->find('pathNodes', [$parentId])
            ->find('list', [
                'keyField' => 'id',
                'valueField' => 'object_id',
            ])
            ->toArray();
    }

    /**
     * Load trees table node of path object.
     *
     * @return void
     */
    protected function loadTreesNode(): void
    {
        $count = count($this->pathInfo['ids']);

        $id = Hash::get($this->pathInfo['ids'], $count - 1);
        $parentId = Hash::get($this->pathInfo['ids'], $count - 2);

        /** @var Tree $node */
        $node = $this->Trees->find()
            ->where([
                'object_id' => $id,
                'parent_id IS' => $parentId,
            ])
            ->first();
        if (empty($node)) {
            throw new NotFoundException(__d('bedita', 'Invalid path'));
        }

        $this->treesNode = $node;
    }

    /**
     * Load object entity
     *
     * @param int $id Object ID
     * @return EntityInterface
     */
    protected function loadObject(int $id): EntityInterface
    {
        $types = array_values($this->pathInfo['types']);
        /** @var \BEdita\Core\Model\Entity\ObjectType $objectType */
        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get(end($types));
        $this->Table = TableRegistry::getTableLocator()->get($objectType->get('alias'));

        $action = new GetObjectAction(['table' => $this->Table, 'objectType' => $objectType]);

        return $action([
            'primaryKey' => $id,
            'contain' => $this->getContain($objectType),
            'lang' => $this->request->getQuery('lang'),
        ]);
    }

    /**
     * Retrieve `contain` associations array
     *
     * @param ObjectType $objectType Object type entity
     * @return array
     */
    protected function getContain(ObjectType $objectType): array
    {
        $include = $this->request->getQuery('include');
        if (empty($include)) {
            return [];
        }

        $relations = array_keys($objectType->getRelations());
        $this->setConfig('allowedAssociations', $relations);

        return $this->prepareInclude($include);
    }

    /**
     * Find the association corresponding to the relationship name.
     *
     * @param string $relationship Relationship name.
     * @param \Cake\ORM\Table|null $table Table to consider.
     * @return \Cake\ORM\Association
     * @throws \Cake\Http\Exception\NotFoundException Throws an exception if no association could be found.
     */
    protected function findAssociation(string $relationship, ?Table $table = null): Association
    {
        if (in_array($relationship, $this->getConfig('allowedAssociations'))) {
            $association = $this->Table->associations()->getByProperty($relationship);
            if ($association !== null) {
                return $association;
            }
        }

        throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
    }
}
