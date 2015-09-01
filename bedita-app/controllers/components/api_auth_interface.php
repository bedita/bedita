<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2015 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * Describe the methods that every API Auth Component should comply with
 * API auth should be a token based authentication that uses 'access_token' and 'refresh_token'
 */
interface ApiAuthInterface {

    /**
     * Identify and return an user starting from an access_token
     * Return false if it's not possible to identify a user
     *
     * @return array|bool Either array of user information or false
     */
    public function identify();

    /**
     * Authenticate a user based on username and password
     *
     * @param string $username the username
     * @param string $password the user password
     * @param array $authGroupName an array of groups authorized to access API
     * @return bool
     */
    public function authenticate($username, $password, array $authGroupName = array());

    /**
     * Generate and return a new access_token
     * If user is not identified/authenticated it returns null
     *
     * @return string|null
     */
    public function generateToken();

    /**
     * Renew an access_token using a refresh token
     * If it fails then return false
     *
     * @param string $refreshToken the refresh token
     * @return string|bool
     */
    public function renewToken($refreshToken);

    /**
     * Generate a refresh token to use for renew an access_token
     * The refresh token should be saved in hash_jobs table
     * If user is not identified/authenticated then return false
     *
     * @return string|bool
     */
    public function generateRefreshToken();

    /**
     * Revoke a refresh token
     * If user is not identified/authenticated then return false
     *
     * @param string $refreshToken the refresh token to remove
     * @return bool
     */
    public function revokeRefreshToken($refreshToken);

    /**
     * Return the access_token reading from Authorization header or from query url
     * Return false if no token is found
     *
     * @return string|bool
     */
    public function getToken();

    /**
     * Return the updated time to access_token expiration (in seconds)
     *
     * @return int
     */
    public function expiresIn();

    /**
     * Return the userid
     * It replaces BeAuthComponent::userid() in API context
     *
     * @return string
     */
    public function userid();

    /**
     * Return the user data
     * it replaces BeAuthComponent::getUserSession() in API context
     *
     * @return array
     */
    public function getUserSession();

    /**
     * Get the current identified/authenticated user
     * It replaces BeAuthComponent::getUser() in API context
     *
     * @return array
     */
    public function getUser();

}
