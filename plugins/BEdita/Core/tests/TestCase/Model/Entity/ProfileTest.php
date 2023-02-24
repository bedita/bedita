<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\Profile;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Profile} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Profile
 */
class ProfileTest extends TestCase
{
    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\ProfilesTable
     */
    public $Profiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Profiles = TableRegistry::getTableLocator()->get('Profiles');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Profiles);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
     */
    public function testAccessible()
    {
        $profile = $this->Profiles->get(4);

        $data = [
            'id' => 42,
            'name' => 'Gust',
        ];
        $profile = $this->Profiles->patchEntity($profile, $data);
        if (!($profile instanceof Profile)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(4, $profile->id);
    }

    /**
     * Test translatable properties.
     *
     * @return void
     * @covers ::__construct()
     */
    public function testTranslatable(): void
    {
        $profile = $this->Profiles->newEmptyEntity();
        $this->assertFalse($profile->isFieldTranslatable('surname'));
    }

    /**
     * Data provider for `testSetUrl` test case.
     *
     * @return array
     */
    public function setUrlProvider(): array
    {
        return [
            'ok' => [
                'https://www.example.com/?gustavo=supporto',
                'https://www.example.com/?gustavo=supporto',
            ],
            'non-standard' => [
                'http://www.example.com/hello/world.html',
                'www.example.com/hello/world.html',
            ],
            'not valid' => [
                'I am not a valid URL',
                'I am not a valid URL',
            ],
            'not a string' => [
                123,
                123,
            ],
        ];
    }

    /**
     * Test that Website URL is correctly standardized.
     *
     * @param mixed $expected Expected result.
     * @param mixed $website Website value.
     * @return void
     * @dataProvider setUrlProvider()
     * @covers ::_setWebsite()
     */
    public function testSetUrl($expected, $website): void
    {
        $profile = $this->Profiles->newEntity([]);
        $profile->website = $website;

        $actual = $profile->website;

        static::assertSame($expected, $actual);
    }
}
