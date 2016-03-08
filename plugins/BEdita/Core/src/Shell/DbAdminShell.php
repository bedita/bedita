<?php
/**
 ${BEDITA_LICENSE_HEADER}
 */
namespace BEdita\Core\Shell;

use BEdita\Core\Utils\DbUtils;
use Cake\Console\Shell;
use Cake\Core\Plugin;
use Cake\Datasource\Exception\MissingDatasourceConfigException;

/**
 * Database related shell commands like:
 *  - initialize a new databasa instance
 *  - create schema files
 *  - check schema consistency
 */
class DbAdminShell extends Shell
{

    /**
     * Default JSON schema file name
     *
     * @var string
     */
    const JSON_SCHEMA_FILE = 'be4-schema.json';

    /**
     * Schema files folder path
     *
     * @var string
     */
    protected $schemaDir = null;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->schemaDir = Plugin::path('BEdita/Core') . 'config' . DS . 'schema' . DS;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('saveSchema', [
            'help' => 'Save current database schema to JSON file.',
            'parser' => [
                'description' => [
                    'Use this command to generate a JSON file schema.',
                    'File is built using current database connection.',
                ],
                'options' => [
                    'output' => [
                        'help' => 'Specifiy output file path',
                        'short' => 'o',
                        'required' => false,
                    ],
                ],
            ],
        ]);
        $parser->addSubcommand('checkSchema', [
            'help' => 'Check schema differences between current db and schema JSON file.',
            'parser' => [
                'description' => [
                    'A JSON file schema is generated from current DB connection.',
                    'This file is compared with the default one in BEdita/Core/config/schema/be4-schema.json.',
                ],
            ],
        ]);
        $parser->addSubcommand('init', [
            'help' => 'Create a new BE4 schema on current DB connection.',
            'parser' => [
                'description' => [
                    'A new database schema is created using current DB connection.',
                    'BEWARE: all existing BE4 tables will be dropped!!',
                ],
            ],
        ]);
        return $parser;
    }

    /**
     * Save schema to file (JSON format) from current db
     * Generated file is BEdita/Core/config/schema/be4-schema.json)
     *
     * @return void
     */
    public function saveSchema()
    {
        if (!empty($this->params['output'])) {
            $schemaFile = $this->params['output'];
        } else {
            $schemaFile = $this->schemaDir . self::JSON_SCHEMA_FILE;
        }
        if (file_exists($schemaFile)) {
            $res = $this->in('Overwrite schema file "' . $schemaFile . '"?', ['y', 'n'], 'n');
            if ($res != 'y') {
                $this->info('Schema file not updated');
                return;
            }
        }
        $schemaData = DbUtils::currentSchema();
        $jsonSchema = json_encode($schemaData, JSON_PRETTY_PRINT);
        $res = file_put_contents($schemaFile, $jsonSchema);
        if (!$res) {
            $this->abort('Error writing schema file ' . $schemaFile);
        }
        $this->info('Schema file updated ' . $schemaFile);
    }

    /**
     * Check schema differences between current db and schema JSON file
     * (in BEdita/Core/config/schema/be4-schema.json)
     *
     * @return void
     */
    public function checkSchema()
    {
        $schemaFile = $this->schemaDir . 'be4-schema.json';
        $json = file_get_contents($schemaFile);
        $be4Schema = json_decode($json, true);
        $currentSchema = DbUtils::currentSchema();
        $schemaDiff = DbUtils::schemaCompare($be4Schema, $currentSchema);
        if (empty($schemaDiff)) {
            $this->info('No schema differences found');
        } else {
            $this->out(print_r($schemaDiff, true));
            $this->error('Schema differences found');
        }
    }

    /**
     * Initialize BE4 database schema
     * SQL schema in BEdita/Core/config/schema/be4-schema-<vendor>.sql
     *
     * @return void
     */
    public function init()
    {
        $info = DbUtils::basicInfo();
        $this->warn('You are about to initialize a new database!!');
        $this->warn('ALL CURRENT BEDITA4 TABLES WILL BE DROPPED!!');
        $this->info('Host: ' . $info['host'] . '');
        $this->info('Database: ' . $info['database'] . '');
        $this->info('Vendor: ' . $info['vendor'] . '');
        $res = $this->in('Do you want to proceed?', ['y', 'n'], 'n');
        if ($res != 'y') {
            $this->out('Database unchanged');
            return;
        }
        $this->out('Creating new database schema...');
        $schemaFile = $this->schemaDir . 'be4-schema-' . $info['vendor'] . '.sql';
        if (!file_exists($schemaFile)) {
            $this->abort('Schema file not found: ' . $schemaFile);
        }
        $sqlSchema = file_get_contents($schemaFile);
        try {
            $result = DbUtils::executeTransaction($sqlSchema);
            if (!$result['success']) {
                $this->abort('Error creating database schema: ' . $result['error']);
            }
        } catch (MissingDatasourceConfigException $e) {
            $this->abort('Database connection not configured!');
        }
        $this->info('New database schema set');
    }
}
