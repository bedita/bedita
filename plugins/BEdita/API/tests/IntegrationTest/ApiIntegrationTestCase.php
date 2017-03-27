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

use BEdita\Core\State\CurrentApplication;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Base class for API integration tests
 */
abstract class ApiIntegrationTestCase extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.date_ranges',
        'plugin.BEdita/Core.locations',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        CurrentApplication::setFromApiKey(API_KEY);
    }

    /**
     * Setup request header
     * Defaults:
     *   'Host' => 'api.example.com',
     *   'Accept' => 'application/vnd.api+json',
     *   'Content-Type' => 'application/vnd.api+json' (POST, PATCH, DELETE methods)
     *
     * @param $method string HTTP method
     * @param $options array Header content options
     * @return void
     */
    public function configRequestHeaders($method = 'GET', array $options = [])
    {
        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
        ];

        if (in_array($method, ['POST', 'PATCH', 'DELETE'])) {
            $headers['Content-Type'] = 'application/vnd.api+json';
        }

        $headers = array_merge($headers, $options);
        $this->configRequest(compact('headers'));
    }

    /**
     * Return last Object ID
     *
     * @return int
     */
    public function lastObjectId()
    {
        $lastObject = TableRegistry::get('Objects')->find()->select('id')->order(['id' => 'DESC'])->first();

        return $lastObject->id;
    }
}
