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
DROP TABLE IF EXISTS roles_users;
DROP TABLE IF EXISTS external_auth;
DROP TABLE IF EXISTS auth_providers;
DROP TABLE IF EXISTS annotations;
DROP TABLE IF EXISTS object_permissions;
DROP TABLE IF EXISTS endpoint_permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS object_properties;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS objects;
SET FOREIGN_KEY_CHECKS=1;

DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS endpoints;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS property_types;
DROP TABLE IF EXISTS object_types;


-- -------------
--   CONFIG
-- -------------

CREATE TABLE config (

  name VARCHAR(255) NOT NULL                  COMMENT 'configuration key',
  context VARCHAR(255) NOT NULL               COMMENT 'group name of configuration parameters',
  content TEXT NOT NULL                       COMMENT 'configuration data as string or JSON',
  created DATETIME NOT NULL                   COMMENT 'creation date',
  modified DATETIME NOT NULL                  COMMENT 'last modification date',

  PRIMARY KEY (name),
  INDEX config_context_idx (context)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'configuration parameters' ;


-- --------
--  ROLES
-- --------

CREATE TABLE roles (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(32) NOT NULL                 COMMENT 'role unique name',
  description TEXT DEFAULT NULL             COMMENT 'role description',
  unchangeable BOOL NOT NULL DEFAULT '0'    COMMENT 'role data not modifiable (default:false)',
  created datetime default NULL             COMMENT 'creation date',
  modified datetime default NULL            COMMENT 'last modification date',

  PRIMARY KEY (id),
  UNIQUE KEY roles_name_uq (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'roles definitions';


-- -------------
--   OBJECTS
-- -------------

CREATE TABLE object_types (

  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL                 COMMENT 'object type name',
  pluralized VARCHAR(50) NOT NULL           COMMENT 'pluralized object type name',
  description TEXT NULL DEFAULT NULL        COMMENT 'object type description',
  plugin VARCHAR(255) NOT NULL              COMMENT 'CakePHP plugin name',
  model VARCHAR(255) NOT NULL               COMMENT 'CakePHP Table class name',

  PRIMARY KEY (id),
  UNIQUE objecttypes_name_uq (name),
  UNIQUE objecttypes_plural_uq (pluralized),
  INDEX objecttypes_model_idx (plugin, model)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'object types definitions';


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
  options_list TEXT                           COMMENT 'property predefined options list (JSON)',
  created DATETIME NOT NULL                   COMMENT 'creation date',
  modified DATETIME NOT NULL                  COMMENT 'last modification date',
  description TEXT NULL                       COMMENT 'brief property description',
  enabled BOOL NOT NULL DEFAULT 1             COMMENT 'property active flag',
  label TEXT NULL                             COMMENT 'property default label',
  list_view BOOL NOT NULL DEFAULT 1           COMMENT 'property displayed in list view (backend operations)',

  PRIMARY KEY (id),
  UNIQUE properties_nametype_uq (name, object_type_id),
  INDEX properties_name_idx (name),
  INDEX properties_objtype_idx (object_type_id),
  INDEX properties_proptype_idx (property_type_id),

  CONSTRAINT properties_objtype_fk FOREIGN KEY (object_type_id) REFERENCES object_types(id),
  CONSTRAINT properties_proptype_fk FOREIGN KEY (property_type_id) REFERENCES property_types(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'object properties definitions' ;


CREATE TABLE objects (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  object_type_id SMALLINT UNSIGNED NOT NULL COMMENT 'object type id',
  status ENUM('on', 'off', 'draft') NOT NULL DEFAULT 'draft'  COMMENT 'object status: on, draft, off',
  uname VARCHAR(255) NOT NULL               COMMENT 'unique and url friendly resource name (slug)',
  locked BOOLEAN NOT NULL DEFAULT 0         COMMENT 'locked flag: some fields (status, uname,...) cannot be changed',
  deleted BOOLEAN NOT NULL DEFAULT 0        COMMENT 'deleted flag: if true object is in trashcan, default false',
  created DATETIME NOT NULL                 COMMENT 'creation date',
  modified DATETIME NOT NULL                COMMENT 'last modification date',
  published DATETIME NULL                   COMMENT 'publication date, status set to ON',
  title TEXT NULL                           COMMENT 'object title, can be emtpy',
  description MEDIUMTEXT NULL               COMMENT 'brief description, abstract',
  body MEDIUMTEXT NULL                      COMMENT 'long description, main object text',
  extra MEDIUMTEXT NULL                     COMMENT 'object data extensions (JSON format)',
  -- From MySQL 5.7.8 use JSON type
  lang CHAR(3) NULL DEFAULT NULL            COMMENT 'language used, ISO 639-3 code',
  created_by INT UNSIGNED NOT NULL          COMMENT 'user who created object',
  modified_by INT UNSIGNED NOT NULL         COMMENT 'last user to modify object',
  publish_start DATETIME NULL               COMMENT 'publish from this date on',
  publish_end DATETIME NULL                 COMMENT 'publish until this date',

  PRIMARY KEY (id),
  UNIQUE KEY objects_uname_uq (uname),
  INDEX objects_objtype_idx (object_type_id),
  INDEX objects_deleted_idx (deleted),

  CONSTRAINT objects_objtype_fk FOREIGN KEY (object_type_id)
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

  CONSTRAINT objectproperties_objectid_fk FOREIGN KEY (object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  CONSTRAINT objectproperties_propertyid_fk FOREIGN KEY (property_id)
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

  CONSTRAINT objectpermissions_objectid_fk FOREIGN KEY (object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  CONSTRAINT objectpermissions_roleid_fk FOREIGN KEY (role_id)
    REFERENCES roles(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'permissions on objects through roles and operations (RBAC)';


-- ---------------------------
--  ENDPOINTS / APPLICATIONS
-- ---------------------------

CREATE TABLE applications (

  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  api_key VARCHAR(255) NOT NULL             COMMENT 'api key value for application',
  name VARCHAR(255) NOT NULL                COMMENT 'application name',
  description TEXT NOT NULL                 COMMENT 'application description',
  created DATETIME NOT NULL                 COMMENT 'creation date',
  modified DATETIME NOT NULL                COMMENT 'last modification date',
  enabled BOOL NOT NULL DEFAULT 1           COMMENT 'application active flag',

  PRIMARY KEY (id),
  UNIQUE applications_apikey_uq (api_key),
  UNIQUE applications_name_uq (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'client API applications' ;


CREATE TABLE endpoints (

  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL                COMMENT 'endpoint name without slash, will be used as /name',
  description TEXT NULL                     COMMENT 'endpoint description',
  created DATETIME NOT NULL                 COMMENT 'creation date',
  modified DATETIME NOT NULL                COMMENT 'last modification date',
  enabled BOOL NOT NULL DEFAULT 1           COMMENT 'endpoint active flag',
  object_type_id SMALLINT UNSIGNED NULL     COMMENT 'link to object_types.id in case of an object type endpoint',

  PRIMARY KEY (id),
  UNIQUE endpoints_name_uq (name),
  CONSTRAINT endpoins_objecttypeid_fk FOREIGN KEY (object_type_id)
    REFERENCES object_types (id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'API available endpoints' ;


CREATE TABLE endpoint_permissions (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  endpoint_id SMALLINT UNSIGNED NOT NULL            COMMENT 'link to endpoints.id',
  application_id SMALLINT UNSIGNED NULL             COMMENT 'link to applications.id - may be null',
  role_id INT UNSIGNED NULL                         COMMENT 'link to roles.id - may be null',
  permission TINYINT UNSIGNED NOT NULL DEFAULT 0    COMMENT 'endpoint permission for role and app',

  PRIMARY KEY (id),
  UNIQUE applications_endapprole_uq (endpoint_id, application_id, role_id),

  CONSTRAINT endpointspermissions_endpointid_fk FOREIGN KEY (endpoint_id)
    REFERENCES endpoints (id),
  CONSTRAINT endpointspermissions_applicationid_fk FOREIGN KEY (application_id)
    REFERENCES applications (id),
  CONSTRAINT endpointspermissions_roleid_fk FOREIGN KEY (role_id)
    REFERENCES roles (id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'permissions on endpoints from applications' ;


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

  CONSTRAINT media_id_fk FOREIGN KEY (id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'media objects like images, audio, videos, files';


CREATE TABLE profiles (
  id INT UNSIGNED NOT NULL,
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

  CONSTRAINT profiles_id_fk FOREIGN KEY (id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'user profiles, addressbook data' ;


CREATE TABLE locations (
  id INT UNSIGNED NOT NULL,
  coords GEOMETRY NULL              COMMENT 'location geometry coordinates, like points or polygons with geo data' ,
  address TEXT NULL                 COMMENT 'generic address, street name and number or other format',
  locality TINYTEXT NULL            COMMENT 'city/town/village or generic settlement',
  postal_code CHAR(12) NULL         COMMENT 'postal code or ZIP code',
  country_name TINYTEXT NULL        COMMENT 'country name',
  region TINYTEXT NULL              COMMENT 'region, state or province inside a country',

  PRIMARY KEY (id),

  CONSTRAINT locations_id_fk FOREIGN KEY (id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'locations including geo data and address details' ;


-- ------------------
--   USERS & AUTH
-- ------------------

CREATE TABLE users (

  id INT UNSIGNED NOT NULL,
  username VARCHAR(100) NOT NULL              COMMENT 'login user name',
  password_hash TINYTEXT NULL                 COMMENT 'login password hash, if empty external auth is used',
  blocked BOOL NOT NULL DEFAULT 0             COMMENT 'user blocked flag',
  last_login DATETIME DEFAULT NULL            COMMENT 'last succcessful login datetime',
  last_login_err DATETIME DEFAULT NULL        COMMENT 'last login filaure datetime',
  num_login_err TINYINT NOT NULL DEFAULT 0    COMMENT 'number of consecutive login failures',

  PRIMARY KEY (id),
  UNIQUE KEY users_username_uq (username),

  CONSTRAINT users_id_fk FOREIGN KEY (id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

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
  CONSTRAINT externalauth_authprovider_fk FOREIGN KEY (auth_provider_id) REFERENCES auth_providers(id),
  CONSTRAINT externalauth_userid_fk FOREIGN KEY (user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'user external auth data' ;


CREATE TABLE roles_users (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  role_id INT UNSIGNED NOT NULL             COMMENT 'link to roles.id',
  user_id INT UNSIGNED NOT NULL             COMMENT 'link to users.id',

  PRIMARY KEY (id),
  INDEX rolesusers_userid_idx (user_id),
  INDEX rolesusers_roleid_idx (role_id),
  CONSTRAINT rolesusers_userid_fk FOREIGN KEY (user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  CONSTRAINT rolesusers_roleid_fk FOREIGN KEY (role_id)
    REFERENCES roles(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'join table for roles/users';


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
  CONSTRAINT annotations_userid_fk FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT annotations_objectid_fk FOREIGN KEY (object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'object annotations, comments, notes';

-- DROP TABLE IF EXISTS date_items;
-- CREATE TABLE date_items (
-- );

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

  CONSTRAINT relationtypes_relationid_fk FOREIGN KEY (relation_id)
    REFERENCES relations(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  CONSTRAINT relationtypes_objtypeid_fk FOREIGN KEY (object_type_id)
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

  CONSTRAINT objectrelations_leftid_fk FOREIGN KEY (left_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  CONSTRAINT objectrelations_rightid_fk FOREIGN KEY (right_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  CONSTRAINT objectrelations_relationid_fk FOREIGN KEY (relation_id)
    REFERENCES relations(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'relations between objects';


-- -------------
--   TREE
-- -------------

CREATE TABLE trees (

  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INT UNSIGNED NOT NULL       COMMENT 'object id',
  parent_id INT UNSIGNED NULL           COMMENT 'parent object id',
  root_id INT UNSIGNED NOT NULL         COMMENT 'root id (for tree scoping)',
  tree_left INT NOT NULL                COMMENT 'left counter (for nested set model)',
  tree_right INT NOT NULL               COMMENT 'right counter (for nested set model)',
  depth_level INT UNSIGNED NOT NULL     COMMENT 'tree depth level',
  menu INT UNSIGNED NOT NULL DEFAULT 1  COMMENT 'menu on/off',

  PRIMARY KEY (id),
  UNIQUE KEY trees_objectparent_uq (object_id, parent_id),
  INDEX trees_rootleft_idx (root_id, tree_left),
  INDEX trees_rootright_idx (root_id, tree_right),
  INDEX trees_menu_idx (menu),

  CONSTRAINT trees_objectid_fk FOREIGN KEY (object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  CONSTRAINT trees_parentid_fk FOREIGN KEY (parent_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  CONSTRAINT trees_rootid_fk FOREIGN KEY (root_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'tree structure';


-- ------------------
--  INITIAL DATA SET
-- ------------------

INSERT INTO `object_types` (`name`, `pluralized`, `description`, `plugin`, `model`)
    VALUES ('user', 'users', 'User object type', 'BEdita/Core', 'Users');
INSERT INTO `objects` (`object_type_id`, `status`, `uname`, `locked`, `created`, `modified`,
    `title`, `lang`, `created_by`, `modified_by`)
    VALUES (1, 'draft', 'bedita', 1, '2016-08-01 00:00:00', '2016-08-01 00:00:00', 'bedita', 'eng', 1, 1);
INSERT INTO `profiles` (`id`) VALUES (1);
INSERT INTO `users` (`id`, `username`, `password_hash`, `blocked`) VALUES (1, 'bedita', '42', 1);

ALTER TABLE  `objects` ADD CONSTRAINT objects_createdby_fk FOREIGN KEY (`created_by`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE  `objects` ADD CONSTRAINT objects_modifiedby_fk FOREIGN KEY (`modified_by`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
