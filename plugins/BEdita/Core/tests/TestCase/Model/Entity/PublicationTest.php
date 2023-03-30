<?php
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
 * @coversDefaultClass \BEdita\Core\Model\Entity\Publication
 */
class PublicationTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Publications',
    ];

    /**
     * Test translatable properties.
     *
     * @return void
     * @covers ::__construct()
     */
    public function testTranslatable(): void
    {
        /** @var \BEdita\Core\Model\Entity\Publication $publication */
        $publication = $this->fetchTable('Publications')->newEmptyEntity();
        $this->assertFalse($publication->isFieldTranslatable('public_url'));
    }
}
