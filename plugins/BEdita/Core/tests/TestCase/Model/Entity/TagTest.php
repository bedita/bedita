<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;

/**
 *  {@see \BEdita\Core\Model\Entity\Tag} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Tag
 */
class TagTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Tags',
    ];

    /**
     * Test `_getLabel` methods.
     *
     * @return void
     * @covers ::_getLabel()
     */
    public function testGetLabel(): void
    {
        $tag = $this->fetchTable('Tags')->get(1);
        static::assertEquals('First tag', $tag->get('label'));
    }

    /**
     * Test `_setLabel` methods.
     *
     * @return void
     * @covers ::_getLabel()
     * @covers ::_setLabel()
     */
    public function testSetLabel(): void
    {
        $tag = $this->fetchTable('Tags')->newEmptyEntity();
        $tag->set('label', 'New label');
        static::assertEquals('New label', $tag->get('label'));
    }
}
