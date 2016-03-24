<?php
namespace BEdita\Auth\Model\Entity;

use Cake\ORM\Entity;

/**
 * AuthProvider Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $params
 * @property \BEdita\Auth\Model\Entity\ExternalAuth[] $external_auth
 */
class AuthProvider extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
