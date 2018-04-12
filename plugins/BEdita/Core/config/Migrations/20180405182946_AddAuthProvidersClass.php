<?php
use Migrations\AbstractMigration;

/**
 * Add `auth_class`, `enabled`, `creaeted` and `modified` columns to `auth_providers` table.
 *
 * @see https://github.com/bedita/bedita/1429
 */
class AddAuthProvidersClass extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('auth_providers')
            ->addColumn('auth_class', 'string', [
                'after' => 'name',
                'comment' => 'auth provider class',
                'default' => 'BEdita/API.OAuth2',
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('enabled', 'boolean', [
                'comment' => 'auth provider enabled flag',
                'default' => true,
                'length' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();

        $this->table('external_auth')
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('auth_providers')
            ->removeColumn('auth_class')
            ->removeColumn('enabled')
            ->removeColumn('created')
            ->removeColumn('modified')
            ->update();

        $this->table('external_auth')
            ->removeColumn('created')
            ->removeColumn('modified')
            ->update();
    }
}
