<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2014 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * Basic event manager for BEdita.
 */
class BeCallbackManager {
    /**
     * Array of currently attached listeners.
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Attaches a listener to an event.
     *
     * @param string $eventName Name of the event to be listened.
     * @param mixed $listener Callback or full qualified name of listener.
     * @return boolean Success.
     */
    public function bind($eventName, $listener) {
        if (!array_key_exists($eventName, $this->listeners)) {
            $this->listeners[$eventName] = array();
        }

        array_push($this->listeners[$eventName], $listener);
        return true;
    }

    /**
     * Detaches a listener from an event.
     *
     * @param string $eventName Name of the event to be unbinded.
     * @param mixed $listener Callback or full qualified name of callback.
     * @return boolean Success.
     */
    public function unbind($eventName = null, $listener = null) {
        if (!empty($eventName)) {
            // Event name specified.
            if (!array_key_exists($eventName, $this->listeners)) {
                // No listeners binded to event.
                return false;
            }

            if (empty($listener)) {
                // Clear all listeners.
                unset($this->listeners[$eventName]);
                return true;
            }

            // Clear specified listener.
            $key = array_search($listener, $this->listeners[$eventName]);
            if ($key === false) {
                return false;
            }
            unset($this->listeners[$eventName][$key]);
            if (!count($this->listeners[$eventName])) {
                unset($this->listeners[$eventName]);
            }
            return true;
        }

        if (!empty($listener)) {
            // No event name specified, but specified listener.
            foreach ($this->listeners as $eventName => $listeners) {
                $key = array_search($listener, $this->listeners[$eventName]);
                if ($key === false) {
                    continue;
                }
                unset($this->listeners[$eventName][$key]);

                if (!count($this->listeners[$eventName])) {
                    unset($this->listeners[$eventName]);
                }
            }
            return true;
        }

        // Reset all.
        $this->listeners = array();
        return true;
    }

    /**
     * Triggers a new event.
     *
     * @param string $eventName Name of the event to be triggered.
     * @param mixed $eventData Data of the event.
     * @return stdClass Class representing the event triggered, with `name`, `data`, `result` and `stopped` keys.
     */
    public function trigger($eventName, $eventData = null) {
        $event = $this->initEvent($eventName, $eventData);

        if (!array_key_exists($event->name, $this->listeners)) {
            return $event;
        }
        foreach ($this->listeners[$event->name] as $listener) {
            if (is_array($listener)) {
                list($class, $method) = $listener;
                $listener = array(ClassRegistry::getObject($class), $method);
            }
            $res = call_user_func_array($listener, $event->data + array($event));
            $event->result = $res;

            if ($event->result === false || !empty($event->stopped)) {
                break;
            }
        }
        return $event;
    }

    /**
     * Initializes a new event.
     *
     * @param string $eventName Event name.
     * @param mixed $eventData Event data.
     * @return stdClass Event.
     */
    private function initEvent($eventName, $eventData) {
        $event = new stdClass();
        $event->name = $eventName;
        $event->data = $eventData;
        $event->stopPropagation = function() use ($event) {
            $event->stopped = true;
        };
        $event->result = null;

        return $event;
    }
}