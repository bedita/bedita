<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\SortRelatedObjectsAction;
use BEdita\Core\Utility\LoggedUser;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\Model\Action\SortRelatedObjectsAction
 * @covers \BEdita\Core\Model\Action\SetRelatedObjectsAction
 * @covers \BEdita\Core\Model\Action\ListRelatedObjectsAction
 */
class SortRelatedObjectsActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        LoggedUser::setUserAdmin();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        LoggedUser::resetUser();
    }

    /**
     * Test `execute`.
     *
     * @return void
     */
    public function testExecute(): void
    {
        $id = 2;
        $Documents = $this->fetchTable('documents');
        $entity = $Documents->get($id);
        $relatedEntities = $Documents->get($id, ['contain' => ['Test']])->get('test');
        $association = $Documents->getAssociation('Test');
        $action = new SortRelatedObjectsAction(compact('association'));
        $action(['entity' => $entity, 'field' => 'title', 'direction' => 'desc']);
        $relatedEntitiesDesc = $Documents->get($id, ['contain' => ['Test']])->get('test');
        static::assertEquals($relatedEntities[0]->get('id'), $relatedEntitiesDesc[1]->get('id'));
        static::assertEquals($relatedEntities[1]->get('id'), $relatedEntitiesDesc[0]->get('id'));
        $action(['entity' => $entity, 'field' => 'title', 'direction' => 'asc']);
        $relatedEntitiesDesc = $Documents->get($id, ['contain' => ['Test']])->get('test');
        static::assertEquals($relatedEntities[0]->get('id'), $relatedEntitiesDesc[0]->get('id'));
        static::assertEquals($relatedEntities[1]->get('id'), $relatedEntitiesDesc[1]->get('id'));
    }
}
