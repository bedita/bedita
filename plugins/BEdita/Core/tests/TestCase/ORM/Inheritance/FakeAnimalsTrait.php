<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\ORM\Inheritance;

use BEdita\Core\ORM\Inheritance\Table;
use Cake\ORM\TableRegistry;

trait FakeAnimalsTrait
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals',
        'plugin.BEdita/Core.fake_mammals',
        'plugin.BEdita/Core.fake_felines',
        'plugin.BEdita/Core.fake_articles',
    ];

    /**
     * Table FakeAnimals
     *
     * @var \BEdita\Core\ORM\Inheritance\Table
     */
    public $fakeAnimals;

    /**
     * Table FakeMammals
     *
     * @var \BEdita\Core\ORM\Inheritance\Table
     */
    public $fakeMammals;

    /**
     * Table FakeFelines
     *
     * @var \BEdita\Core\ORM\Inheritance\Table
     */
    public $fakeFelines;

    /**
     * Table options used for initialization
     *
     * @var array
     */
    protected $tableOptions = ['className' => Table::class];

    /**
     * Setup Tables
     *
     * @return void
     */
    public function setupTables()
    {
        $this->fakeFelines = TableRegistry::get('FakeFelines', $this->tableOptions);
        $this->fakeMammals = TableRegistry::get('FakeMammals', $this->tableOptions);
        $this->fakeAnimals = TableRegistry::get('FakeAnimals');
    }

    /**
     * Setup inheritance associations
     *
     * @return void
     */
    protected function setupAssociations()
    {
        $this->fakeMammals->extensionOf('FakeAnimals');
        $this->fakeFelines->extensionOf('FakeMammals');
        $this->fakeAnimals->hasMany('FakeArticles', ['dependent' => true]);
    }
}
