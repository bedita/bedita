<?php
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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\UserModifiedBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\UserModifiedBehavior
 */
class UserModifiedBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * Table object instance.
     *
     * @var \BEdita\Core\Model\Table\ObjectsTable
     */
    protected $Objects;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        LoggedUser::setUser(['id' => 1]);
        $this->Objects = TableRegistry::get('Objects');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        LoggedUser::resetUser();
    }

    /**
     * Test behavior initialization process.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $events = [
            'MyCustomEvent' => [
                'field_one' => 'always',
                'field_two' => 'new',
                'field_three' => 'existing',
            ],
        ];

        $behavior = $this->Objects->behaviors()->get('UserModified');
        $behavior->initialize(compact('events'));

        $config = $behavior->getConfig();

        static::assertArraySubset(compact('events'), $config);
    }

    /**
     * Test setting a custom user ID.
     *
     * @return void
     *
     * @covers ::userId()
     */
    public function testUserId()
    {
        $behavior = $this->Objects->behaviors()->get('UserModified');

        static::assertAttributeSame(null, 'userId', $behavior);
        static::assertSame(LoggedUser::id(), $this->Objects->userId());
        static::assertAttributeSame(LoggedUser::id(), 'userId', $behavior);

        static::assertSame(99, $this->Objects->userId(99));
        static::assertSame(99, $this->Objects->userId());
        static::assertAttributeSame(99, 'userId', $behavior);
    }

    /**
     * Test implemented events.
     *
     * @return void
     *
     * @covers ::implementedEvents()
     */
    public function testImplementedEvents()
    {
        $expected = [
            'Model.beforeSave' => 'handleEvent',
        ];

        $behavior = $this->Objects->behaviors()->get('UserModified');

        static::assertEquals($expected, $behavior->implementedEvents());
    }

    /**
     * Test handling of events.
     *
     * @return \BEdita\Core\Model\Entity\ObjectEntity
     *
     * @covers ::handleEvent()
     * @covers ::updateField()
     */
    public function testHandleEvent()
    {
        $object = $this->Objects->newEntity();
        $object->type = 'documents';
        $object = $this->Objects->save($object);

        static::assertSame(1, $object->created_by);
        static::assertSame(1, $object->modified_by);

        return $object;
    }

    /**
     * Test handling of events.
     *
     * @return void
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage When should be one of "always", "new" or "existing". The passed value "sometimes" is invalid
     * @covers ::handleEvent()
     * @covers ::updateField()
     */
    public function testHandleEventFailure()
    {
        $this->Objects->behaviors()->get('UserModified')->setConfig('events', [
            'Model.beforeSave' => [
                'modified_by' => 'sometimes',
            ],
        ], false);

        $object = $this->Objects->newEntity();
        $object->type = 'documents';
        $this->Objects->save($object);
    }

    /**
     * Test "touch" of an entity.
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $object
     * @return void
     *
     * @depends testHandleEvent
     * @covers ::touchUser()
     * @covers ::updateField()
     */
    public function testTouchUser(ObjectEntity $object)
    {
        $this->Objects->userId(99);
        $this->Objects->touchUser($object);

        static::assertSame(LoggedUser::id(), $object->created_by);
        static::assertSame(99, $object->modified_by);
    }

    /**
     * Test "touch" of an entity with an unknown event.
     *
     * @return void
     *
     * @depends testHandleEvent
     * @covers ::touchUser()
     * @covers ::updateField()
     */
    public function testTouchUserUnknownEvent()
    {
        $object = $this->Objects->get(1);

        $this->Objects->userId(99);
        $this->Objects->touchUser($object, 'UnknownEvent');

        static::assertSame(1, $object->created_by);
        static::assertSame(1, $object->modified_by);
    }

    /**
     * Test "touch" of an entity when one of the fields is dirty already.
     *
     * @return void
     *
     * @depends testHandleEvent
     * @covers ::touchUser()
     * @covers ::updateField()
     */
    public function testTouchUserDirtyField()
    {
        $object = $this->Objects->newEntity();
        $object->type = 'documents';
        $object->created_by = 5;
        $this->Objects->saveOrFail($object);

        static::assertSame(5, $object->created_by);
        static::assertSame(1, $object->modified_by);
    }
}
