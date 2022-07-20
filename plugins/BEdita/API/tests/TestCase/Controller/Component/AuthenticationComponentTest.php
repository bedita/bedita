<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Controller\Component;

use Authentication\AuthenticationService;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use BEdita\API\Controller\Component\AuthenticationComponent;
use BEdita\API\Exception\ExpiredTokenException;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Firebase\JWT\ExpiredException;

/**
 * AuthenticationComponent Test Case
 *
 * @coversDefaultClass \BEdita\API\Controller\Component\AuthenticationComponent
 */
class AuthenticationComponentTest extends TestCase
{
    /**
     * Data provider for `testCheckExpiredToken` test case.
     *
     * @return array
     */
    public function checkExpiredTokenProvider()
    {
        return [
            'ok' => [
                true,
                null,
            ],
            'result without exception' => [
                true,
                new Result(null, ResultInterface::FAILURE_CREDENTIALS_INVALID),
            ],
            'expired exception' => [
                new ExpiredTokenException(),
                new Result(
                    null,
                    ResultInterface::FAILURE_CREDENTIALS_INVALID,
                    ['exception' => new ExpiredException()]
                ),
            ],
        ];
    }

    /**
     * Test `checkExpiredToken()` method
     *
     * @param true|\Exception $expected Expected success.
     * @param \Authorization\Policy\ResultInterface|null $result Authentication result.
     * @return void
     * @dataProvider checkExpiredTokenProvider
     * @covers ::checkExpiredToken()
     * @covers ::initialize()
     */
    public function testCheckExpiredToken($expected, ?ResultInterface $result): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $request = new ServerRequest();
        $service = new class (compact('result')) extends AuthenticationService {
            public function getResult(): ?ResultInterface
            {
                return $this->getConfig('result');
            }
        };
        $request = $request->withAttribute('authentication', $service);
        $component = new AuthenticationComponent(new ComponentRegistry(new Controller($request)));
        static::assertNotEmpty($component);
        static::assertTrue($expected);
    }
}
