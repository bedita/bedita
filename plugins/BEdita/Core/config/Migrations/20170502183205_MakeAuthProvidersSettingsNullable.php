<?php
use Migrations\AbstractMigration;

/**
 * Make `auth_providers.url` and `auth_providers.params` columns nullable.
 */
class MakeAuthProvidersSettingsNullable extends AbstractMigration
{

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('auth_providers')
            ->changeColumn('url', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
                'comment' => 'external provider url',
            ])
            ->changeColumn('params', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
                'comment' => 'external provider parameters',
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('auth_providers')
            ->changeColumn('url', 'string', [
                'comment' => 'external provider url',
                'default' => null,
                'length' => 255,
                'null' => false,
            ])
            ->changeColumn('params', 'text', [
                'comment' => 'external provider parameters',
                'default' => null,
                'length' => null,
                'null' => false,
            ])
            ->update();
    }
}

