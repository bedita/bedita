<?php

App::import('Lib', 'BeLib');

class FakeListener {

    public static $value = null;

    public static function returnMe($value) {
        self::$value = $value;
        return $value;
    }

    public static function stopMeOne($stop) {
        return $stop;
    }

    public static function stopMeTwo($stop, $event) {
        if ($stop) {
            $event->stopPropagation->__invoke();
        }
    }
}

class BeCallbackManagerTestCase extends CakeTestCase {

    public $manager = null;

    public function start() {
        $this->manager = BeLib::eventManager();
    }

    public function endTest($method) {
        $this->manager->unbind();
        FakeListener::$value = null;
    }

    public function testListenerString() {
        $this->manager->bind('Event.one', 'string');
        $this->expectException('BeditaInternalErrorException');
        $this->manager->trigger('Event.one');
    }

    public function testListenerMissingObjectFromRegistry() {
        $this->manager->bind('Event.one', array('None', 'method'));
        $this->expectException('BeditaInternalErrorException');
        $this->manager->trigger('Event.one');
    }

    public function testTrigger() {
        // test closure
        $this->manager->bind('Event.one', function($arg) {
           return $arg;
        });
        $eventData = array('name' => 'John', 'surname' => 'Smith');
        $event = $this->manager->trigger('Event.one', array($eventData));
        $this->assertIdentical($eventData, $event->result);

        $document = ClassRegistry::init('Document');

        // test listener using class name in the registry
        $modelBinding = array(
            'BEObject' => array('Category')
        );
        $this->manager->bind('Document.test', array('Document', 'setBindingsLevel'));
        $this->manager->trigger('Document.test', array('test', $modelBinding));
        $this->assertIdentical($modelBinding, $document->getBindingsLevel('test'));

        // test listener using instance
        $modelBinding = array('BEObject');
        $this->manager->bind('Document.test2', array($document, 'setBindingsLevel'));
        $this->manager->trigger('Document.test2', array('test2', $modelBinding));
        $this->assertIdentical($modelBinding, $document->getBindingsLevel('test2'));

        // test listener using static signature class::method
        $this->manager->bind('static', 'FakeListener::returnMe');
        $expected = 'working';
        $event = $this->manager->trigger('static', array($expected));
        $this->assertIdentical($expected, $event->result);

        // test listener using array with class not in the registry
        $this->manager->bind('static2', array('FakeListener', 'returnMe'));
        $expected = 'working again';
        $event = $this->manager->trigger('static2', array($expected));
        $this->assertIdentical($expected, $event->result);
    }

    public function testStopPropagation() {
        // not stop
        $this->manager->bind('stop.no', array('FakeListener', 'stopMeOne'));
        $this->manager->bind('stop.no', array('FakeListener', 'returnMe'));
        $expected = 'notStopped';
        $this->manager->trigger('stop.no', array($expected));
        $this->assertIdentical($expected, FakeListener::$value);

        // stop one
        FakeListener::$value = null;
        $this->manager->bind('stop.yes', array('FakeListener', 'stopMeOne'));
        $this->manager->bind('stop.yes', array('FakeListener', 'returnMe'));
        $expected = 'notStopped';
        $event = $this->manager->trigger('stop.yes', array(false));
        $this->assertFalse($event->result);
        $this->assertIdentical(null, FakeListener::$value);

        // stop two
        FakeListener::$value = null;
        $this->manager->bind('stop.yes2', array('FakeListener', 'stopMeTwo'));
        $this->manager->bind('stop.yes2', array('FakeListener', 'returnMe'));
        $expected = 'notStopped';
        $event = $this->manager->trigger('stop.yes2', array(true));
        $this->assertTrue($event->stopped);
        $this->assertIdentical(null, FakeListener::$value);
    }

}
