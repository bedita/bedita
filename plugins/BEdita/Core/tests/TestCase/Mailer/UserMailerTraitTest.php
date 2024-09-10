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

namespace BEdita\Core\Test\TestCase\Mailer;

use BEdita\Core\Mailer\UserMailerInterface;
use BEdita\Core\Mailer\UserMailerTrait;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\TestSuite\TestCase;
use LogicException;

/**
 * Test Mailer class.
 */
class TestMailer extends Mailer
{
}

/**
 * {@see \BEdita\Core\Mailer\UserMailerTrait} Test Case.
 *
 * @coversDefaultClass \BEdita\Core\Mailer\UserMailerTrait
 */
class UserMailerTraitTest extends TestCase
{
    use UserMailerTrait;

    /**
     * Test `getUserMailer` failure.
     *
     * @return void
     * @covers ::getUserMailer()
     */
    public function testGetUserMailerFailure(): void
    {
        Configure::write('Mailer.User', TestMailer::class);
        $this->expectException(LogicException::class);
        $msg = sprintf('Mailer class "%s" must implement UserMailerInterface', TestMailer::class);
        $this->expectExceptionMessage($msg);
        $this->getUserMailer();
        Configure::delete('Mailer.User');
    }

    /**
     * Test `getUserMailer`
     *
     * @return void
     * @covers ::getUserMailer()
     */
    public function testGetUserMailer(): void
    {
        $mailer = $this->getUserMailer();
        static::assertInstanceOf(UserMailerInterface::class, $mailer);
    }
}
