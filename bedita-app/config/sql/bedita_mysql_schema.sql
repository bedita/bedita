-- 
-- bedita database main schema
-- 
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `aliases`;
DROP TABLE IF EXISTS `annotations`;
DROP TABLE IF EXISTS `applications`;
DROP TABLE IF EXISTS `areas`;
DROP TABLE IF EXISTS `banned_ips`;
DROP TABLE IF EXISTS `cake_sessions`;
DROP TABLE IF EXISTS `cards`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `contents`;
DROP TABLE IF EXISTS `date_items`;
DROP TABLE IF EXISTS `event_logs`;
DROP TABLE IF EXISTS `geo_tags`;
DROP TABLE IF EXISTS `groups`;
DROP TABLE IF EXISTS `groups_users`;
DROP TABLE IF EXISTS `hash_jobs`;
DROP TABLE IF EXISTS `history`;
DROP TABLE IF EXISTS `images`;
DROP TABLE IF EXISTS `lang_texts`;
DROP TABLE IF EXISTS `links`;
DROP TABLE IF EXISTS `mail_groups`;
DROP TABLE IF EXISTS `mail_group_cards`;
DROP TABLE IF EXISTS `mail_group_messages`;
DROP TABLE IF EXISTS `mail_jobs`;
DROP TABLE IF EXISTS `mail_logs`;
DROP TABLE IF EXISTS `mail_messages`;
DROP TABLE IF EXISTS `modules`;
DROP TABLE IF EXISTS `objects`;
DROP TABLE IF EXISTS `object_categories`;
DROP TABLE IF EXISTS `object_editors`;
DROP TABLE IF EXISTS `object_properties`;
DROP TABLE IF EXISTS `object_relations`;
DROP TABLE IF EXISTS `object_types`;
DROP TABLE IF EXISTS `object_users`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `permission_modules`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `properties`;
DROP TABLE IF EXISTS `property_options`;
DROP TABLE IF EXISTS `search_texts`;
DROP TABLE IF EXISTS `sections`;
DROP TABLE IF EXISTS `section_types`;
DROP TABLE IF EXISTS `streams`;
DROP TABLE IF EXISTS `trees`;
DROP TABLE IF EXISTS `user_properties`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `versions`;
DROP TABLE IF EXISTS `videos`;

