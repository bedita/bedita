<?php
use Migrations\AbstractSeed;

/**
 * Core object types seed.
 */
class ObjectTypesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        $this->table('object_types')
            ->insert([
                [
                    'name' => 'document',
                    'pluralized' => 'documents',
                    'description' => 'Generic document',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
                [
                    'name' => 'news',
                    'pluralized' => 'news',
                    'description' => 'Generic piece of news',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
            ])
            ->save();
    }
}
