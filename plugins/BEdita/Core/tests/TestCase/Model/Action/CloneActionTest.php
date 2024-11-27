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

use BEdita\Core\Model\Action\CloneAction;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Action\CloneAction} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\CloneAction
 */
class CloneActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.EndpointPermissions',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Translations',
        'plugin.BEdita/Core.UserTokens',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.Annotations',
    ];

    /**
     * Test command execution.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::execute()
     * @covers ::cloneEntity()
     * @covers ::cloneRelationships()
     * @covers ::cloneTranslations()
     */
    public function testExecute()
    {
        // document with ID 2 from fixtures has 5 relationships records and 4 translations records
        $id = 2;
        $title = 'new title';
        $status = 'draft';
        $include = ['relationships', 'translations'];
        $table = $this->fetchTable('Documents');
        $action = new CloneAction(compact('table'));
        $actual = $action(compact('id', 'title', 'status', 'include'));
        $original = $table->get($id, ['contain' => ['Test', 'TestSimple', 'TestDefaults', 'Translations']]);
        $clone = $table->get($actual->id, ['contain' => ['Test', 'TestSimple', 'TestDefaults', 'Translations']]);
        static::assertEquals($clone->title, $title);
        static::assertEquals($clone->status, $status);
        static::assertEquals($clone->title, $actual->title);
        static::assertEquals($clone->status, $actual->status);
        // relation test
        $relatedTest = $clone->get('test');
        $originalRelatedTest = $original->get('test');
        foreach ($relatedTest as $key => $item) {
            static::assertEquals($item->uname, $originalRelatedTest[$key]->uname);
        }
        // relation test_simple
        $relatedTestSimple = $clone->get('test_simple');
        $originalRelatedTestSimple = $original->get('test_simple');
        foreach ($relatedTestSimple as $key => $item) {
            static::assertEquals($item->uname, $originalRelatedTestSimple[$key]->uname);
        }
        // relation test_defaults
        $relatedTestDefaults = $clone->get('test_defaults');
        $originalRelatedTestDefaults = $original->get('test_defaults');
        foreach ($relatedTestDefaults as $key => $item) {
            static::assertEquals($item->uname, $originalRelatedTestDefaults[$key]->uname);
        }
        // translations
        $translations = $clone->get('translations');
        static::assertCount(4, $translations);
        foreach ($translations as $translation) {
            foreach ($original->get('translations') as $originalTranslation) {
                if ($originalTranslation->lang === $translation->lang) {
                    static::assertEquals($originalTranslation->translated_fields, $translation->translated_fields);
                }
            }
        }
    }
}
