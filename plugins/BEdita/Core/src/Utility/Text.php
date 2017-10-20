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

namespace BEdita\Core\Utility;

use Cake\Utility\Text as CakeText;

class Text extends CakeText
{

    /**
     * Null UUID.
     *
     * @var string
     *
     * @see https://www.ietf.org/rfc/rfc4122.txt
     */
    const UUID_NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * Namespace for names that are fully-qualified domain names.
     *
     * @var string
     *
     * @see https://www.ietf.org/rfc/rfc4122.txt
     */
    const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Namespace for names that are URLs.
     *
     * @var string
     *
     * @see https://www.ietf.org/rfc/rfc4122.txt
     */
    const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Namespace for names that are ISO OIDs.
     *
     * @var string
     *
     * @see https://www.ietf.org/rfc/rfc4122.txt
     */
    const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Namespace for names that are X.500 DNs (in DER or a text output format).
     *
     * @var string
     *
     * @see https://www.ietf.org/rfc/rfc4122.txt
     */
    const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Utility function to convert hex into bin for a UUID.
     *
     * @param string $uuid A UUID to convert into binary format.
     * @return string
     *
     * @copyright Matt Farina MIT License https://github.com/lootils/uuid/blob/master/LICENSE
     */
    protected static function uuidToBin($uuid)
    {
        static $pattern = '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i';
        if (preg_match($pattern, $uuid) !== 1) {
            throw new \LogicException(__d('bedita', 'The UUID provided for the namespace is not valid.'));
        }

        // Get hexadecimal components of namespace
        $hex = str_replace('-', '', $uuid);

        // Convert to bits
        $bin = '';
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $bin .= chr(hexdec($hex[$i] . $hex[$i+1]));
        }

        return $bin;
    }

    /**
     * Generate a UUID version 5.
     *
     * @param string $name Name to generate UUID v5 for.
     * @param string $namespace A valid UUID to be used as namespace.
     * @return string
     *
     * @see https://www.ietf.org/rfc/rfc4122.txt
     * @copyright Matt Farina MIT License https://github.com/lootils/uuid/blob/master/LICENSE
     */
    public static function uuid5($name, $namespace = self::UUID_NIL)
    {
        $bin = static::uuidToBin($namespace);

        $hash = sha1($bin . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',
            // 32 bits for "time_low"
            substr($hash, 0, 8),
            // 16 bits for "time_mid"
            substr($hash, 8, 4),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 5
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }
}
