<?php
namespace BEdita\Core\Test\TestCase\Model\Behavior;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Behavior\TreeBehavior
 */
class TreeBehaviorTest extends TestCase
{
    /**
     * Fixtures.
     *
     * @var string[]
     */
    public $fixtures = [
        'plugin.BEdita/Core.FakeCategories',
    ];

    /**
     * Test table.
     *
     * @var \Cake\ORM\Table|\BEdita\Core\Model\Behavior\TreeBehavior
     */
    public $Table;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Table = TableRegistry::getTableLocator()->get('FakeCategories');
        $this->Table->belongsToMany('ChildCategories', [
            'className' => 'FakeCategories',
            'joinTable' => 'fake_categories',
            'foreignKey' => 'parent_id',
            'targetForeignKey' => 'id',
        ]);
        $this->Table->addBehavior('BEdita/Core.Tree', [
            'left' => 'left_idx',
            'right' => 'right_idx',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Table);

        parent::tearDown();
    }

    /**
     * Data provider for `testGetCurrentPosition` test case.
     *
     * @return array
     */
    public function getCurrentPositionProvider(): array
    {
        return [
            '1st node, root' => [
                1,
                'Science',
            ],
            '2nd node, root' => [
                2,
                'History of Art',
            ],
            '1st node, depth 1' => [
                1,
                'Mathematics',
            ],
            '2nd node, depth 1' => [
                2,
                'Physics',
            ],
            '3rd node, depth 2' => [
                3,
                'Mathematical Logic',
            ],
        ];
    }

    /**
     * Test `getCurrentPosition()` method.
     *
     * @param int $expected Expected position.
     * @param string $name Name of node.
     * @return void
     * @dataProvider getCurrentPositionProvider()
     * @covers ::getCurrentPosition()
     */
    public function testGetCurrentPosition(int $expected, string $name): void
    {
        $node = $this->Table->find()
            ->where(compact('name'))
            ->firstOrFail();

        $position = $this->Table->getCurrentPosition($node);

        static::assertSame($expected, $position);
    }

    /**
     * Data provider for `testMoveAt` test case.
     *
     * @return array
     */
    public function moveAtProvider(): array
    {
        return [
            'first' => [
                1,
                'Mathematical Logic',
                'first',
            ],
            'last' => [
                3,
                'Geometry',
                'last',
            ],
            'positive' => [
                2,
                'Mathematics',
                2,
            ],
            'positive, out of bounds' => [
                3,
                'Geometry',
                999,
            ],
            'positive, unchanged' => [
                2,
                'Algebra',
                2,
            ],
            'negative' => [
                2,
                'Geometry',
                -2,
            ],
            'negative, out of bounds' => [
                1,
                'Algebra',
                -999,
            ],
            'negative, unchanged' => [
                2,
                'History of Art',
                -1,
            ],
            'invalid position' => [
                false,
                'Science',
                'gustavo',
            ],
            'zero' => [
                false,
                'Science',
                '0',
            ],
        ];
    }

    /**
     * Test `moveAt()` method.
     *
     * @param int|false $expected Expected result.
     * @param string $name Name of node.
     * @param int|string $position Position to move node at.
     * @return void
     * @dataProvider moveAtProvider()
     * @covers ::moveAt()
     * @covers ::validatePosition()
     */
    public function testMoveAt($expected, string $name, $position): void
    {
        $node = $this->Table->find()
            ->where(compact('name'))
            ->firstOrFail();

        $previousPosition = $this->Table->getCurrentPosition($node);
        $previousIndexes = [$node->get('left_idx'), $node->get('right_idx')];

        $result = $this->Table->moveAt($node, $position);
        $finalPosition = $this->Table->getCurrentPosition($node);

        if ($expected === false) {
            static::assertFalse($result);
            static::assertSame($previousPosition, $finalPosition);

            $finalIndexes = [$node->get('left_idx'), $node->get('right_idx')];
            self::assertSame($previousIndexes, $finalIndexes);
        } else {
            static::assertInstanceOf(Entity::class, $result);
            static::assertSame($expected, $finalPosition);

            $finalIndexes = [$result->get('left_idx'), $result->get('right_idx')];
            if ($finalPosition === $previousPosition) {
                self::assertSame($previousIndexes, $finalIndexes);
            } else {
                self::assertNotSame($previousIndexes, $finalIndexes);
            }
        }
    }

