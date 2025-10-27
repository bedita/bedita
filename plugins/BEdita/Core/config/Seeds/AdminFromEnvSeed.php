<?php
use Authentication\PasswordHasher\LegacyPasswordHasher;
use Migrations\BaseSeed;

/**
 * Updates main admin user from env vars:
 *  - 'username' => BEDITA_ADMIN_USR
 *  - 'password' => BEDITA_ADMIN_PWD
 */
class AdminFromEnvSeed extends BaseSeed
{
    /**
     * {@inheritDoc}
     */
    public function run(): void
    {
        $username = getenv('BEDITA_ADMIN_USR');
        $password = getenv('BEDITA_ADMIN_PWD');
        if (empty($username) || empty($password)) {
            $this->io->error('Mandatory environment variables missing: BEDITA_ADMIN_USR, BEDITA_ADMIN_PWD');
            $this->io->abort('No data seeded!');
        }

        $hash = (new LegacyPasswordHasher(['hashType' => 'md5']))->hash('password1');
        $query = sprintf("UPDATE users set username='%s', password_hash='%s' WHERE id=1", $username, $hash);
        $this->query($query);
    }
}
