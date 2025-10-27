<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Command\SubcommandTrait;
use Cake\Command\Command;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;

/**
 *  {@see \BEdita\Core\Command\SubcommandTrait} Test Case
 */
#[CoversTrait(SubcommandTrait::class)]
class SubcommandTraitTest extends TestCase
{
    use SubcommandTrait;

    public $functionArguments = [];
    protected $args;
    protected $io;
    protected $subcommands = [
        'foo' => [
            'class' => 'FooCommand',
            'arguments' => [
                'arg1',
                'arg2',
            ],
            'options' => [
                'opt1',
                'opt2',
            ],
        ],
    ];

    public function getArguments(): object
    {
        return $this->args;
    }

    public function executeCommand($class, $args, $io)
    {
        $this->functionArguments = compact('class', 'args', 'io');

        return Command::CODE_SUCCESS;
    }

    public function exSubCmd($subcommand)
    {
        return $this->executeSubcommand($subcommand);
    }

    /**
     * Test `executeSubcommand` method
     *
     * @return void
     */
    public function testExecuteSubcommand(): void
    {
        $this->args = new class () {
            public function getArgument($name): string
            {
                return $name;
            }

            public function getOptions(): array
            {
                return [
                    'opt1' => 'value1',
                    'opt2' => 'value2',
                ];
            }
        };
        $actual = $this->exSubCmd('foo');
        $this->assertSame(Command::CODE_SUCCESS, $actual);
        $this->assertSame([
            'arg1',
            'arg2',
            '--opt1',
            'value1',
            '--opt2',
            'value2',
        ], $this->functionArguments['args']);
    }
}
