<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\History;

/**
 * Interface for objects history operations.
 *
 * @since 4.1.0
 */
interface HistoryInterface
{
    /**
     * Add history event data.
     *
     * $data array MUST contain this keys
     *  - 'object_id' - Objecd ID
     *  - 'application_id' - Application ID in use
     *  - 'user_id' - User performing change action
     *  - 'changed' - array data changed by user
     *  - 'created' - change time
     *  - 'user_action' - change action, one of 'create', 'update', 'trash', 'restore', 'remove'
     *
     * @param array $data History data.
     * @return void
     */
    public function addEvent(array $data): void;

    /**
     * Read history event data of a single object.
     *
     * @param int|string $objectId Object ID.
     * @param array $options Read options.
     * @return array History event data
     */
    public function readEvents($objectId, array $options = []): array;

    /**
     * Read history event data of a single user.
     *
     * @param int|string $userId User ID.
     * @param array $options Read options.
     * @return array History event data
     */
    public function readUserEvents($userId, array $options = []): array;
}
