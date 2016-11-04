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

namespace BEdita\Core\Test\TestCase\ORM;

use BEdita\Core\ORM\Locator\TableLocator;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\ORM\Locator\TableLocator
 */
class TableLocatorTest extends TestCase
{

    /**
     * Table locator instance.
     *
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    protected $TableLocator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->TableLocator = new TableLocator();
    }

    /**
     * Data provider for `testGetClassName` test case.
     *
     * @return array
     */
    public function getClassNameProvider()
    {
        return [
            'withPluginName' => [
                'BEdita\Core\Model\Table\RolesTable',
                'BEdita/Core.Roles',
            ],
            'fallbackBEditaCore' => [
                'BEdita\Core\Model\Table\RolesTable',
                'Roles',
            ],
            'fallback' => [
                'Cake\ORM\Table',
                'ThisTableDoesNotExists',
            ],
            'fallbackWithPluginName' => [
                'Cake\ORM\Table',
                'BEdita/Core.ThisTableDoesNotExists',
            ],
        ];
    }

    /**
     * Test class name finder.
     *
     * @param string $expected Expected class name of table instance.
     * @param string $alias Table alias.
     * @param array $options Table options.
     * @return void
     *
     * @dataProvider getClassNameProvider()
     */
    public function testGetClassName($expected, $alias, array $options = [])
    {
        $result = $this->TableLocator->get($alias, $options);

        $this->assertInstanceOf($expected, $result);
    }
}
