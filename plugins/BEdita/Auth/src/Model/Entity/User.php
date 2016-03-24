<?php
namespace BEdita\Auth\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity.
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property bool $blocked
 * @property \Cake\I18n\Time $last_login
 * @property \Cake\I18n\Time $last_login_err
 * @property int $num_login_err
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \BEdita\Auth\Model\Entity\ExternalAuth[] $external_auth
 */
class User extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'username' => true,
        'password' => true,
        'external_auth' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'password',
    ];

    /**
     * Password setter.
     *
     * @param string $password Password to be hashed.
     * @return string
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher())->hash($password);
    }
}
