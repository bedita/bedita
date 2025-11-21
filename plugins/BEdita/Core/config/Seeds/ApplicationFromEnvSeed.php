<?php

use Cake\Utility\Text;
use Migrations\BaseSeed;

/**
 * Create new application from env vars:
 *  - 'api_key' => BEDITA_API_KEY
 *  - 'name' => BEDITA_APP_NAME (optional)
 *
 * PLease note:
 *  - If an `api_key` with same value is found no action is taken.
 *  - If no BEDITA_APP_NAME env is set default `manager` value is used.
 *  - If an application `name` with same value (BEDITA_APP_NAME or `manager`)
 * is found an hash suffix is added.
 */
class ApplicationFromEnvSeed extends BaseSeed
{

    /**
     * {@inheritDoc}
     */
    public function run(): void
    {
        $apiKey = getenv('BEDITA_API_KEY');
        if (empty($apiKey)) {
            $this->io->error('Mandatory environment variable missing: BEDITA_API_KEY');
            $this->io->abort('No data seeded!');
        }

        $appName = getenv('BEDITA_APP_NAME') ? getenv('BEDITA_APP_NAME') : 'manager';
        $appRow = $this->fetchAll(sprintf("SELECT id FROM applications where api_key='%s'", $apiKey));
        if (!empty($appRow)) {
            return;
        }

        $row = [
            'name' => $appName,
            'api_key' => $apiKey,
            'created' => date("Y-m-d H:i:s"),
            'modified' => date("Y-m-d H:i:s"),
            'enabled' => 1,
        ];

        $appRow = $this->fetchAll(sprintf("SELECT id FROM applications where name='%s'", $appName));
        if (!empty($appRow)) {
            $hash = str_replace('-', '', Text::uuid());
            $row['name'] = $appName . '-' . substr($hash, 0, 6);
        }

        $table = $this->table('applications');
        $table->insert($row)->saveData();
    }
}
