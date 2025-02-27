<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Controller;

use Cake\Http\Exception\NotFoundException;

/**
 * Controller for `/tree_paths` endpoint.
 *
 * @since 5.36.12
 */
class TreePathsController extends TreesController
{
    /**
     * Path information with ID, object type and slug of each object
     * Associative array having keys:
     *  - 'ids': ID path list
     *  - 'slugs': slug path list
     *  - 'types': object types id list
     *
     * @var array
     */
    protected $pathInfo = [
        'ids' => [],
        'slugs' => [],
        'types' => [],
    ];

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

        $entity->set('slug_path', sprintf('/%s', implode('/', $this->pathInfo['slugs'])));
        $entity->setAccess('slug_path', false);
        $entity->set('menu', (bool)$this->treesNode->get('menu'));

        $this->set('_fields', $this->request->getQuery('fields', []));
        $this->set(compact('entity'));
        $this->setSerialize(['entity']);

        return null;
    }

    /**
     * Populate $pathInfo with path details on ID, uname and type:
     *
     * @param string $path Requested object path
     * @return void
     */
    protected function pathDetails(string $path): void
    {
        $pathList = explode('/', $path);
        foreach ($pathList as $p) {
            if (is_numeric($p)) {
                $item = $this->objectDetails(['id' => (int)$p]);
            } else {
                $item = $this->objectDetails([$this->Objects->getAssociation('TreeNodes')->aliasField('slug') => (string)$p]);
            }
            if (empty($item)) {
                throw new NotFoundException(__d('bedita', 'Invalid path'));
            }
            $this->pathInfo['ids'][] = $item['id'];
            $this->pathInfo['slugs'][] = $item['slug'];
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
            ->select(['id', 'slug' => $this->Objects->getAssociation('TreeNodes')->aliasField('slug'), 'object_type_id'])
            ->innerJoinWith('TreeNodes')
            ->disableHydration()
            ->first();
    }
}
