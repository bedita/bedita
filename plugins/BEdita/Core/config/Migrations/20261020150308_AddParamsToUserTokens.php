<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddParamsToUserTokens extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/guides/writing-migrations/migration-methods.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $adapterType = $this->getAdapter()->getAdapterType();
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $json = in_array('json', $columnTypes) ? 'json' : 'text';
        if ($adapterType === 'pgsql' && in_array('jsonb', $columnTypes)) {
            $json = 'jsonb';
        }

        $this->table('user_tokens')
            ->addColumn('params', $json, [
                'after' => 'token_type',
                'comment' => 'token parameters (JSON)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }
}
