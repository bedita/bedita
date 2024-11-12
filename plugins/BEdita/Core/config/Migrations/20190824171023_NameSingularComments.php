<?php

use Migrations\AbstractMigration;

class NameSingularComments extends AbstractMigration
{
    /**
     * {@inheritDoc}
     *
     * @see https://github.com/cakephp/migrations/issues/741, https://github.com/cakephp/migrations/pull/745
     */
    public function useTransactions(): bool
    {
        return $this->getAdapter()->getAdapterType() === 'sqlite' ? false : parent::useTransactions();
    }

    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->table('object_types')
            ->changeColumn('name', 'string', [
                'comment' => 'object type name, plural form',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->changeColumn('singular', 'string', [
                'comment' => 'object type name, singular form',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->update();
    }
}
