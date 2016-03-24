<?php

namespace BEdita\Auth\Auth;

use Cake\Auth\AbstractPasswordHasher;

/**
 * MD5 Password hasher
 *
 * @deprecated Added for backwards-compatibility with BEdita 3 users.
 */
class LegacyMd5PasswordHasher extends AbstractPasswordHasher
{

    /**
     * Generates password hash.
     *
     * @param string|array $password Plain text password to hash or array of data
     *   required to generate password hash.
     * @return string Password hash
     */
    public function hash($password)
    {
        return md5($password);
    }

    /**
     * Check hash. Generate hash from user provided password string or data array
     * and check against existing hash.
     *
     * @param string|array $password Plain text password to hash or data array.
     * @param string $hashedPassword Existing hashed password.
     * @return bool True if hashes match else false.
     */
    public function check($password, $hashedPassword)
    {
        return md5($password) === $hashedPassword;
    }
}
