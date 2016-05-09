<?php
namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Model\Behavior\ClassTableInheritanceBehavior;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Behavior\ClassTableInheritanceBehavior Test Case
 */
class ClassTableInheritanceBehaviorTest extends TestCase
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
        'plugin.BEdita/Core.fake_infos',
        'plugin.BEdita/Core.fake_articles',
    ];

    /**
     * Table FakeAnimals
     *
     * @var \Cake\ORM\Table
     */
    public $fakeAnimals;

    /**
     * Table FakeMammals
     *
     * @var \Cake\ORM\Table
     */
    public $fakeMammals;

    /**
     * Table FakeFelines
     *
     * @var \Cake\ORM\Table
     */
    public $fakeFelines;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->fakeAnimals = TableRegistry::get('FakeAnimals');
        $this->fakeAnimals->hasMany('FakeArticles');

        $this->fakeMammals = TableRegistry::get('FakeMammals');
        $this->fakeMammals->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => 'FakeAnimals'
        ]);

        $this->fakeFelines = TableRegistry::get('FakeFelines');
        $this->fakeFelines->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => 'FakeMammals'
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->fakeAnimals);
        unset($this->fakeMammals);
        unset($this->fakeFelines);
        TableRegistry::clear();

        parent::tearDown();
    }

    /**
     * Test inherited tables
     *
     * @return void
     */
    public function testInheritedTables()
    {
        $mammalsInheritance = $this->fakeMammals->inheritedTables();
        $this->assertEquals(['FakeAnimals'], array_column($mammalsInheritance, 'alias'));

        $felinesInheritance = $this->fakeFelines->inheritedTables();
        $this->assertEquals(['FakeMammals'], array_column($felinesInheritance, 'alias'));

        $felinesDeepInheritance = $this->fakeFelines->inheritedTables(true);
        $this->assertEquals(['FakeMammals', 'FakeAnimals'], array_column($felinesDeepInheritance, 'alias'));

        $this->assertTrue($this->fakeFelines->isTableInherited('FakeAnimals', true));
        $this->assertFalse($this->fakeFelines->isTableInherited('FakeAnimals'));
        $this->assertTrue($this->fakeFelines->isTableInherited('FakeMammals', true));
        $this->assertTrue($this->fakeFelines->isTableInherited('FakeMammals'));
    }

    /**
     * Data provider for `testBuildContainString` test case.
     *
     * @return array
     */
    public function containStringProvider()
    {
        return [
            // start value, expected
            ['FakeMammals', 'FakeMammals'],
            ['FakeAnimals', 'FakeMammals.FakeAnimals'],
            ['FakeArticles', 'FakeMammals.FakeAnimals.FakeArticles'],
            ['WrongAssociation', false]
        ];
    }

    /**
     * Test build contain string
     *
     * @dataProvider containStringProvider
     *
     * @return void
     */
    public function testBuildContainString($string, $expected)
    {
        $containString = $this->fakeFelines->buildContainString($string);
        $this->assertEquals($expected, $containString);
    }

    /**
     * Test basic find
     *
     * @return void
     */
    public function testBasicFind()
    {
        // find felines
        $felines = $this->fakeFelines->find();
        $this->assertEquals(1, $felines->count());

        $feline = $felines->first();
        $expected = [
            'id' => 1,
            'name' => 'cat',
            'legs' => 4,
            'subclass' => 'Eutheria',
            'family' => 'purring cats'
        ];
        $result = $feline->extract($felines->first()->visibleProperties());
        $this->assertEquals(ksort($expected), ksort($result));

        $this->assertFalse($feline->dirty());

        // hydrate false
        $felines = $this->fakeFelines->find()->hydrate(false);
        $this->assertEquals(1, $felines->count());

        $result = $felines->first();
        $this->assertEquals(ksort($expected), ksort($result));

        // find mammals
        $mammals = $this->fakeMammals->find()->hydrate(false);
        $this->assertEquals(2, $mammals->count());

        $expected = [
            [
                'id' => 1,
                'name' => 'cat',
                'legs' => 4,
                'subclass' => 'Eutheria'
            ],
            [
                'id' => 2,
                'name' => 'koala',
                'legs' => 4,
                'subclass' => 'Marsupial'
            ]
        ];
        $expected = array_map(function ($a) {
            ksort($a);
            return $a;
        }, $expected);

        $result = array_map(function ($a) {
            ksort($a);
            return $a;
        }, $mammals->toArray());
        $this->assertEquals($expected, $result);
    }

    public function testContainFind()
    {
        $felines = $this->fakeFelines
            ->find()
            ->contain('FakeArticles');
        $this->assertEquals(1, $felines->count());

        $feline = $felines->first();

        $this->assertTrue($feline->has('fake_articles'));
        $this->assertEquals(2, count($feline->fake_articles));
        $this->assertFalse($feline->dirty());

        $expected = [
            'id' => 1,
            'name' => 'cat',
            'legs' => 4,
            'subclass' => 'Eutheria',
            'family' => 'purring cats'
        ];
    }
}
