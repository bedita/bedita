<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Test delete/restore operations on folders
 */
class DeleteFolderTest extends IntegrationTestCase
{

    /**
     * Test that restoring a folder is allowed only if no ancestors are deleted.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testRestoreFolder()
    {
        // GET current children of 11
        $folderId = 11;
        $this->configRequestHeaders();
        $this->get(sprintf('/folders/%s/children', $folderId));
        $expectedChildren = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertNotEmpty($expectedChildren['data']);

        // DELETE folder 11
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete(sprintf('/folders/%s', $folderId));

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');

        foreach ($expectedChildren['data'] as $child) {
            $id = $child['id'];
            $type = $child['type'];

            $this->configRequestHeaders();
            $this->get(sprintf('/%s/%s', $type, $id));
            if ($type === 'folders') {
                // GET folder => 404 for subfolders
                $this->assertResponseCode(404);

                $data = [
                    'id' => "$id",
                    'type' => 'folders'
                ];

                // PATCH to restore subfolder of deleted folder => 400
                $this->configRequestHeaders('PATCH', $authHeader);
                $this->patch(sprintf('/trash/%s', $id), json_encode(compact('data')));
                $this->assertResponseCode(400);
                $this->assertContentType('application/vnd.api+json');
                $result = json_decode((string)$this->_response->getBody(), true);
                static::assertNotEmpty($result['error']['detail']);
                static::assertStringStartsWith('[deleted.isFolderRestorable]: Folder can be restored only if its ancestors are not deleted.', $result['error']['detail']);
            } else {
                // GET other objects => 200 for other children objects
                $this->assertResponseCode(200);
            }
        }

        // PATCH restore parent folder => ok
        $data = [
            'id' => "$folderId",
            'type' => 'folders'
        ];
        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch(sprintf('/trash/%s', $folderId), json_encode(compact('data')));
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $trash = TableRegistry::get('Objects')->get($folderId);
        $this->assertFalse($trash['deleted']);

        // check that all children are restored
        $this->configRequestHeaders();
        $this->get(sprintf('/folders/%s/children', $folderId));
        $actualResult = json_decode((string)$this->_response->getBody(), true);
        static::assertNotEmpty($actualResult['data']);
        static::assertEquals(Hash::extract($expectedChildren, 'data.{n}.id'), Hash::extract($actualResult, 'data.{n}.id'));
    }
}
