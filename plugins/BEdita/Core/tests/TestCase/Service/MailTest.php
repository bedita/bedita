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
namespace BEdita\Core\Test\TestCase\Service;

use BEdita\Core\Service\Mail;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Service\Mail} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Service\Mail
 */
class MailTest extends TestCase
{
    protected $mailService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->mailService = new Mail();
    }

    /**
     * Test run method
     *
     * @return void
     * @covers ::run()
     */
    public function testRun()
    {
        $result = $this->mailService->run([]);
        $this->assertTrue($result);
    }
}
