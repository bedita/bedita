<?php
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
        $appName = getenv('BEDITA_APP_NAME');

        $row = [
            'name' => $appName ? $appName : 'manager',
            'api_key' => $apiKey,
            'created' => date("Y-m-d H:i:s"),
            'modified' => date("Y-m-d H:i:s"),
            'enabled' => 1,
        ];

        $table = $this->table('applications');
        $table->insert($row)->saveData();
    }
}
