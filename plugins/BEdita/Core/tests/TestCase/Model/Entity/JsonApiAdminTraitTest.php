<?php
declare(strict_types=1);

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
namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\JsonApiAdminTrait;
use BEdita\Core\Model\Table\ApplicationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;

/**
 *  {@see \BEdita\Core\Model\Entity\JsonApiAdminTrait} Test Case
 */
#[CoversTrait(JsonApiAdminTrait::class)]
class JsonApiAdminTraitTest extends TestCase
{
    /**
     * Helper table.
     *
     * @var \BEdita\Core\Model\Table\ApplicationsTable
     */
    public ApplicationsTable $Applications;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.Applications',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Applications = TableRegistry::getTableLocator()->get('Applications');

        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Applications);

        parent::tearDown();
    }

    /**
     * Test getter for meta.
     *
     * @return void
     */
    public function testGetLinks()
    {
        $expected = [
            'self' => '/admin/applications/1',
        ];

        $application = $this->Applications->get(1)->jsonApiSerialize();

        $links = $application['links'];

        static::assertEquals($expected, $links);
    }
}
