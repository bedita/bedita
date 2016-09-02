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
namespace BEdita\API\Network;

use Cake\Network\CorsBuilder as CakeCorsBuilder;
use Psr\Http\Message\ResponseInterface;

/**
 * A builder object that assists in defining Cross Origin Request related
 * headers.
 *
 * Each of the methods in this object provide a fluent interface. Once you've
 * set all the headers you want to use, the `build()` method can be used to return
 * a modified Response.
 *
 * It overrides `Cake\Network\CorsBuilder` to use PSR-7 compliant Response to use in Middlewares
 *
 */
class CorsBuilder extends CakeCorsBuilder
{
    /**
     * The response object this builder is attached to.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $_response;

    /**
     * {@inheritDoc}
     *
     * @param \Psr\Http\Message\ResponseInterface $response The Psr-7 response object.
     * @param string $origin The request's Origin header.
     * @param bool $isSsl Whether or not the request was over SSL.
     */
    public function __construct(ResponseInterface $response, $origin, $isSsl = false)
    {
        $this->_origin = $origin;
        $this->_isSsl = $isSsl;
        $this->_response = $response;
    }

    /**
     * {@inheritDoc}
     *
     * If `Access-Control-Allow-Origin` header is different from `*`
     * add `Origin` to `Vary` header
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function build()
    {
        if (empty($this->_origin) || !isset($this->_headers['Access-Control-Allow-Origin'])) {
            return $this->_response;
        }

        foreach ($this->_headers as $name => $value) {
            $this->_response = $this->_response->withHeader($name, $value);
        }

        if ($this->_headers['Access-Control-Allow-Origin'] != '*') {
            $this->_response = $this->_response->withAddedHeader('Vary', 'Origin');
        }

        return $this->_response;
    }
}
