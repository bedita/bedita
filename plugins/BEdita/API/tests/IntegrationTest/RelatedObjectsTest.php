<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Test related objects endpoints.
 */
class RelatedObjectsTest extends IntegrationTestCase
{
    use LocatorAwareTrait;

    /**
     * Create a relation between profiles and users and connect user id 1 to profile id 4
     *
     * @return void
     */
    protected function createUserProfileRelation(): void
    {
        $Relations = $this->fetchTable('Relations');
        $RelationTypes = $this->fetchTable('RelationTypes');
        $ObjectRelations = $this->fetchTable('ObjectRelations');
        $ObjectTypes = $this->fetchTable('ObjectTypes');

        // Create the relation
        $relation = $Relations->newEntity([
            'name' => 'has_user',
            'label' => 'Has User',
            'inverse_name' => 'has_profile',
            'inverse_label' => 'Has Profile',
            'description' => 'Profile has a user relationship',
            'params' => null,
        ]);
        $relation = $Relations->saveOrFail($relation);

        // Get object types for profiles and users
        $profileType = $ObjectTypes->find()->where(['name' => 'profiles'])->firstOrFail();
        $userType = $ObjectTypes->find()->where(['name' => 'users'])->firstOrFail();

        // Create relation types - profiles on left, users on right
        $leftRelationType = $RelationTypes->newEntity([
            'relation_id' => $relation->id,
            'object_type_id' => $profileType->id,
            'side' => 'left',
        ]);
        $RelationTypes->saveOrFail($leftRelationType);

        $rightRelationType = $RelationTypes->newEntity([
            'relation_id' => $relation->id,
            'object_type_id' => $userType->id,
            'side' => 'right',
        ]);
        $RelationTypes->saveOrFail($rightRelationType);

        // Create object relation linking user id 1 with profile id 4
        $objectRelation = $ObjectRelations->newEmptyEntity();
        $objectRelation->left_id = 4; // profile id 4
        $objectRelation->relation_id = $relation->id;
        $objectRelation->right_id = 1; // user id 1
        $ObjectRelations->saveOrFail($objectRelation);
    }

    /**
     * Test releated objects endpoint.
     *
     * @return void
     * @coversNothing
     */
    public function testRelated(): void
    {
        $this->createUserProfileRelation();
        $this->configRequestHeaders('GET');
        $this->get('/profiles/4/has_user');
        $this->assertResponseCode(200);

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertResponseNotEmpty();
        debug($body);
        $id = $body['data'][0]['id'] ?? null;
        $this->assertNotEmpty($id, 'Expected a related user ID in response');
        $this->assertEquals(1, $id, 'Expected related user ID to be 1');
        $type = $body['data'][0]['type'] ?? null;
        $this->assertEquals('users', $type, 'Expected related user type to be "users"');
    }

    public function testIncluded(): void
    {
        $this->createUserProfileRelation();
        $this->configRequestHeaders('GET');
        $this->get('/profiles?filter[id]=4&include=has_user');
        $this->assertResponseCode(200);

        $body = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertResponseNotEmpty();
        debug($body);
        $id = $body['data'][0]['relationships']['has_user']['data'][0]['id'] ?? null;
        $this->assertNotEmpty($id, 'Expected a related user ID in included response');
        $this->assertEquals(1, $id, 'Expected related user ID to be 1');
        $type = $body['data'][0]['relationships']['has_user']['data'][0]['type'] ?? null;
        $this->assertEquals('users', $type, 'Expected related user type to be "users"');
    }
}
