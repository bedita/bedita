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

namespace BEdita\API\Test\TestCase\Network\Exception;

use BEdita\API\Network\Exception\UnsupportedMediaTypeException;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\API\Network\Exception\UnsupportedMediaTypeException
 */
class UnsupportedMediaTypeExceptionTest extends TestCase
{

    /**
     * Test `__construct()` method.
     *
     * @return void
     */
    public function testDefaults()
    {
        $exception = new UnsupportedMediaTypeException();

        $this->assertEquals('Unsupported Media Type', $exception->getMessage());
        $this->assertEquals(415, $exception->getCode());
    }

    /**
     * Test `__construct()` method.
     *
     * @return void
     */
    public function testCustomParams()
    {
        $exception = new UnsupportedMediaTypeException('My custom message', -1);

        $this->assertEquals('My custom message', $exception->getMessage());
        $this->assertEquals(-1, $exception->getCode());
    }
}
