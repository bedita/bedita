<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Policy;

use Authorization\Policy\Exception\MissingPolicyException;
use Authorization\Policy\ResolverInterface;
use BEdita\Core\Model\Entity\ObjectEntity;

/**
 * Resolver for BEdita objects policy.
 *
 * @since 5.10.0
 */
class ObjectResolver implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function getPolicy($resource)
    {
        if ($resource instanceof ObjectEntity) {
            return new ObjectPolicy();
        }

        throw new MissingPolicyException($resource);
    }
}
