<?php
use Migrations\BaseMigration;

/**
 * Remove unique `profiles.email` index and add non-unique index
 */
class RemoveUniqueEmail extends BaseMigration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->table('profiles')
            ->removeIndexByName('profiles_email_uq')
            ->addIndex(
                [
                    'email',
                ],
                [
                    'name' => 'profiles_email_idx',
                ]
            )
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('profiles')
            ->removeIndexByName('profiles_email_idx')
            ->addIndex(
                [
                    'email',
                ],
                [
                    'name' => 'profiles_email_uq',
                    'unique' => true,
                ]
            )
            ->update();
    }
}
