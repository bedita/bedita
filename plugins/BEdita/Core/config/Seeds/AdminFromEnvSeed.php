<?php
use Cake\Auth\WeakPasswordHasher;
use Migrations\AbstractSeed;

/**
 * Updates main admin user from env vars:
 *  - 'username' => BEDITA_ADMIN_USR
 *  - 'password' => BEDITA_ADMIN_PWD
 */
class AdminFromEnvSeed extends AbstractSeed
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $username = getenv('BEDITA_ADMIN_USR');
        $password = getenv('BEDITA_ADMIN_PWD');
        if (empty($username) || empty($password)) {
            echo "Mandatory environment variables missing: BEDITA_ADMIN_USR, BEDITA_ADMIN_PWD\n";
            echo 'No data seeded!';

            return -1;
        }

        $hash = (new WeakPasswordHasher(['hashType' => 'md5']))->hash($password);
        $query = sprintf("UPDATE users set username='%s', password_hash='%s' WHERE id=1", $username, $hash);
        $this->query($query);
    }
}
