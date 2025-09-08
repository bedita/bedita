<?php
declare(strict_types=1);

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
use BEdita\Core\Model\Table\ProfilesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Entity\Profile} Test Case
 */
#[CoversClass(Profile::class)]
class ProfileTest extends TestCase
{
    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\ProfilesTable
     */
    public ProfilesTable $Profiles;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
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
     */
    #[CoversNothing]
    public function testAccessible()
    {
        $profile = $this->Profiles->get(4);

        $data = [
            'id' => 42,
            'name' => 'Gust',
        ];
        $profile = $this->Profiles->patchEntity($profile, $data);
        if (!($profile instanceof Profile)) {
            throw new InvalidArgumentException();
        }

        $this->assertEquals(4, $profile->id);
    }

    /**
     * Test translatable properties.
     *
     * @return void
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
    public static function setUrlProvider(): array
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
     */
    #[DataProvider('setUrlProvider')]
    public function testSetUrl($expected, $website): void
    {
        $profile = $this->Profiles->newEmptyEntity();
        $profile->website = $website;

        $actual = $profile->website;

        static::assertSame($expected, $actual);
    }
}
