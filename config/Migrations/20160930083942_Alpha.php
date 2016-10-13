<?php
use Cake\Auth\WeakPasswordHasher;
use Migrations\AbstractMigration;

/**
 * Database schema for Alpha release.
 */
class Alpha extends AbstractMigration
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

        $this->table('annotations')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_id', 'integer', [
                'comment' => 'link to annotated object',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('description', 'text', [
                'comment' => 'annotation author',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'comment' => 'user creating this annotation',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'annotation parameters (serialized JSON)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'annotations_objectid_idx',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'annotations_userid_idx',
                ]
            )
            ->create();

        $this->table('applications')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('api_key', 'string', [
                'comment' => 'api key value for application',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'comment' => 'application name',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'comment' => 'application description',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('enabled', 'boolean', [
                'comment' => 'application active flag',
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'api_key',
                ],
                [
                    'name' => 'applications_apikey_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'applications_name_uq',
                    'unique' => true,
                ]
            )
            ->create();

        $this->table('auth_providers')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'external provider name: facebook, google, github...',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('url', 'string', [
                'comment' => 'external provider url',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'external provider parameters',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'authproviders_name_uq',
                    'unique' => true,
                ]
            )
            ->create();

        $this->table('config')
            ->addColumn('name', 'string', [
                'comment' => 'configuration key',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addPrimaryKey(['name'])
            ->addColumn('context', 'string', [
                'comment' => 'group name of configuration parameters',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('content', 'text', [
                'comment' => 'configuration data as string or JSON',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'context',
                ],
                [
                    'name' => 'config_context_idx',
                ]
            )
            ->create();

        $this->table('endpoint_permissions')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('endpoint_id', 'integer', [
                'comment' => 'link to endpoints.id',
                'default' => null,
                'limit' => 5,
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
            ->addColumn('role_id', 'integer', [
                'comment' => 'link to roles.id - may be null',
                'default' => null,
                'limit' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('permission', 'integer', [
                'comment' => 'endpoint permission for role and app',
                'default' => 0,
                'limit' => 3,
                'null' => false,
                'signed' => false,
            ])
            ->addIndex(
                [
                    'endpoint_id',
                    'application_id',
                    'role_id',
                ],
                [
                    'name' => 'applications_endapprole_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'application_id',
                ],
                [
                    'name' => 'endpointspermissions_applicationid_idx',
                ]
            )
            ->addIndex(
                [
                    'endpoint_id',
                ],
                [
                    'name' => 'endpointspermissions_endpointid_idx',
                ]
            )
            ->addIndex(
                [
                    'role_id',
                ],
                [
                    'name' => 'endpointspermissions_roleid_idx',
                ]
            )
            ->create();

        $this->table('endpoints')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'endpoint name without slash, will be used as /name',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'comment' => 'endpoint description',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('enabled', 'boolean', [
                'comment' => 'endpoint active flag',
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('object_type_id', 'integer', [
                'comment' => 'link to object_types.id in case of an object type endpoint',
                'default' => null,
                'limit' => 5,
                'null' => true,
                'signed' => false,
            ])
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'endpoints_name_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'object_type_id',
                ],
                [
                    'name' => 'endpoins_objecttypeid_idx',
                ]
            )
            ->create();

        $this->table('external_auth')
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
            ->addColumn('auth_provider_id', 'integer', [
                'comment' => 'link to external auth provider',
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'external auth params, serialized JSON',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('provider_username', 'string', [
                'comment' => 'auth username on provider',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addIndex(
                [
                    'auth_provider_id',
                    'provider_username',
                ],
                [
                    'name' => 'externalauth_authuser_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'auth_provider_id',
                ],
                [
                    'name' => 'externalauth_authprovider_idx',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'externalauth_userid_idx',
                ]
            )
            ->create();

        $this->table('media')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('uri', 'text', [
                'comment' => 'media uri: relative path on local filesystem or remote URL',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'text', [
                'comment' => 'file name',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('mime_type', 'string', [
                'comment' => 'resource mime type',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('file_size', 'integer', [
                'comment' => 'file size in bytes (if local)',
                'default' => null,
                'limit' => 11,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('hash_file', 'string', [
                'comment' => 'md5 hash of local file',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('original_name', 'text', [
                'comment' => 'original name for uploaded file',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('width', 'integer', [
                'comment' => '(image) width',
                'default' => null,
                'limit' => 6,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('height', 'integer', [
                'comment' => '(image) height',
                'default' => null,
                'limit' => 6,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('provider', 'string', [
                'comment' => 'external provider/service name',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('media_uid', 'string', [
                'comment' => 'uid, used for remote videos',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('thumbnail', 'string', [
                'comment' => 'remote media thumbnail URL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addIndex(
                [
                    'hash_file',
                ],
                [
                    'name' => 'media_hashfile_idx',
                ]
            )
            ->create();

        $this->table('object_permissions')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_id', 'integer', [
                'comment' => 'object - link to objects.id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('role_id', 'integer', [
                'comment' => 'role - link to roles.id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'permission parameters (JSON data)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'object_id',
                    'role_id',
                ],
                [
                    'name' => 'objectpermissions_objectrole_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'objectpermissions_objectid_idx',
                ]
            )
            ->addIndex(
                [
                    'role_id',
                ],
                [
                    'name' => 'objectpermissions_roleid_idx',
                ]
            )
            ->create();

        $this->table('object_properties')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('property_id', 'integer', [
                'comment' => 'link to properties.id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('object_id', 'integer', [
                'comment' => 'link to objects.id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('property_value', 'text', [
                'comment' => 'property value of linked object',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'objectproperties_objectid_idx',
                ]
            )
            ->addIndex(
                [
                    'property_id',
                ],
                [
                    'name' => 'objectproperties_propertyid_idx',
                ]
            )
            ->create();

        $this->table('object_relations')
            ->addColumn('left_id', 'integer', [
                'comment' => 'left part of the relation object id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('relation_id', 'integer', [
                'comment' => 'link to relation definition',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('right_id', 'integer', [
                'comment' => 'right part of the relation object id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['left_id', 'relation_id', 'right_id'])
            ->addColumn('priority', 'integer', [
                'comment' => 'priority order in relation',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('inv_priority', 'integer', [
                'comment' => 'priority order in inverse relation',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'relation parameters (JSON format)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'left_id',
                ],
                [
                    'name' => 'objectrelations_leftid_idx',
                ]
            )
            ->addIndex(
                [
                    'relation_id',
                ],
                [
                    'name' => 'objectrelations_relationid_idx',
                ]
            )
            ->addIndex(
                [
                    'right_id',
                ],
                [
                    'name' => 'objectrelations_rightid_idx',
                ]
            )
            ->create();

        $this->table('object_types')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'object type name',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('pluralized', 'string', [
                'comment' => 'pluralized object type name',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'comment' => 'object type description',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('plugin', 'string', [
                'comment' => 'CakePHP plugin name',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('model', 'string', [
                'comment' => 'CakePHP Table class name',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'objecttypes_name_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'pluralized',
                ],
                [
                    'name' => 'objecttypes_plural_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'plugin',
                    'model',
                ],
                [
                    'name' => 'objecttypes_model_idx',
                ]
            )
            ->create();

        $this->table('objects')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_type_id', 'integer', [
                'comment' => 'object type id',
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('status', $enum, [
                'comment' => 'object status: on, draft, off, deleted',
                'default' => 'draft',
                'limit' => 255,
                'values' => ['on', 'off', 'draft'],
                'null' => false,
            ])
            ->addColumn('uname', 'string', [
                'comment' => 'unique and url friendly resource name (slug)',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('locked', 'boolean', [
                'comment' => 'locked flag: some fields (status, uname,...) cannot be changed',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'comment' => 'creation date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'comment' => 'last modification date',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('published', 'datetime', [
                'comment' => 'publication date, status set to ON',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('title', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('body', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('extra', 'text', [
                'comment' => 'object data extensions (JSON format)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('lang', 'char', [
                'comment' => 'language used, ISO 639-3 code',
                'default' => null,
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('created_by', 'integer', [
                'comment' => 'user who created object',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('modified_by', 'integer', [
                'comment' => 'last user to modify object',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('publish_start', 'datetime', [
                'comment' => 'publish from this date on',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('publish_end', 'datetime', [
                'comment' => 'publish until this date',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'uname',
                ],
                [
                    'name' => 'objects_uname_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'created_by',
                ],
                [
                    'name' => 'objects_createdby_idx',
                ]
            )
            ->addIndex(
                [
                    'modified_by',
                ],
                [
                    'name' => 'objects_modifiedby_idx',
                ]
            )
            ->addIndex(
                [
                    'object_type_id',
                ],
                [
                    'name' => 'objects_objtype_idx',
                ]
            )
            ->create();

        $this->table('profiles')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'person name, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('surname', 'string', [
                'comment' => 'person surname, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'comment' => 'first email, can be NULL',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('person_title', 'string', [
                'comment' => 'person title, for example Sir, Madame, Prof, Doct, ecc., can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('gender', 'string', [
                'comment' => 'gender, for example male, female, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('birthdate', 'date', [
                'comment' => 'date of birth, can be NULL',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('deathdate', 'date', [
                'comment' => 'date of death, can be NULL',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('company', 'boolean', [
                'comment' => 'is a company, default: false',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('company_name', 'string', [
                'comment' => 'name of company, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('company_kind', 'string', [
                'comment' => 'type of company, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('street_address', 'text', [
                'comment' => 'address street, can be NULL',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('city', 'string', [
                'comment' => 'city, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('zipcode', 'string', [
                'comment' => 'zipcode, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('country', 'string', [
                'comment' => 'country, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('state_name', 'string', [
                'comment' => 'state, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('phone', 'string', [
                'comment' => 'first phone number, can be NULL',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('website', 'text', [
                'comment' => 'website url, can be NULL',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'email',
                ],
                [
                    'name' => 'profiles_email_uq',
                    'unique' => true,
                ]
            )
            ->create();

        $this->table('properties')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'property name',
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('object_type_id', 'integer', [
                'comment' => 'link to object_types.id',
                'default' => null,
                'limit' => 5,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('property_type_id', 'integer', [
                'comment' => 'link to property_type.id',
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('multiple', 'boolean', [
                'comment' => 'multiple values for this property?',
                'default' => false,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('options_list', 'text', [
                'comment' => 'property predefined options list',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'name',
                    'object_type_id',
                ],
                [
                    'name' => 'properties_nametype_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'object_type_id',
                ],
                [
                    'name' => 'properties_objtype_idx',
                ]
            )
            ->addIndex(
                [
                    'property_type_id',
                ],
                [
                    'name' => 'properties_proptype_idx',
                ]
            )
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'properties_name_idx',
                ]
            )
            ->create();

        $this->table('property_types')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'property type name',
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'property type parameters',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'propertytypes_name_uq',
                    'unique' => true,
                ]
            )
            ->create();

        $this->table('relation_types')
            ->addColumn('relation_id', 'integer', [
                'comment' => 'link to relation definition',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('object_type_id', 'integer', [
                'comment' => 'object type id',
                'default' => null,
                'limit' => 5,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('side', $enum, [
                'comment' => 'type position in relation, left or right',
                'default' => null,
                'limit' => 255,
                'values' => ['left', 'right'],
                'null' => false,
            ])
            ->addPrimaryKey(['relation_id', 'object_type_id', 'side'])
            ->addIndex(
                [
                    'object_type_id',
                ],
                [
                    'name' => 'relationtypes_objtypeid_idx',
                ]
            )
            ->addIndex(
                [
                    'relation_id',
                ],
                [
                    'name' => 'relationtypes_relationid_idx',
                ]
            )
            ->create();

        $this->table('relations')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'relation name',
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('label', 'string', [
                'comment' => 'relation label',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('inverse_name', 'string', [
                'comment' => 'inverse relation name',
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('inverse_label', 'string', [
                'comment' => 'inverse relation label',
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'comment' => 'relation description',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('params', 'text', [
                'comment' => 'relation parameters definitions (JSON format)',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'relations_name_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'inverse_name',
                ],
                [
                    'name' => 'relations_inversename_uq',
                    'unique' => true,
                ]
            )
            ->create();

        $this->table('roles')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'comment' => 'role unique name',
                'default' => null,
                'limit' => 32,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'comment' => 'role description',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('unchangeable', 'boolean', [
                'comment' => 'role data not modifiable (default:false)',
                'default' => false,
                'limit' => null,
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
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'roles_name_uq',
                    'unique' => true,
                ]
            )
            ->create();

        $this->table('roles_users')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('role_id', 'integer', [
                'comment' => 'link to roles.id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'comment' => 'link to users.id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addIndex(
                [
                    'role_id',
                ],
                [
                    'name' => 'rolesusers_roleid_idx',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ],
                [
                    'name' => 'rolesusers_userid_idx',
                ]
            )
            ->create();

        $this->table('trees')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('object_id', 'integer', [
                'comment' => 'object id',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('parent_id', 'integer', [
                'comment' => 'parent object id',
                'default' => null,
                'limit' => 10,
                'null' => true,
                'signed' => false,
            ])
            ->addColumn('root_id', 'integer', [
                'comment' => 'root id (for tree scoping)',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('tree_left', 'integer', [
                'comment' => 'left counter (for nested set model)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('tree_right', 'integer', [
                'comment' => 'right counter (for nested set model)',
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('depth_level', 'integer', [
                'comment' => 'tree depth level',
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('menu', 'integer', [
                'comment' => 'menu on/off',
                'default' => 1,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addIndex(
                [
                    'object_id',
                    'parent_id',
                ],
                [
                    'name' => 'trees_objectparent_uq',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'object_id',
                ],
                [
                    'name' => 'trees_objectid_idx',
                ]
            )
            ->addIndex(
                [
                    'parent_id',
                ],
                [
                    'name' => 'trees_parentid_idx',
                ]
            )
            ->addIndex(
                [
                    'root_id',
                ],
                [
                    'name' => 'trees_rootid_idx',
                ]
            )
            ->addIndex(
                [
                    'root_id',
                    'tree_left',
                ],
                [
                    'name' => 'trees_rootleft_idx',
                ]
            )
            ->addIndex(
                [
                    'root_id',
                    'tree_right',
                ],
                [
                    'name' => 'trees_rootright_idx',
                ]
            )
            ->addIndex(
                [
                    'menu',
                ],
                [
                    'name' => 'trees_menu_idx',
                ]
            )
            ->create();

        $this->table('users')
            ->addColumn('id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('username', 'string', [
                'comment' => 'login user name',
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('password_hash', 'string', [
                'comment' => 'login password hash, if empty external auth is used',
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('blocked', 'boolean', [
                'comment' => 'user blocked flag',
                'default' => false,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('last_login', 'datetime', [
                'comment' => 'last succcessful login datetime',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('last_login_err', 'datetime', [
                'comment' => 'last login filaure datetime',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('num_login_err', 'integer', [
                'comment' => 'number of consecutive login failures',
                'default' => 0,
                'limit' => 4,
                'null' => false,
            ])
            ->addIndex(
                [
                    'username',
                ],
                [
                    'name' => 'users_username_uq',
                    'unique' => true,
                ]
            )
            ->create();

        $this->table('object_types')
            ->insert([
                [
                    'id' => 1,
                    'name' => 'object',
                    'pluralized' => 'objects',
                    'description' => 'Base BEdita object type, to be extended by concrete implementations',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                ],
                [
                    'id' => 2,
                    'name' => 'profile',
                    'pluralized' => 'profiles',
                    'description' => 'Generic person profile',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Profiles',
                ],
                [
                    'id' => 3,
                    'name' => 'user',
                    'pluralized' => 'users',
                    'description' => 'BEdita user profile',
                    'plugin' => 'BEdita/Core',
                    'model' => 'Users',
                ],
            ])
            ->save();

        $this->table('objects')
            ->insert([
                'id' => 1,
                'object_type_id' => 3,
                'status' => 'on',
                'uname' => 'bedita',
                'locked' => 1,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
                'lang' => 'eng',
                'created_by' => 1,
                'modified_by' => 1,
            ])
            ->save();

        $this->table('profiles')
            ->insert([
                'id' => 1,
            ])
            ->save();

        $this->table('users')
            ->insert([
                'id' => 1,
                'username' => 'bedita',
                'password_hash' => (new WeakPasswordHasher(['hashType' => 'md5']))->hash('password1'),
            ])
            ->save();

        $this->table('roles')
            ->insert([
                'id' => 1,
                'name' => 'admin',
                'description' => 'Administrators\' role',
                'unchangeable' => 1,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ])
            ->save();

        $this->table('roles_users')
            ->insert([
                'role_id' => 1,
                'user_id' => 1,
            ])
            ->save();

        $this->table('annotations')
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'annotations_objectid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'constraint' => 'annotations_userid_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('endpoint_permissions')
            ->addForeignKey(
                'application_id',
                'applications',
                'id',
                [
                    'constraint' => 'endpointspermissions_applicationid_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'endpoint_id',
                'endpoints',
                'id',
                [
                    'constraint' => 'endpointspermissions_endpointid_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'role_id',
                'roles',
                'id',
                [
                    'constraint' => 'endpointspermissions_roleid_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('endpoints')
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'endpoins_objecttypeid_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('external_auth')
            ->addForeignKey(
                'auth_provider_id',
                'auth_providers',
                'id',
                [
                    'constraint' => 'externalauth_authprovider_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'constraint' => 'externalauth_userid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('media')
            ->addForeignKey(
                'id',
                'objects',
                'id',
                [
                    'constraint' => 'media_id_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('object_permissions')
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'objectpermissions_objectid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'role_id',
                'roles',
                'id',
                [
                    'constraint' => 'objectpermissions_roleid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('object_properties')
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'objectproperties_objectid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'property_id',
                'properties',
                'id',
                [
                    'constraint' => 'objectproperties_propertyid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('object_relations')
            ->addForeignKey(
                'left_id',
                'objects',
                'id',
                [
                    'constraint' => 'objectrelations_leftid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'relation_id',
                'relations',
                'id',
                [
                    'constraint' => 'objectrelations_relationid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'right_id',
                'objects',
                'id',
                [
                    'constraint' => 'objectrelations_rightid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('objects')
            ->addForeignKey(
                'created_by',
                'users',
                'id',
                [
                    'constraint' => 'objects_createdby_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'modified_by',
                'users',
                'id',
                [
                    'constraint' => 'objects_modifiedby_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'objects_objtype_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('profiles')
            ->addForeignKey(
                'id',
                'objects',
                'id',
                [
                    'constraint' => 'profiles_id_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('properties')
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'properties_objtype_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'property_type_id',
                'property_types',
                'id',
                [
                    'constraint' => 'properties_proptype_fk',
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('relation_types')
            ->addForeignKey(
                'object_type_id',
                'object_types',
                'id',
                [
                    'constraint' => 'relationtypes_objtypeid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'relation_id',
                'relations',
                'id',
                [
                    'constraint' => 'relationtypes_relationid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('roles_users')
            ->addForeignKey(
                'role_id',
                'roles',
                'id',
                [
                    'constraint' => 'rolesusers_roleid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'constraint' => 'rolesusers_userid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('trees')
            ->addForeignKey(
                'object_id',
                'objects',
                'id',
                [
                    'constraint' => 'trees_objectid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'parent_id',
                'objects',
                'id',
                [
                    'constraint' => 'trees_parentid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->addForeignKey(
                'root_id',
                'objects',
                'id',
                [
                    'constraint' => 'trees_rootid_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();

        $this->table('users')
            ->addForeignKey(
                'id',
                'objects',
                'id',
                [
                    'constraint' => 'users_id_fk',
                    'update' => 'NO_ACTION',
                    'delete' => 'CASCADE'
                ]
            )
            ->update();
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->table('annotations')
            ->dropForeignKey(
                'object_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->table('endpoint_permissions')
            ->dropForeignKey(
                'application_id'
            )
            ->dropForeignKey(
                'endpoint_id'
            )
            ->dropForeignKey(
                'role_id'
            );

        $this->table('endpoints')
            ->dropForeignKey(
                'object_type_id'
            );

        $this->table('external_auth')
            ->dropForeignKey(
                'auth_provider_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->table('media')
            ->dropForeignKey(
                'id'
            );

        $this->table('object_permissions')
            ->dropForeignKey(
                'object_id'
            )
            ->dropForeignKey(
                'role_id'
            );

        $this->table('object_properties')
            ->dropForeignKey(
                'object_id'
            )
            ->dropForeignKey(
                'property_id'
            );

        $this->table('object_relations')
            ->dropForeignKey(
                'left_id'
            )
            ->dropForeignKey(
                'relation_id'
            )
            ->dropForeignKey(
                'right_id'
            );

        $this->table('objects')
            ->dropForeignKey(
                'created_by'
            )
            ->dropForeignKey(
                'modified_by'
            )
            ->dropForeignKey(
                'object_type_id'
            );

        $this->table('profiles')
            ->dropForeignKey(
                'id'
            );

        $this->table('properties')
            ->dropForeignKey(
                'object_type_id'
            )
            ->dropForeignKey(
                'property_type_id'
            );

        $this->table('relation_types')
            ->dropForeignKey(
                'object_type_id'
            )
            ->dropForeignKey(
                'relation_id'
            );

        $this->table('roles_users')
            ->dropForeignKey(
                'role_id'
            )
            ->dropForeignKey(
                'user_id'
            );

        $this->table('trees')
            ->dropForeignKey(
                'object_id'
            )
            ->dropForeignKey(
                'parent_id'
            )
            ->dropForeignKey(
                'root_id'
            );

        $this->table('users')
            ->dropForeignKey(
                'id'
            );

        $this->dropTable('annotations');
        $this->dropTable('applications');
        $this->dropTable('auth_providers');
        $this->dropTable('config');
        $this->dropTable('endpoint_permissions');
        $this->dropTable('endpoints');
        $this->dropTable('external_auth');
        $this->dropTable('media');
        $this->dropTable('object_permissions');
        $this->dropTable('object_properties');
        $this->dropTable('object_relations');
        $this->dropTable('object_types');
        $this->dropTable('objects');
        $this->dropTable('profiles');
        $this->dropTable('properties');
        $this->dropTable('property_types');
        $this->dropTable('relation_types');
        $this->dropTable('relations');
        $this->dropTable('roles');
        $this->dropTable('roles_users');
        $this->dropTable('trees');
        $this->dropTable('users');
    }
}