    /**
     * Test that when a node is moved in another position
     * the actual node position is used.
     *
     * To test it:
     * 1. first get all children of a parent node
     * 2. reverse the position of the children in a loop of prevoius results.
     *
     * When a child is moved to another position its siblings are moved too.
     * So the next child in the loop will have '`left_idx` and `right_idx` not updated.
     * The test assures that `left_idx` and `right_idx` are the actual values and not those stored in the entities.
     *
     * @return void
     * @covers ::moveAt()
     */
    public function testMoveAtUseActualNodePosition(): void
    {
        $parentNode = $this->Table->find()
            ->where(['name' => 'Mathematics'])
            ->firstOrFail();

        $children = $this->Table
            ->find('children', ['for' => $parentNode->id])
            ->all();

        $currentPositions = $children->extract('id')->toList();

        $count = count($currentPositions);
        $expected = array_reverse($currentPositions);

        foreach ($children as $child) {
            $this->Table->moveAt($child, $count--);
        }

        $actual = $this->Table
            ->find('children', ['for' => $parentNode->id])
            ->all()
            ->extract('id')
            ->toList();

        static::assertEquals($expected, $actual);
    }

    /**
     * Test {@see TreeBehavior::checkIntegrity()} method with a sane tree.
     *
     * @return void
     *
     * @covers ::checkIntegrity()
     */
    public function testCheckIntegritySuccess(): void
    {
        $errors = $this->Table->checkIntegrity();

        static::assertEmpty($errors);
    }

    /**
     * Test {@see TreeBehavior::checkIntegrity()} method with a record where `left_idx >= right_idx`.
     *
     * @return void
     *
     * @covers ::checkIntegrity()
     */
    public function testCheckIntegritySwappedIndexes(): void
    {
        $this->Table->updateAll(['right_idx' => 17], ['id' => 9]);
        $expected = [
            'Found record where left_idx >= right_idx',
        ];

        $errors = $this->Table->checkIntegrity();

        static::assertSame($expected, $errors);
    }

    /**
     * Test {@see TreeBehavior::checkIntegrity()} method with a record where there's a gap between a parent's `left_idx`
     * and its first child's `left_idx`.
     *
     * @return void
     *
     * @covers ::checkIntegrity()
     */
    public function testCheckIntegrityFirstChildGap(): void
    {
        $this->Table->updateAll(['left_idx = left_idx + 2'], ['left_idx >=' => 3]);
        $this->Table->updateAll(['right_idx = right_idx + 2'], []);
        $expected = [
            'Found record where parent.left_idx + 1 != MIN(children.left_idx)',
        ];

        $errors = $this->Table->checkIntegrity();

        static::assertSame($expected, $errors);
    }

    /**
     * Test {@see TreeBehavior::checkIntegrity()} method with a record where there's a gap between a parent's `right_idx`
     * and its last child's `right_idx`.
     *
     * @return void
     *
     * @covers ::checkIntegrity()
     */
    public function testCheckIntegrityLastChildGap(): void
    {
        $this->Table->updateAll(['right_idx = right_idx + 2'], ['right_idx >' => 8]);
        $this->Table->updateAll(['left_idx = left_idx + 2'], ['left_idx >' => 7]);
        $expected = [
            'Found record where parent.right_idx - 1 != MAX(children.right_idx)',
        ];

        $errors = $this->Table->checkIntegrity();

        static::assertSame($expected, $errors);
    }

    /**
     * Test {@see TreeBehavior::checkIntegrity()} method with a record where there's a gap between two consecutive siblings.
     *
     * @return void
     *
     * @covers ::checkIntegrity()
     */
    public function testCheckIntegritySiblingsGap(): void
    {
        $this->Table->updateAll(['left_idx = left_idx + 2'], ['left_idx >= 5']);
        $this->Table->updateAll(['right_idx = right_idx + 2'], ['right_idx >= 6']);
        $expected = [
            'Found record where left_idx - 1 != MAX(previousSiblings.right_idx)',
        ];

        $errors = $this->Table->checkIntegrity();

        static::assertSame($expected, $errors);
    }

    /**
     * Test {@see TreeBehavior::checkIntegrity()} method with two siblings overlapping each other.
     *
     * @return void
     *
     * @covers ::checkIntegrity()
     */
    public function testCheckIntegritySiblingsOverlapping(): void
    {
        $this->Table->updateAll(['left_idx = left_idx - 1'], ['left_idx >= 5']);
        $this->Table->updateAll(['right_idx = right_idx - 1'], ['right_idx >= 6']);
        $expected = [
            'Found record where left_idx - 1 != MAX(previousSiblings.right_idx)',
        ];

        $errors = $this->Table->checkIntegrity();

        static::assertSame($expected, $errors);
    }

    /**
     * Test {@see TreeBehavior::beforeDelete()} method with two siblings nodes.
     *
     * @return void
     *
     * @covers ::beforeDelete()
     */
    public function testUnorderedDelete()
    {
        $parentNode = $this->Table->find()
            ->where(['name' => 'Mathematics'])
            ->contain(['ChildCategories'])
            ->firstOrFail();

        $this->Table->ChildCategories->unlink($parentNode, [
            $parentNode->child_categories[0],
            $parentNode->child_categories[2],
        ]);

        $errors = $this->Table->checkIntegrity();

        static::assertEmpty($errors);
    }
}
