<?php
declare(strict_types=1);

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
namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\BaseAction;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Model\Action\BaseAction} Test Case
 */
#[CoversClass(BaseAction::class)]
class BaseActionTest extends TestCase
{
    /**
     * Test constructor method.
     *
     * @return void
     */
    public function testConstruct()
    {
        $config = [
            'key' => 'value',
        ];

        $baseAction = new class ($config) extends BaseAction {
            public function execute(array $data = []): mixed
            {
                return $data;
            }
        };

        static::assertEquals($config, $baseAction->getConfig());
    }

    /**
     * Test magic method for invoking command.
     *
     * @return void
     */
    public function testInvoke()
    {
        $baseAction = new class () extends BaseAction {
            public function execute(array $data = []): mixed
            {
                return $data;
            }
        };

        $data = [
            'key' => 'value',
        ];

        $result = $baseAction->__invoke($data);

        static::assertEquals($data, $result);
    }
}
