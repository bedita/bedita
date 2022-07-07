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
namespace BEdita\API\Identifier;

use Authentication\Identifier\JwtSubjectIdentifier as CakeJwtSubjectIdentifier;
use Cake\Utility\Hash;

/**
 * Extends base JwtSubjectIdentifier to allow to build conditions using dot style for `dataField`.
 */
class JwtSubjectIdentifier extends CakeJwtSubjectIdentifier
{
    /**
     * @inheritDoc
     */
    public function identify(array $data)
    {
        $dataField = $this->getConfig('dataField');

        $fieldValue = Hash::get($data, $dataField);
        if (empty($fieldValue)) {
            return null;
        }

        $conditions = [
            $this->getConfig('tokenField') => $fieldValue,
        ];

        return $this->getResolver()->find($conditions);
    }
}
