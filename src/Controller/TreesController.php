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
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

/**
 * Controller for `/trees` endpoint.
 *
 * @since 4.2.0
 */
class TreesController extends AppController
{
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
     * Path ID list
     *
     * @var array
     */
    protected $idList;

    /**
     * Path uname list
     *
     * @var array
     */
    protected $unameList;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize()
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

        $parents = $this->parents();

        $objectId = end((array_values($this->idList)));
        $entity = $this->loadObject($objectId);

        $this->checkPath($entity, $parents);

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
        if (empty($parents) && $entity->get('type') !== 'folders') {
            throw new NotFoundException('');
        }

        if ($entity->get('type') === 'folders') {
            $idPath = sprintf('/%s', implode('/', $this->idList));
            if ($entity->get('path') !== $idPath) {
                throw new NotFoundException('');
            }

            return;
        }

        $pathFound = array_values($parents);
        $pathFound[] = (int)$entity->get('id');
        if ($this->idList !== $pathFound) {
            throw new NotFoundException('');
        }
    }

    /**
     * Calculate path details:
     *  - idList: ID based path list
     *  - unameList: uname based path list
     *
     * @param string $path Requesed object path
     * @return void
     */
    protected function pathDetails(string $path): void
    {
        $this->unameList = $this->idList = [];
        $pathList = explode('/', $path);
        foreach ($pathList as $p) {
            if (is_numeric($p)) {
                $this->idList[] = (int)$p;
                $this->unameList[] = $this->objectUname($p);
            } else {
                $this->idList[] = $this->Objects->getId($p);
                $this->unameList[] = (string)$p;
            }
        }
    }

    /**
     * Get object uname
     *
     * @param int $id Object ID
     * @return string
     */
    protected function objectUname(int $id): string
    {
        return (string)$this->Objects->find('list', ['valueField' => 'uname'])
            ->where(compact('id'))
            ->firstOrFail();
    }

    /**
     * Get parents object ID array and check object parent existence
     *
     * @return array
     */
    protected function parents(): array
    {
        $count = count($this->idList);
        if ($count === 1) {
            return [];
        }

        $id = $this->idList[$count - 1];
        $parentId = $this->idList[$count - 2];
        $parentCondition = ['object_id' => $id, 'parent_id' => $parentId];
        if (!$this->Trees->exists($parentCondition)) {
            throw new NotFoundException('');
        }

        $node = $this->Trees->find()
            ->where(['object_id' => $parentId])
            ->firstOrFail();

        $path = $this->Trees->find('list', [
                'keyField' => 'id',
                'valueField' => 'object_id',
            ])
            ->where([
                'tree_left <=' => $node->get('tree_left'),
                'tree_right >=' => $node->get('tree_right'),
            ])
            ->order(['tree_left' => 'ASC']);

        return $path->toArray();
    }

    /**
     * Load object entity
     *
     * @param int $id Object ID
     * @return EntityInterface
     */
    protected function loadObject(int $id): EntityInterface
    {
        $res = $this->Objects->find()->where(compact('id'))
            ->select([$this->Objects->aliasField('object_type_id')])
            ->enableHydration(false)
            ->firstOrFail();

        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get($res['object_type_id']);
        $table = TableRegistry::getTableLocator()->get($objectType->get('alias'));
        $contain = $this->prepareInclude($this->request->getQuery('include'));

        $action = new GetObjectAction(compact('table', 'objectType'));

        return $action([
            'primaryKey' => $id,
            'contain' => $contain,
            'lang' => $this->request->getQuery('lang'),
        ]);
    }
}
