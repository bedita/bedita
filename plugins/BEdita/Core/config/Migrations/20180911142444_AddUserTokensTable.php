<?php
use Migrations\AbstractMigration;

/**
 * Add `user_tokens` table
 */
class AddUserTokensTable extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public $autoId = false;

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $columnTypes = $this->getAdapter()->getColumnTypes();
        $enum = in_array('enum', $columnTypes) ? 'enum' : 'string';

        $this->table('user_tokens')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('user_id', 'integer', [
                'comment' => 'reference to system user',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('application_id', 'integer', [
                'comment' => 'link to applications.id - may be null',
                'default' => null,
                'limit' => 5,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('client_token', 'string', [
                'comment' => 'token sent to application',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('secret_token', 'string', [
                'comment' => 'secret token sent to user in a secure way',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('token_type', $enum, [
                'comment' => 'type of token: otp, refresh, recovery, 2fa, access',
                'default' => 'refresh',
                'limit' => 255,
                'values' => ['otp', 'refresh', 'recovery', '2fa', 'access'],
                'null' => false,
            ])
            ->addColumn('expires', 'timestamp', [
                'comment' => 'token expiry time',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'timestamp', [
                'comment' => 'creation date',
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('used', 'timestamp', [
                'comment' => 'token used time',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'usertokens_userid_idx',
                ]
            )
            ->addIndex(
                [
                    'application_id',
                ],
                [
                    'name' => 'usertokens_applicationid_idx',
                ]
            )
            ->addIndex(
                [
                    'client_token',
                ],
                [
                    'name' => 'usertokens_clienttoken_idx',
                ]
            )
            ->addIndex(
                [
                    'secret_token',
                ],
                [
                    'name' => 'usertokens_secrettoken_idx',
                ]
            )
            ->addForeignKey(
                'application_id',
                'applications',
                'id',
                [
                    'constraint' => 'usertokens_applicationid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE',
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'constraint' => 'usertokens_userid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE',
                ]
            )
            ->create();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('user_tokens')
            ->dropForeignKey('user_id')
            ->dropForeignKey('application_id')
            ->update();

        $this->table('user_tokens')
            ->drop();
    }
}
