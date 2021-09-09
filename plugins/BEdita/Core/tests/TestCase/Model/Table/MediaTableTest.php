<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\MediaTable Test Case
 */
class MediaTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\MediaTable
     */
    public $Media;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Media = TableRegistry::getTableLocator()->get('Media');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Media);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'valid' => [
                false,
                [
                    'name' => 'Cool media file',
                    'width' => null,
                    'height' => null,
                    'duration' => null,
                    'provider' => null,
                    'provider_uid' => null,
                    'provider_url' => null,
                    'provider_thumbnail' => null,
                ],
            ],
            'notUniqueUname' => [
                true,
                [
                    'name' => 'Cooler media file',
                    'width' => null,
                    'height' => null,
                    'duration' => null,
                    'provider' => null,
                    'provider_uid' => null,
                    'provider_url' => null,
                    'provider_thumbnail' => null,
                    'uname' => 'media-one',
                ],
            ],
        ];
    }

    /**
     * Test entity save.
     *
     * @param bool $changed
     * @param array $data
     * @return void
     * @dataProvider saveProvider
     * @coversNothing
     */
    public function testSave(bool $changed, array $data)
    {
        $entity = $this->Media->newEntity($data);
        $entity->object_type_id = 9;
        $success = (bool)$this->Media->save($entity);

        $this->assertTrue($success);

        if ($changed) {
            $this->assertNotEquals($data['uname'], $entity->uname);
        } elseif (isset($data['uname'])) {
            $this->assertEquals($data['uname'], $entity->uname);
        }
    }
}
