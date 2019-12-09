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

namespace BEdita\API\Test\TestCase\Error;

use BEdita\API\Error\ErrorHandler;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Error\ErrorHandler
 */
class ErrorHandlerTest extends TestCase
{

    /**
     * Data provider for `testDisplayError` test case.
     *
     * @return array
     */
    public function displayErrorProvider()
    {
        return [
            'simple' => [
                new \LogicException(' [8192] Very bad coder!'),
                8192,
                'Very bad coder!',
                true,
            ],
            'debug' => [
                true,
                1024,
                'Error',
                false,
            ],
        ];
    }

    /**
     * Test `_displayError` method
     *
     * @dataProvider displayErrorProvider
     * @covers ::_displayError()
     * @return void
     */
    public function testDisplayError($expected, $code, $description, $debug)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $current = Configure::read('debug');

        Configure::write('debug', $debug);

        $handler = new ErrorHandler();
        $result = $handler->handleError($code, $description);
        static::assertSame($expected, $result);

        Configure::write('debug', $current);
    }
}
