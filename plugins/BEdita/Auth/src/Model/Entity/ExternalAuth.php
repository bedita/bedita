<?php
namespace BEdita\Auth\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExternalAuth Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \BEdita\Auth\Model\Entity\User $user
 * @property int $auth_provider_id
 * @property \BEdita\Auth\Model\Entity\AuthProvider $auth_provider
 * @property string $username
 * @property string $params
 */
class ExternalAuth extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
