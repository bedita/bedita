-- --------------------
-- DROP existing tables
-- --------------------

DROP TABLE IF EXISTS config;
DROP TABLE IF EXISTS trees;
DROP TABLE IF EXISTS object_relations;
DROP TABLE IF EXISTS relation_types;
DROP TABLE IF EXISTS relations;
DROP TABLE IF EXISTS profiles;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS annotations;
DROP TABLE IF EXISTS object_permissions;
DROP TABLE IF EXISTS object_properties;
DROP TABLE IF EXISTS objects;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS property_types;
DROP TABLE IF EXISTS object_types;
DROP TABLE IF EXISTS roles_users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS external_auth;
DROP TABLE IF EXISTS auth_providers;
DROP TABLE IF EXISTS users;

-- ------------------
--   USERS & AUTH
-- ------------------

CREATE TABLE users (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(100) NOT NULL              COMMENT 'login user name',
  password_hash TINYTEXT NULL                 COMMENT 'login password hash, if empty external auth is used',
  blocked BOOL NOT NULL DEFAULT 0             COMMENT 'user blocked flag',
  last_login DATETIME DEFAULT NULL            COMMENT 'last succcessful login datetime',
  last_login_err DATETIME DEFAULT NULL        COMMENT 'last login filaure datetime',
  num_login_err TINYINT NOT NULL DEFAULT 0    COMMENT 'number of consecutive login failures',
  created DATETIME NOT NULL                   COMMENT 'record creation date',
  -- from MySQL 5.6.5 created NOT NULL DATETIME DEFAULT CURRENT_TIMESTAMP,
  modified DATETIME NOT NULL                  COMMENT 'record last modification date',
  -- from MySQL 5.6.5 modified NOT NULL DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

  PRIMARY KEY (id),
  UNIQUE KEY users_username_uq (username)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'authenticated users basic data';


CREATE TABLE auth_providers (

  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL         COMMENT 'external provider name: facebook, google, github...',
  url VARCHAR(255) NOT NULL          COMMENT 'external provider url',
  params TINYTEXT NOT NULL           COMMENT 'external provider parameters',

  PRIMARY KEY (id),
  UNIQUE KEY authproviders_name_uq (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'supported external auth providers';


CREATE TABLE external_auth (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL               COMMENT 'reference to system user',
  auth_provider_id SMALLINT UNSIGNED NOT NULL COMMENT 'link to external auth provider',
  params TEXT DEFAULT NULL                    COMMENT 'external auth params, serialized JSON',
  -- From MySQL 5.7.8 JSON type
  provider_username VARCHAR(255)              COMMENT 'auth username on provider',

  PRIMARY KEY (id),
  UNIQUE KEY externalauth_authuser_uq (auth_provider_id, provider_username),
  FOREIGN KEY externalauth_authprovider_fk (auth_provider_id) REFERENCES auth_providers(id),
  FOREIGN KEY externalauth_userid_fk (user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'user external auth data' ;

-- --------
--  ROLES
-- --------

CREATE TABLE roles (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(32) NOT NULL                 COMMENT 'role unique name',
  description TEXT DEFAULT NULL             COMMENT 'role description',
  unchangeable BOOL NOT NULL DEFAULT '0'    COMMENT 'role data not modifiable (default:false)',
  backend_auth BOOL NOT NULL DEFAULT '0'    COMMENT 'role authorized to backend (default: false)',
  created datetime default NULL             COMMENT 'creation date',
  modified datetime default NULL            COMMENT 'last modification date',

  PRIMARY KEY (id),
  UNIQUE KEY roles_name_uq (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'roles definitions';


CREATE TABLE roles_users (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  role_id INT UNSIGNED NOT NULL             COMMENT 'link to roles.id',
  user_id INT UNSIGNED NOT NULL             COMMENT 'link to users.id',

  PRIMARY KEY (id),
  INDEX rolesusers_userid_idx (user_id),
  INDEX rolesusers_roleid_idx (role_id),
  FOREIGN KEY rolesusers_userid_fk (user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY rolesusers_roleid_fk (role_id)
    REFERENCES roles(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'join table for roles/users';


-- -------------
--   CONFIG
-- -------------

CREATE TABLE config (

  name VARCHAR(255) NOT NULL                  COMMENT 'configuration key',
  context VARCHAR(255) NOT NULL               COMMENT 'group name of configuration parameters',
  content TEXT NOT NULL                       COMMENT 'configuration data as string or JSON',
  created DATETIME NOT NULL                   COMMENT 'creation date',

  PRIMARY KEY (name),
  INDEX config_context_idx (context)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'configuration parameters' ;

-- -------------
--   OBJECTS
-- -------------

CREATE TABLE object_types (

  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL                 COMMENT 'object type name',
  description TEXT NULL                     COMMENT 'object type description',
  plugin VARCHAR(255) NOT NULL              COMMENT 'CakePHP plugin name',
  model VARCHAR(255) NOT NULL               COMMENT 'CakePHP Table class name',

  PRIMARY KEY (id),
  UNIQUE objecttypes_name_uq (name),
  INDEX objecttypes_model_idx (plugin, model)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'obect types definitions';


CREATE TABLE property_types (

  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL                   COMMENT 'property type name',
  params TEXT                                 COMMENT 'property type parameters',

  PRIMARY KEY (id),
  UNIQUE propertytypes_name_uq (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'property types definitions';


CREATE TABLE properties (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL                  COMMENT 'property name',
  object_type_id SMALLINT UNSIGNED NULL       COMMENT 'link to object_types.id',
  property_type_id SMALLINT UNSIGNED NOT NULL COMMENT 'link to property_type.id',
  multiple BOOL DEFAULT 0                     COMMENT 'multiple values for this property?',
  options_list TEXT                           COMMENT 'property predefined options list',

  PRIMARY KEY (id),
  UNIQUE properties_nametype_uq (name, object_type_id),
  INDEX properties_name_idx (name),
  INDEX properties_objtype_idx (object_type_id),
  INDEX properties_proptype_idx (property_type_id),

  FOREIGN KEY properties_objtype_fk (object_type_id) REFERENCES object_types(id),
  FOREIGN KEY properties_proptype_fk (property_type_id) REFERENCES property_types(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'object properties definitions' ;


CREATE TABLE objects (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  object_type_id SMALLINT UNSIGNED NOT NULL COMMENT 'object type id',
  status ENUM('on', 'off', 'draft', 'deleted') NOT NULL DEFAULT 'draft'  COMMENT 'object status: on, draft, off, deleted',
  uname VARCHAR(255) NOT NULL               COMMENT 'unique and url friendly resource name (slug)',
  locked BOOLEAN NOT NULL DEFAULT 0         COMMENT 'locked flag: some fields (status, uname,...) cannot be changed',
  created DATETIME NOT NULL                 COMMENT 'creation date',
  modified DATETIME NOT NULL                COMMENT 'last modification date',
  published DATETIME NULL                   COMMENT 'publication date, status set to ON',
  title TEXT NULL,
  description MEDIUMTEXT NULL,
  body MEDIUMTEXT NULL,
  extra MEDIUMTEXT NULL                     COMMENT 'object data extensions (JSON format)',
  -- From MySQL 5.7.8 use JSON type
  lang CHAR(3) NOT NULL                     COMMENT 'language used, ISO 639-3 code',
  created_by INT UNSIGNED NOT NULL          COMMENT 'user who created object',
  modified_by INT UNSIGNED NOT NULL         COMMENT 'last user to modify object',
  publish_start DATETIME NULL               COMMENT 'publish from this date on',
  publish_end DATETIME NULL                 COMMENT 'publish until this date',

  PRIMARY KEY (id),
  UNIQUE KEY objects_uname_uq (uname),
  INDEX objects_objtype_idx (object_type_id),

  FOREIGN KEY objects_objtype_fk (object_type_id)
    REFERENCES object_types(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'base table for all objects';


CREATE TABLE object_properties (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  property_id INT UNSIGNED NOT NULL           COMMENT 'link to properties.id',
  object_id INT UNSIGNED NOT NULL             COMMENT 'link to objects.id',
  property_value TEXT NOT NULL                COMMENT 'property value of linked object',

  PRIMARY KEY (id),

  FOREIGN KEY objectproperties_objectid_fk (object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY objectproperties_propertyid_fk (property_id)
    REFERENCES properties(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'object properties values' ;


-- --------------
--  PERMISSIONS
-- --------------

CREATE TABLE object_permissions (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INT UNSIGNED NOT NULL               COMMENT 'object - link to objects.id',
  role_id INT UNSIGNED NOT NULL                 COMMENT 'role - link to roles.id',
  params TEXT                                   COMMENT 'permission parameters (JSON data)',

  PRIMARY KEY (id),
  UNIQUE objectpermissions_objectrole_uq (object_id, role_id),

  FOREIGN KEY objectpermissions_objectid_fk (object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY objectpermissions_roleid_fk (role_id)
    REFERENCES roles(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'permissions on objects through roles and operations (RBAC)';


-- --------------------------------------
--  OBJECT METADATA / SPECIAL PROPERTIES
-- --------------------------------------

CREATE TABLE annotations (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INT UNSIGNED NOT NULL   COMMENT 'link to annotated object',
  description TEXT NULL             COMMENT 'annotation author',
  user_id INT UNSIGNED NOT NULL     COMMENT 'user creating this annotation',
  created DATETIME NOT NULL         COMMENT 'creation date',
  modified DATETIME NOT NULL        COMMENT 'last modification date',
  params MEDIUMTEXT                 COMMENT 'annotation parameters (serialized JSON)',

  PRIMARY KEY (id),
  FOREIGN KEY annotations_userid_fk (user_id) REFERENCES users(id),
  FOREIGN KEY annotations_objectid_fk (object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'object annotations, comments, notes';

-- DROP TABLE IF EXISTS date_items;
-- CREATE TABLE date_items (
-- );

-- --------------------
--  CORE OBJECT TYPES
-- --------------------

CREATE TABLE media (

  id INT UNSIGNED NOT NULL,
  uri TEXT NOT NULL                 COMMENT 'media uri: relative path on local filesystem or remote URL',
  name TEXT NULL                    COMMENT 'file name',
  mime_type TINYTEXT NOT NULL       COMMENT 'resource mime type',
  file_size INT(11) UNSIGNED NULL   COMMENT 'file size in bytes (if local)',
  hash_file VARCHAR(255) NULL       COMMENT 'md5 hash of local file',
  original_name TEXT NULL           COMMENT 'original name for uploaded file',
  width MEDIUMINT(6) UNSIGNED NULL  COMMENT '(image) width',
  height MEDIUMINT(6) UNSIGNED NULL COMMENT '(image) height',
  provider  TINYTEXT NULL           COMMENT 'external provider/service name',
  media_uid VARCHAR(255) NULL       COMMENT 'uid, used for remote videos',
  thumbnail TINYTEXT NULL           COMMENT 'remote media thumbnail URL',

  PRIMARY KEY (id),
  INDEX media_hashfile_idx (hash_file),

  FOREIGN KEY media_id_fk (id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'media objects like images, audio, videos, files';


CREATE TABLE profiles (
  id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL         COMMENT 'link to users.id, if not null',
  name TINYTEXT NULL                COMMENT 'person name, can be NULL',
  surname TINYTEXT NULL             COMMENT 'person surname, can be NULL',
  email VARCHAR(100) NULL           COMMENT 'first email, can be NULL',
  person_title TINYTEXT NULL        COMMENT 'person title, for example Sir, Madame, Prof, Doct, ecc., can be NULL',
  gender TINYTEXT NULL              COMMENT 'gender, for example male, female, can be NULL',
  birthdate DATE NULL               COMMENT 'date of birth, can be NULL',
  deathdate DATE NULL               COMMENT 'date of death, can be NULL',
  company BOOL NOT NULL DEFAULT '0' COMMENT 'is a company, default: false',
  company_name TINYTEXT NULL        COMMENT 'name of company, can be NULL',
  company_kind TINYTEXT NULL        COMMENT 'type of company, can be NULL',
  street_address TEXT NULL          COMMENT 'address street, can be NULL',
  city TINYTEXT NULL                COMMENT 'city, can be NULL',
  zipcode TINYTEXT NULL             COMMENT 'zipcode, can be NULL',
  country TINYTEXT NULL             COMMENT 'country, can be NULL',
  state_name TINYTEXT NULL          COMMENT 'state, can be NULL',
  phone TINYTEXT NULL               COMMENT 'first phone number, can be NULL',
  website TEXT NULL                 COMMENT 'website url, can be NULL',

  PRIMARY KEY (id),
  UNIQUE KEY profiles_email_uq (email),

  FOREIGN KEY profiles_userid_fk (user_id) REFERENCES users(id),

  FOREIGN KEY profiles_id_fk (id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'user profiles, addressbook data' ;

-- -------------
--   RELATIONS
-- -------------

CREATE TABLE relations (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL            COMMENT 'relation name',
  label TINYTEXT NOT NULL               COMMENT 'relation label',
  inverse_name VARCHAR(100) NOT NULL    COMMENT 'inverse relation name',
  inverse_label TINYTEXT NOT NULL       COMMENT 'inverse relation label',
  description TEXT                      COMMENT 'relation description',
  params MEDIUMTEXT NULL                COMMENT 'relation parameters definitions (JSON format)',
-- From MySQL 5.7.8 use JSON type

  PRIMARY KEY (id),
  UNIQUE KEY relations_name_uq (name),
  UNIQUE KEY relations_inversename_uq (inverse_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'object relations definitions';


CREATE TABLE relation_types (

  relation_id INT UNSIGNED NOT NULL             COMMENT 'link to relation definition',
  object_type_id SMALLINT UNSIGNED NOT NULL     COMMENT 'object type id',
  side ENUM ('left', 'right') NOT NULL          COMMENT 'type position in relation, left or right',

  PRIMARY KEY relationtypes_relobjside_pk (relation_id, object_type_id, side),

  FOREIGN KEY relationtypes_relationid_fk (relation_id)
    REFERENCES relations(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY relationtypes_objtypeid_fk (object_type_id)
    REFERENCES object_types(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'type constraints in object relations';


CREATE TABLE object_relations (

  left_id INT UNSIGNED NOT NULL         COMMENT 'left part of the relation object id',
  relation_id INT UNSIGNED NOT NULL     COMMENT 'link to relation definition',
  right_id INT UNSIGNED NOT NULL        COMMENT 'right part of the relation object id',
  priority INT UNSIGNED NOT NULL        COMMENT 'priority order in relation',
  inv_priority INT UNSIGNED NOT NULL    COMMENT 'priority order in inverse relation',
  params MEDIUMTEXT NULL                COMMENT 'relation parameters (JSON format)',
-- From MySQL 5.7.8 use JSON type

  PRIMARY KEY objectrelations_leftrelright_pk (left_id, relation_id, right_id),
  INDEX objectrelations_leftid_idx (left_id),
  INDEX objectrelations_rightid_idx (right_id),

  FOREIGN KEY objectrelations_leftid_fk (left_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY objectrelations_rightid_fk (right_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY objectrelations_relationid_fk (relation_id)
    REFERENCES relations(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'relations between objects';


-- -------------
--   TREE
-- -------------

CREATE TABLE trees (

  object_id INT UNSIGNED NOT NULL       COMMENT 'object id',
  parent_id INT UNSIGNED NULL           COMMENT 'parent object id',
  root_id INT UNSIGNED NOT NULL         COMMENT 'root id (for tree scoping)',
  tree_left INT NOT NULL                COMMENT 'left counter (for nested set model)',
  tree_right INT NOT NULL               COMMENT 'right counter (for nested set model)',
  depth_level INT UNSIGNED NOT NULL     COMMENT 'tree depth level',
  menu INT UNSIGNED NOT NULL DEFAULT 1  COMMENT 'menu on/off',

  PRIMARY KEY trees_parentobj_pk (parent_id, object_id),
  INDEX trees_objectparent_idx (object_id, parent_id),
  INDEX trees_rootleft_idx (root_id, tree_left),
  INDEX trees_rootright_idx (root_id, tree_right),
  INDEX trees_menu_idx (menu),

  FOREIGN KEY trees_objectid_fk (object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY trees_parentid_fk (parent_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY trees_rootid_fk (root_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'tree structure';
