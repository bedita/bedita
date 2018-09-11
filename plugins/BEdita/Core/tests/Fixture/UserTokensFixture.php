<?php
namespace BEdita\Core\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UserTokensFixture
 *
 */
class UserTokensFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'reference to system user', 'precision' => null, 'autoIncrement' => null],
        'application_id' => ['type' => 'integer', 'length' => 5, 'unsigned' => true, 'null' => true, 'default' => null, 'comment' => 'link to applications.id - may be null', 'precision' => null, 'autoIncrement' => null],
        'client_token' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'token sent to application', 'precision' => null, 'fixed' => null],
        'secret_token' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => 'secret token sent to user in a secure way', 'precision' => null, 'fixed' => null],
        'token_type' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => 'refresh', 'collate' => 'latin1_swedish_ci', 'comment' => 'type of token: otp, refresh, recovery, 2fa, access', 'precision' => null, 'fixed' => null],
        'expires' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'token expiry time', 'precision' => null],
        'created' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => 'creation date', 'precision' => null],
        'used' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'token used time', 'precision' => null],
        '_indexes' => [
            'usertokens_userid_idx' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
            'usertokens_applicationid_idx' => ['type' => 'index', 'columns' => ['application_id'], 'length' => []],
            'clienttoken_applicationid_idx' => ['type' => 'index', 'columns' => ['client_token'], 'length' => []],
            'secrettoken_applicationid_idx' => ['type' => 'index', 'columns' => ['secret_token'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'usertokens_applicationid_fk' => ['type' => 'foreign', 'columns' => ['application_id'], 'references' => ['applications', 'id'], 'update' => 'noAction', 'delete' => 'cascade', 'length' => []],
            'usertokens_userid_fk' => ['type' => 'foreign', 'columns' => ['user_id'], 'references' => ['users', 'id'], 'update' => 'noAction', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'user_id' => 1,
                'application_id' => 1,
                'client_token' => 'Lorem ipsum dolor sit amet',
                'secret_token' => 'Lorem ipsum dolor sit amet',
                'token_type' => 'Lorem ipsum dolor sit amet',
                'expires' => 1536679290,
                'created' => 1536679290,
                'used' => 1536679290
            ],
        ];
        parent::init();
    }
}
