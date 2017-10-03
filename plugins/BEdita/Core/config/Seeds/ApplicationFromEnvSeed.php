<?php

use Cake\Utility\Text;
use Migrations\AbstractSeed;

/**
 * Create new application from env vars:
 *  - 'api_key' => BEDITA_API_KEY
 *  - 'name' => BEDITA_APP_NAME (optional)
 */
class ApplicationFromEnvSeed extends AbstractSeed
{

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $apiKey = getenv('BEDITA_API_KEY');
        if (empty($apiKey)) {
            echo "Mandatory environment variable missing: BEDITA_API_KEY\n";
            echo 'No data seeded!';

            return -1;
        }
        $appName = getenv('BEDITA_APP_NAME') ? getenv('BEDITA_APP_NAME') : 'manager';

        $appRow = $this->fetchAll(sprintf("SELECT id FROM applications where api_key='%s'", $apiKey));
        if (!empty($appRow)) {
            return 0;
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
