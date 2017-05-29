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

namespace BEdita\Core\Test\TestCase\Model\Validation;

use BEdita\Core\Model\Validation\CustomUrlValidationProvider;
use Cake\TestSuite\TestCase;
use Cake\Validation\Validator;

/**
 * {@see \BEdita\Core\Model\Validation\CustomUrlValidationProvider} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Validation\CustomUrlValidationProvider
 */
class CustomUrlValidationProviderTest extends TestCase
{

    /**
     * Test `isValidUrl`.
     *
     * @return void
     *
     * @covers ::isValidUrl()
     */
    public function testValidUrl()
    {
        $provider = new CustomUrlValidationProvider();

        $result = $provider->isValidUrl('https://example.com');
        $this->assertTrue($result);

        $result = $provider->isValidUrl('myapp://example.com');
        $this->assertTrue($result);

        $result = $provider->isValidUrl('https:example.com');
        $this->assertFalse($result);

        $result = $provider->isValidUrl('https://examplecom');
        $this->assertFalse($result);

        $result = $provider->isValidUrl('https://example.com', ['providers' => ['default' => new Validator()]]);
        $this->assertInstanceOf(Validator::class, $result);
    }
}