CREATE TABLE `aliases` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `object_id` INTEGER UNSIGNED NOT NULL,
  `nickname_alias` VARCHAR( 255 ) NOT NULL COMMENT 'alternative nickname',
  `lang` CHAR( 3 ) NULL COMMENT 'alias preferred language, can be NULL',
  PRIMARY KEY (`id` ) ,
  UNIQUE (`nickname_alias`) ,
  INDEX (`object_id`),
  FOREIGN KEY(object_id)
  REFERENCES objects(id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE = InnoDB CHARACTER SET utf8 COMMENT = 'Object nickname aliases (mainly frontend URLs)';

CREATE TABLE annotations (
  id INTEGER UNSIGNED NOT NULL,
  object_id INTEGER UNSIGNED NOT NULL,
  author VARCHAR(255) NULL COMMENT 'annotation author',
  email VARCHAR(255) NULL COMMENT 'annotation author email',
  url VARCHAR(255) NULL COMMENT 'annotation url, can be NULL',
  thread_path MEDIUMTEXT NULL COMMENT 'path to thread, can be NULL',
  rating INTEGER UNSIGNED NULL COMMENT 'object rating, can be NULL',
  PRIMARY KEY(id),
  INDEX `author_idx`(author),
  INDEX `objects_idx` (`object_id`),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'object annotations, comments, notes';

CREATE TABLE applications (
  id INTEGER UNSIGNED NOT NULL,
  application_name VARCHAR(255) NOT NULL COMMENT 'name of application, for example flash',
  application_label VARCHAR(255) NULL COMMENT 'label for application, for example Adobe Flash, can be NULL',
  application_version VARCHAR(50) NULL COMMENT 'version of application, can be NULL',
  application_type VARCHAR (255) NOT NULL COMMENT 'type of application, for example application/x-shockwave-flash',
  text_dir VARCHAR( 10 ) DEFAULT 'ltr' COMMENT 'text orientation (ltr:left to right;rtl: right to left)',
  text_lang VARCHAR (255) NULL COMMENT 'text language, can be NULL',
  width INT (5) NULL COMMENT 'application window width in pixels',
  height INT (5) NULL COMMENT 'application window height in pixels',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES streams(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'applications, for example flash, java applet, etc.' ;

CREATE TABLE areas (
  `id` INTEGER UNSIGNED NOT NULL,
  `public_name` VARCHAR(255) NULL COMMENT 'public name for publication, can be NULL',
  `public_url` VARCHAR(255) NULL COMMENT 'public url for publication, can be NULL',
  `staging_url` VARCHAR(255) NULL COMMENT 'staging/test url for publication, can be NULL',
  `email` VARCHAR(255) NULL COMMENT 'publication email, can be NULL',
  `stats_code` TEXT NULL COMMENT 'statistics code, for example google stats code. can be NULL',
  `stats_provider` VARCHAR(255) NULL COMMENT 'statistics provider, for example google. can be NULL',
  `stats_provider_url` TEXT NULL COMMENT 'statistics provider url',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'publications (web sites, etc.)' ;

CREATE TABLE `banned_ips` (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  ip_address VARCHAR(15) NOT NULL,
  created datetime NOT NULL COMMENT 'creation time',
  modified datetime NOT NULL COMMENT 'last modified time',
  status VARCHAR(10) NOT NULL default 'ban' COMMENT 'ip status (ban, accept)',
  PRIMARY KEY  (id),
  UNIQUE KEY `ip_unique` (`ip_address`),
  KEY status_idx (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'banned ips (mainly for comments)';

CREATE TABLE cake_sessions (
  id varchar(255) NOT NULL default '',
  data text,
  expires int(11) default NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE cards (
  id INTEGER UNSIGNED NOT NULL,
  name    VARCHAR(64) NULL COMMENT 'person name, can be NULL',
  surname VARCHAR(64) NULL COMMENT 'person surname, can be NULL',
  person_title VARCHAR(32) NULL COMMENT 'person title, for example Sir, Madame, Prof, Doct, ecc., can be NULL',
  gender VARCHAR(32) NULL COMMENT 'gender, for example male, female, can be NULL',
  birthdate DATE NULL COMMENT 'date of birth, can be NULL',
  deathdate DATE NULL COMMENT 'date of death, can be NULL',
  company BOOL NOT NULL DEFAULT '0' COMMENT 'is a company, default: false',
  company_name VARCHAR(128) NULL COMMENT 'name of company, can be NULL',
  company_kind VARCHAR(64) NULL COMMENT 'type of company, can be NULL',
  street_address VARCHAR(255) NULL COMMENT 'address street, can be NULL',
  city VARCHAR(255) NULL COMMENT 'city, can be NULL',
  zipcode VARCHAR(32) NULL COMMENT 'zipcode, can be NULL',
  country VARCHAR(128) NULL COMMENT 'country, can be NULL',
  state_name VARCHAR(128) NULL COMMENT 'state, can be NULL',
  email VARCHAR(128) NULL COMMENT 'first email, can be NULL',
  email2 VARCHAR(128) NULL COMMENT 'second email, can be NULL',
  phone VARCHAR(32) NULL COMMENT 'first phone number, can be NULL',
  phone2 VARCHAR(32) NULL COMMENT 'second phone number, can be NULL',
  fax VARCHAR(32) NULL COMMENT 'fax number, can be NULL',
  website VARCHAR(128) NULL COMMENT 'website url, can be NULL',
  privacy_level TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT 'level of privacy (0-9), default 0',
  newsletter_email VARCHAR(255) NULL COMMENT 'email for newsletter subscription, can be NULL',
  mail_status VARCHAR(10) NOT NULL default 'valid' COMMENT 'status of email address (valid/blocked)',
  mail_bounce int(10) unsigned NOT NULL default '0' COMMENT 'mail bounce response, default 0',
  mail_last_bounce_date datetime default NULL COMMENT 'date of last email check, can be NULL',
  mail_html tinyint(1) NOT NULL default '1' COMMENT 'html confirmation email on subscription, default:1 (true)',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'persons/companies cards, addressbook data, etc.' ;

CREATE TABLE categories (
  `id` int(10) unsigned NOT NULL auto_increment,
  `area_id` int(10) unsigned default NULL,
  `label` varchar(255) NOT NULL COMMENT 'label for category',
  `name` varchar(255) NOT NULL COMMENT 'category name',
  `object_type_id` int(10) unsigned default NULL,
  `priority` int(10) unsigned default NULL COMMENT 'order priority',
  `parent_id` INTEGER UNSIGNED NULL,
  `parent_path` MEDIUMTEXT NULL COMMENT 'path to parent, can be NULL',
  `status` VARCHAR(10) NOT NULL default 'on' COMMENT 'status of category (on/off)',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_type` (`name`,`object_type_id`),
  KEY `object_type_id` (`object_type_id`),
  KEY `index_label` (`label`),
  KEY `index_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'general categories';

CREATE TABLE `contents` (
  `id` INTEGER UNSIGNED NOT NULL,
  `start_date` DATETIME NULL ,
  `end_date` DATETIME NULL,
  `subject` VARCHAR(255) NULL,
  `abstract` MEDIUMTEXT NULL,
  `body` MEDIUMTEXT NULL,
  `duration` INTEGER UNSIGNED NULL COMMENT 'in seconds',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'general contents data' ;

CREATE TABLE date_items (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  `start_date` DATETIME NULL COMMENT 'start time, can be NULL',
  `end_date` DATETIME NULL COMMENT 'end time, can be NULL',
  `params` TEXT NULL COMMENT 'calendar params: e.g. days of week',
  PRIMARY KEY(id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'dates associated to objects' ;

CREATE TABLE `event_logs` (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` VARCHAR(200) NOT NULL COMMENT 'event user',
  created datetime NOT NULL COMMENT 'event time',
  msg VARCHAR(100) NOT NULL COMMENT 'log content',
  `log_level` VARCHAR(10) NOT NULL default 'info' COMMENT 'log level (debug, info, warn, err)',
  context VARCHAR(32) DEFAULT NULL COMMENT 'event context',
  PRIMARY KEY  (id),
  KEY userid_idx (userid),
  KEY date_idx (created)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'backend main events log';

CREATE TABLE geo_tags (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  latitude FLOAT(9,6) NULL COMMENT 'latitude, can be NULL',
  longitude FLOAT(9,6) NULL COMMENT 'longitude, can be NULL',
  address MEDIUMTEXT NULL COMMENT 'address, can be NULL',
  title MEDIUMTEXT NULL COMMENT 'geotag name/title',
  gmaps_lookat MEDIUMTEXT NULL COMMENT 'google maps code, can be NULL',
  PRIMARY KEY(id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'geotagging informations' ;

CREATE TABLE groups (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(32) NOT NULL COMMENT 'group name',
  backend_auth BOOL NOT NULL DEFAULT '0' COMMENT 'group authorized to backend (default: false)',
  immutable BOOL NOT NULL DEFAULT '0' COMMENT 'group data immutable (default:false)',
  created datetime default NULL,
  modified datetime default NULL,
  PRIMARY KEY(id),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'generic groups';

CREATE TABLE groups_users (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  group_id INTEGER UNSIGNED NOT NULL,
  user_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(id),
  INDEX groups_users_FKIndex1(user_id),
  INDEX groups_users_FKIndex2(group_id),
  FOREIGN KEY(user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(group_id)
    REFERENCES groups(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'join table for groups/users';

CREATE TABLE `hash_jobs` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `service_type` varchar(255) default NULL COMMENT 'type of hash operations',
  `user_id` int(11) unsigned NOT NULL,
  `params` text COMMENT 'serialized specific params for hash operation',
  `hash` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `expired` datetime NOT NULL COMMENT 'hash expired datetime',
  `status` VARCHAR(10) NOT NULL default 'pending' COMMENT 'job status, can be pending/expired/closed/failed',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='contains hash operations, for example subscribe/unsubscribe';

CREATE TABLE history (
  `id` INTEGER UNSIGNED NOT NULL  AUTO_INCREMENT,
  `user_id` INTEGER UNSIGNED DEFAULT NULL,
  `object_id` INTEGER UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NULL COMMENT 'title, can be NULL',
  `area_id` INTEGER UNSIGNED DEFAULT NULL COMMENT 'NULL in backend history',
  `url` VARCHAR(255) NOT NULL COMMENT '???',
  `created` DATETIME NULL,
  PRIMARY KEY(id),
  INDEX (`object_id`),
  INDEX (`user_id`),
  INDEX (`area_id`),
  INDEX (`url`),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(area_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'history of users navigation, can be in backend/frontend';

CREATE TABLE images (
  id INTEGER UNSIGNED NOT NULL,
  width INT(5) UNSIGNED NULL COMMENT 'image width, can be NULL',
  height INT(5) UNSIGNED NULL COMMENT 'image height, can be NULL',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES streams(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'image data' ;

CREATE TABLE `lang_texts` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `object_id` INTEGER UNSIGNED NOT NULL,
  `lang` CHAR(3) NOT NULL COMMENT 'language of translation, for example ita, eng, por',
  `name` VARCHAR(255) NULL COMMENT 'field/attribute name',
  `text` TEXT NULL COMMENT 'translation',
  PRIMARY KEY(id),
  INDEX lang_texts_FKIndex1(object_id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'translations of object fields/attributes' ;

CREATE TABLE links (
  id INTEGER UNSIGNED NOT NULL,
  `url` varchar(255) default NULL COMMENT '???',
  `target` VARCHAR(10) default NULL COMMENT '(_self, _blank, parent, top, popup)',
  `http_code` MEDIUMTEXT NULL COMMENT '???',
  `http_response_date` DATETIME NULL COMMENT '???',
  `source_type` VARCHAR(64) NULL COMMENT 'can be rss, wikipedia, archive.org, localresource....',
  PRIMARY KEY(id),
  KEY `idx_url` (`url`),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE `mail_groups` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `area_id` INTEGER UNSIGNED NOT NULL,
  `group_name` varchar(255) NOT NULL COMMENT '???',
  `visible` tinyint(1) NOT NULL default '1' COMMENT '???',
  `security` VARCHAR(10) NOT NULL default 'all' COMMENT 'secure level (all, none)',
  `confirmation_in_message` TEXT NULL COMMENT '???',
  `confirmation_out_message` TEXT NULL COMMENT '???',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `group_name` (`group_name`),
  KEY `area_id` (`area_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE `mail_group_cards` (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `mail_group_id` INTEGER UNSIGNED NOT NULL,
  `card_id` INTEGER UNSIGNED NOT NULL,
  `status` VARCHAR(10) NOT NULL default 'pending' COMMENT 'describe subscription status (pending, confirmed)',
  `created` datetime default NULL COMMENT '???',
  PRIMARY KEY(id),
  INDEX `card_id_index` (`card_id`),
  INDEX `mail_group_id_index` (`mail_group_id`),
  UNIQUE KEY `mail_group_card` (`card_id`, `mail_group_id`),
  FOREIGN KEY(card_id)
    REFERENCES cards(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(mail_group_id)
    REFERENCES mail_groups(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE `mail_group_messages` (
  `mail_group_id` INTEGER UNSIGNED NOT NULL,
  `mail_message_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(mail_group_id, mail_message_id),
  INDEX `mail_group_id_index` (`mail_group_id`),
  INDEX `mail_message_id_index` (`mail_message_id`),
  FOREIGN KEY(mail_message_id)
    REFERENCES mail_messages(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(mail_group_id)
    REFERENCES mail_groups(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE mail_jobs (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  mail_message_id INTEGER UNSIGNED,
  card_id INTEGER UNSIGNED,
  status VARCHAR(10) NOT NULL DEFAULT 'unsent' COMMENT 'job status (unsent, pending, sent, failed)',
  sending_date DATETIME NULL COMMENT '???',
  created DATETIME NULL COMMENT '???',
  modified DATETIME NULL COMMENT '???',
  priority INTEGER UNSIGNED NULL COMMENT '???',
  mail_body TEXT NULL COMMENT '???',
  recipient VARCHAR(255) NULL COMMENT 'email recipient, used if card_is and mail_message_id are null',
  mail_params TEXT NULL COMMENT 'serialized array with: reply-to, sender, subject, signature...',
  smtp_err TEXT NULL COMMENT 'SMTP error message on sending failure',
  process_info INTEGER UNSIGNED NULL COMMENT 'pid of process delegates to send this mail job',
  PRIMARY KEY(id),
  INDEX card_id_index(card_id),
  INDEX mail_message_id_index(mail_message_id),
  INDEX process_info_index(process_info),
  INDEX status_index(status),
  INDEX recipient_index(recipient),
  FOREIGN KEY(mail_message_id)
    REFERENCES mail_messages(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(card_id)
    REFERENCES cards(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE `mail_logs` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg` MEDIUMTEXT NOT NULL COMMENT '???',
  `log_level` VARCHAR(10) NOT NULL default 'info' COMMENT '(info, warn, err)',
  `created` datetime NOT NULL COMMENT '???',
  `recipient` VARCHAR(255) NULL COMMENT '???',
  `subject` VARCHAR(255) NULL COMMENT '???',
  `mail_params` TEXT NULL COMMENT 'on failure, serialized array with: reply-to, sender, subject, signature...',
  PRIMARY KEY(id),
  INDEX (`recipient`),
  INDEX (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE mail_messages (
  id INTEGER UNSIGNED NOT NULL,
  mail_status VARCHAR(10) DEFAULT 'unsent' NOT NULL COMMENT 'sending status (unsent, pending, injob, sent)',
  start_sending DATETIME DEFAULT NULL COMMENT '???',
  end_sending DATETIME DEFAULT NULL COMMENT '???',
  sender_name VARCHAR(255) NOT NULL COMMENT 'newsletter sender name',
  sender VARCHAR(255) NOT NULL COMMENT 'newsletter sender email',
  reply_to VARCHAR(255) NOT NULL COMMENT '???',
  bounce_to VARCHAR(255) NOT NULL COMMENT '???',
  priority INTEGER UNSIGNED NULL COMMENT '???',
  signature VARCHAR(255) NOT NULL COMMENT '???',
  privacy_disclaimer TEXT NULL COMMENT '???',
  stylesheet VARCHAR(255) DEFAULT NULL COMMENT '???',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES contents(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE `modules` (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '???',
  `label` varchar(32) default NULL COMMENT '???',
  `url` varchar(255) NOT NULL COMMENT '???',
  `status` VARCHAR(10) NOT NULL default 'on' COMMENT '(on, off)',
  `priority` int(11) default NULL COMMENT '???',
  `module_type` VARCHAR(10) NOT NULL default 'core' COMMENT '(core, plugin)',
  PRIMARY KEY  (`id`),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE objects (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_type_id INTEGER UNSIGNED NOT NULL,
  `status` VARCHAR(10) DEFAULT 'draft' COMMENT '(on, off, draft)',
  created DATETIME NULL,
  modified DATETIME NULL,
  title MEDIUMTEXT NULL COMMENT '???',
  nickname VARCHAR(255) NULL COMMENT '???',
  description MEDIUMTEXT NULL COMMENT '???',
  valid BOOL NULL DEFAULT '1' COMMENT '???',
  lang CHAR(3) NULL COMMENT '???',
  ip_created VARCHAR(15) NULL,
  user_created INTEGER UNSIGNED NOT NULL DEFAULT 1,
  user_modified INTEGER UNSIGNED NOT NULL DEFAULT 1,  
  rights VARCHAR(255) NULL COMMENT '???',
  license VARCHAR(255) NULL COMMENT '???',
  creator VARCHAR(255) NULL COMMENT '???',
  publisher VARCHAR(255) NULL COMMENT '???',
  note MEDIUMTEXT NULL COMMENT '???',
  fixed TINYINT(1) DEFAULT 0 COMMENT '???',
  comments VARCHAR(10) DEFAULT 'off' COMMENT 'define if an object is commentable (on, off, moderated)',
  PRIMARY KEY(id),
  INDEX objects_FKIndex1(object_type_id),
  UNIQUE nickname_idx(nickname),
  FOREIGN KEY(user_created)
    REFERENCES users(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_modified)
    REFERENCES users(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE object_categories (
  object_id INTEGER UNSIGNED NOT NULL,
  category_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(object_id, category_id),
  INDEX objects_has_categories_FKIndex1(object_id),
  INDEX objects_has_categories_FKIndex2(category_id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(category_id)
    REFERENCES categories(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE object_editors (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  user_id INTEGER UNSIGNED NOT NULL,
  last_access TIMESTAMP NOT NULL,
  PRIMARY KEY(id),
  INDEX object_id_index(object_id),
  INDEX user_id_index(user_id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE object_properties (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  property_id INTEGER UNSIGNED NOT NULL,
  object_id INTEGER UNSIGNED NOT NULL,
  property_value TEXT not null,
  PRIMARY KEY(id), 
  INDEX property_id_index(property_id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(property_id)
    REFERENCES properties(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE object_relations (
  id INTEGER UNSIGNED NOT NULL,
  switch varchar(63) NOT NULL default 'attach' COMMENT '???',
  object_id INTEGER UNSIGNED NOT NULL,
  priority int(11) default NULL COMMENT '???',
  params TEXT NULL COMMENT 'relation properties values',
  PRIMARY KEY  (`object_id`,`id`,`switch`),
  INDEX `related_objects_FKIndex1` (`id`),
  INDEX `related_objects_FKIndex2` (`object_id`),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE object_types (
  id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(255) NULL,
  module_name VARCHAR(32) NULL,
  PRIMARY KEY(id),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE object_users (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  user_id INTEGER UNSIGNED NOT NULL,
  switch VARCHAR(63) NOT NULL DEFAULT 'card' COMMENT '???',
  priority INTEGER(11) NULL COMMENT '???',
  params TEXT NULL COMMENT '???',
  PRIMARY KEY(id),
  UNIQUE KEY `object_id_user_id_switch` (`object_id`,`user_id`,`switch`),
  INDEX object_id_FKIndex1(object_id),
  INDEX user_id_FKIndex2(user_id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE permissions (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  ugid INTEGER UNSIGNED NOT NULL,
  switch VARCHAR(10) NOT NULL COMMENT 'permission type (user,group)',
  flag INTEGER UNSIGNED NULL COMMENT '???',
  PRIMARY KEY(`id`),
  INDEX permissions_obj_inkdex(object_id),
  INDEX permissions_ugid_switch(`ugid`, `switch`),
  UNIQUE permissions_obj_ug_sw_fl(`object_id`, `ugid`, `switch`,`flag`),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE `permission_modules` (
  `id` int(10) NOT NULL auto_increment,
  module_id INTEGER UNSIGNED NOT NULL,
  ugid INTEGER UNSIGNED NOT NULL,
  switch VARCHAR(10) NULL COMMENT 'permission type (user,group)',
  flag INTEGER UNSIGNED NULL COMMENT '???',
  PRIMARY KEY  (`id`),
  INDEX permission_modules_FKIndex1(module_id),
  INDEX permission_modules_FKIndex3(ugid),
  FOREIGN KEY(module_id)
    REFERENCES modules(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE products (
  id INTEGER UNSIGNED NOT NULL,
  abstract MEDIUMTEXT NULL COMMENT '???',
  body MEDIUMTEXT NULL COMMENT '???',
  serial_number VARCHAR(128) NULL COMMENT '???',
  weight DOUBLE NULL COMMENT '???',
  width DOUBLE NULL COMMENT '???',
  height DOUBLE NULL COMMENT '???',
  product_depth DOUBLE NULL COMMENT '???',
  volume DOUBLE NULL COMMENT '???',
  length_unit VARCHAR(40) NULL COMMENT '???',
  weight_unit VARCHAR(40) NULL COMMENT '???',
  volume_unit VARCHAR(40) NULL COMMENT '???',
  color VARCHAR(128) NULL COMMENT '???',
  production_date DATETIME NULL COMMENT '???',
  production_place VARCHAR(255) NULL COMMENT '???',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE properties (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  object_type_id INTEGER UNSIGNED NULL,
  property_type VARCHAR(10) NOT NULL COMMENT '(number, date, text, options)',
  multiple_choice TINYINT(1) default 0 COMMENT '???',
  PRIMARY KEY(id),
  UNIQUE name_type(name, object_type_id),
  INDEX name_index(name),
  INDEX type_index(object_type_id),
  FOREIGN KEY(object_type_id)
    REFERENCES object_types(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE property_options (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  property_id INTEGER UNSIGNED NOT NULL,
  property_option TEXT not null COMMENT '???',
  PRIMARY KEY(id),
  FOREIGN KEY(property_id)
    REFERENCES properties(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE `search_texts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL,
  `lang` varchar(3) NOT NULL COMMENT '???',
  `content` mediumtext NOT NULL COMMENT '???',
  `relevance` tinyint(4) NOT NULL default '1' COMMENT 'importance (1-10) range',
  PRIMARY KEY  (`id`),
  KEY `object_id` (`object_id`,`lang`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='search texts table';

CREATE TABLE sections (
  id INTEGER UNSIGNED NOT NULL,
  syndicate VARCHAR(10) DEFAULT 'on' COMMENT '(on, off)',
  priority_order VARCHAR(10) DEFAULT 'asc' COMMENT 'order of objects inserted in section (asc, desc)',
  last_modified DATETIME NULL,
  map_priority FLOAT(2,1) NULL COMMENT '???',
  map_changefreq VARCHAR(128) NULL COMMENT '???',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE section_types (
  id INTEGER UNSIGNED NOT NULL,
  section_id INTEGER UNSIGNED NOT NULL,
  object_type_id INTEGER UNSIGNED NOT NULL,
  restricted TINYINT NULL COMMENT '???',
  predefined TINYINT NULL COMMENT '???',
  PRIMARY KEY(id),
  FOREIGN KEY(section_id)
    REFERENCES sections(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE streams (
  id INTEGER UNSIGNED NOT NULL,
  uri VARCHAR(255) NOT NULL COMMENT '???',
  name VARCHAR(255) NULL COMMENT '???',
  mime_type VARCHAR(60) NULL COMMENT '???',
  file_size INTEGER UNSIGNED NULL COMMENT '???',
  hash_file VARCHAR(255) NULL COMMENT '???',
  original_name VARCHAR(255) NULL COMMENT 'original name for uploaded file',
  PRIMARY KEY(id),
  INDEX hash_file_index(hash_file),
  FOREIGN KEY(id)
    REFERENCES contents(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE trees (
  id INTEGER UNSIGNED NOT NULL,
  area_id INTEGER UNSIGNED NULL,
  parent_id INTEGER UNSIGNED NULL,
  object_path VARCHAR(255) NOT NULL COMMENT '???',
  parent_path VARCHAR(255) NULL COMMENT '???',
  priority INTEGER UNSIGNED NULL COMMENT '???',
  menu INTEGER UNSIGNED NOT NULL default '0',
  INDEX id_idx(id),
  INDEX parent_idx(parent_id),
  INDEX area_idx(area_id),
  UNIQUE object_path(object_path),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(parent_id)
    REFERENCES objects(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;


CREATE TABLE user_properties (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  property_id INTEGER UNSIGNED NOT NULL,
  user_id INTEGER UNSIGNED NOT NULL,
  property_value TEXT not null,
  PRIMARY KEY(id), 
  INDEX property_id_index(property_id),
  FOREIGN KEY(user_id)
    REFERENCES users(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(property_id)
    REFERENCES properties(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = 'user custom properties values' ;


CREATE TABLE users (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  userid VARCHAR(200) NOT NULL,
  realname VARCHAR(255) NULL,
  passwd VARCHAR(255) NULL,
  email varchar(255) default NULL,
  valid tinyint(1) NOT NULL default '1',
  last_login datetime default NULL,
  last_login_err datetime default NULL,
  num_login_err int(11) NOT NULL default '0',
  created datetime default NULL,
  modified datetime default NULL,
  user_level TINYINT(1) NOT NULL DEFAULT '0' COMMENT '???',
  auth_type varchar(255) default NULL COMMENT '???',
  auth_params TEXT default NULL COMMENT '???',
  lang CHAR(3) NULL COMMENT '???',
  time_zone CHAR(9) NULL COMMENT 'format UTC+/-hh:mm - eg UTC+11:30' COMMENT '???',
  comments VARCHAR(10) default NULL COMMENT 'notify new comments option (never, mine, all)',
  notes VARCHAR(10) default NULL COMMENT 'notify new notes option (never, mine, all)',
  notify_changes TINYINT(1) DEFAULT NULL COMMENT '???',
  reports TINYINT(1) DEFAULT NULL COMMENT '???',
  PRIMARY KEY  (id),
  UNIQUE KEY userid (userid),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???';

CREATE TABLE versions (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `object_id` INTEGER UNSIGNED NOT NULL,
  `revision` INTEGER UNSIGNED NOT NULL COMMENT '???',
  `user_id` INTEGER UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `diff` TEXT NOT NULL,
  PRIMARY KEY(id),
  INDEX objects_index(object_id),
  INDEX user_index(user_id),
  UNIQUE object_id_revision(object_id, revision),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_id)
    REFERENCES users(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;

CREATE TABLE videos (
  id INTEGER UNSIGNED NOT NULL,
  provider VARCHAR( 255 ) NULL COMMENT '???' ,
  video_uid VARCHAR( 255 ) NULL COMMENT '???',
  thumbnail VARCHAR (255) NULL COMMENT '???',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES streams(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '???' ;
