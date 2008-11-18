-- 
-- bedita database main schema
-- 
SET FOREIGN_KEY_CHECKS=0;

DROP VIEW IF EXISTS `view_permissions` ;
DROP VIEW IF EXISTS `view_trees` ;

-- old tables/views --
DROP TABLE IF EXISTS `answers`;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `base_documents`;
DROP TABLE IF EXISTS `content_bases`;
DROP TABLE IF EXISTS `content_bases_objects`;
DROP TABLE IF EXISTS `content_objects`;
DROP TABLE IF EXISTS `content_bases_object_categories`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `short_news`;
DROP TABLE IF EXISTS `audio`;
DROP TABLE IF EXISTS `video`;
DROP TABLE IF EXISTS `content_bases_object_categories`;
DROP TABLE IF EXISTS `contents_object_categories`;
DROP TABLE IF EXISTS `newsletters`;
DROP TABLE IF EXISTS `files`;
DROP TABLE IF EXISTS `audios`;
DROP TABLE IF EXISTS `collections`;
DROP TABLE IF EXISTS `bibliographies`;
DROP TABLE IF EXISTS `biblio_items`;
DROP TABLE IF EXISTS `mail_addresses`;
DROP TABLE IF EXISTS `mail_group_addresses`;
DROP VIEW IF EXISTS `view_galleries` ;
DROP VIEW IF EXISTS `view_communities`;
DROP VIEW IF EXISTS `view_faqs`;
DROP VIEW IF EXISTS `view_questionnaires`;
DROP VIEW IF EXISTS `view_timelines`;
DROP VIEW IF EXISTS `view_scrolls`;
DROP VIEW IF EXISTS `view_sections`;
DROP VIEW IF EXISTS `view_streams`;
DROP TABLE IF EXISTS `custom_properties`;

-- current tables --
DROP TABLE IF EXISTS `cake_sessions`;
DROP TABLE IF EXISTS `links`;
DROP TABLE IF EXISTS `cards`;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `date_items`;
DROP TABLE IF EXISTS `geo_tags`;
DROP TABLE IF EXISTS `object_users`;
DROP TABLE IF EXISTS `object_relations`;
DROP TABLE IF EXISTS `object_categories`;
DROP TABLE IF EXISTS `object_properties`;
DROP TABLE IF EXISTS `object_editors`;
DROP TABLE IF EXISTS `contents`;
DROP TABLE IF EXISTS `authors`;
DROP TABLE IF EXISTS `images`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `faq_questions`;
DROP TABLE IF EXISTS `audio_videos`;
DROP TABLE IF EXISTS `videos`;
DROP TABLE IF EXISTS `areas`;
DROP TABLE IF EXISTS `sections`;
DROP TABLE IF EXISTS `streams`;
DROP TABLE IF EXISTS `mail_messages`;
DROP TABLE IF EXISTS `mail_templates`;
DROP TABLE IF EXISTS `mail_groups`;
DROP TABLE IF EXISTS `mail_group_cards`;
DROP TABLE IF EXISTS `mail_group_messages`;
DROP TABLE IF EXISTS `mail_jobs`;
DROP TABLE IF EXISTS `lang_texts`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `object_users`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `versions`;
DROP TABLE IF EXISTS `trees`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `properties`;
DROP TABLE IF EXISTS `property_options`;
DROP TABLE IF EXISTS `objects`;
DROP TABLE IF EXISTS `question_types`;
DROP TABLE IF EXISTS `object_types`;
DROP TABLE IF EXISTS `groups_users`;
DROP TABLE IF EXISTS `permission_modules`;
DROP TABLE IF EXISTS `modules`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `groups`;
DROP TABLE IF EXISTS `event_logs`;
DROP TABLE IF EXISTS `search_texts`;
DROP TABLE IF EXISTS `banned_ips`;

CREATE TABLE cake_sessions (
  id varchar(255) NOT NULL default '',
  data text,
  expires int(11) default NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE `event_logs` (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  user VARCHAR(32) NOT NULL,
  created datetime NOT NULL,
  msg VARCHAR(100) NOT NULL,
  level set('debug','info','warn','err') NOT NULL default 'info',
  context VARCHAR(32) DEFAULT NULL,
  PRIMARY KEY  (id),
  KEY user_idx (user),
  KEY date_idx (created)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE `banned_ips` (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  ip_address VARCHAR(15) NOT NULL,
  created datetime NOT NULL,
  modified datetime NOT NULL,
  status set('ban','accept') NOT NULL default 'ban',
  PRIMARY KEY  (id),
  UNIQUE KEY `ip_unique` (`ip_address`),
  KEY status_idx (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE groups (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(32) NULL,
  created datetime default NULL,
  modified datetime default NULL,
 PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE users (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  userid VARCHAR(32) NOT NULL ,
  realname VARCHAR(255) NULL,
  passwd VARCHAR(255) NULL,
  email varchar(255) default NULL,
  valid tinyint(1) NOT NULL default '1',
  last_login datetime default NULL,
  last_login_err datetime default NULL,
  num_login_err int(11) NOT NULL default '0',
  created datetime default NULL,
  modified datetime default NULL,
  level TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (id),
  UNIQUE KEY userid (userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE groups_users (
  user_id INTEGER UNSIGNED NOT NULL,
  group_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(user_id, group_id),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;


CREATE TABLE object_types (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NULL,
  module VARCHAR(32) NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;



CREATE TABLE question_types (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  label VARCHAR(255) NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE objects (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_type_id INTEGER UNSIGNED NOT NULL,
  `status` ENUM('on','off','draft','staging') DEFAULT 'draft',
  created DATETIME NULL,
  modified DATETIME NULL,
  title VARCHAR(255) NULL,
  nickname VARCHAR(255) NULL,
  description MEDIUMTEXT NULL,
  current BOOL NULL DEFAULT '1',
  lang CHAR(3) NULL,
  ip_created VARCHAR(15) NULL,
  user_created INTEGER UNSIGNED NULL,
  user_modified INTEGER UNSIGNED NULL,
  rights VARCHAR(255) NULL,
  license VARCHAR(255) NULL,
  creator VARCHAR(255) NULL,
  publisher VARCHAR(255) NULL,
  note TEXT NULL,
  fixed TINYINT(1) DEFAULT 0,
  PRIMARY KEY(id),
  INDEX objects_FKIndex1(object_type_id),
  FOREIGN KEY(user_created)
    REFERENCES users(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_modified)
    REFERENCES users(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;


CREATE TABLE properties (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  object_type_id INTEGER UNSIGNED NOT NULL,
  property_type SET('number','date','text','options') NOT NULL,
  multiple_choice TINYINT(1) default 0,
  PRIMARY KEY(id),
  UNIQUE name_type(name, object_type_id),
  INDEX name_index(name),
  INDEX type_index(object_type_id),
  FOREIGN KEY(object_type_id)
    REFERENCES object_types(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE property_options (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  property_id INTEGER UNSIGNED NOT NULL,
  property_option TEXT not null,
  PRIMARY KEY(id),
  FOREIGN KEY(property_id)
    REFERENCES properties(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE object_properties (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  property_id INTEGER UNSIGNED NOT NULL,
  object_id INTEGER UNSIGNED NOT NULL,
  property_value TEXT not null,
  INDEX id_index(id),
  INDEX property_id_index(property_id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(property_id)
    REFERENCES properties(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE contents (
  id INTEGER UNSIGNED NOT NULL,
  `start` DATETIME NULL ,
  `end` DATETIME NULL,
  subject VARCHAR(255) NULL,
  abstract MEDIUMTEXT NULL,
  body MEDIUMTEXT NULL,
  type ENUM('html','txt','txtParsed') DEFAULT 'txt',
  comments ENUM('on','off','moderated') DEFAULT 'off',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE comments (
  id INTEGER UNSIGNED NOT NULL,
  author VARCHAR(255) NULL,
  email VARCHAR(255) NULL,
  url VARCHAR(255) NULL,
  thread_path MEDIUMTEXT NULL,
  PRIMARY KEY(id),
  INDEX author_FKIndex1(author),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- 
-- La cancellazione deve essere gestita separatamente
-- 
CREATE TABLE trees (
  id INTEGER UNSIGNED NOT NULL,
  parent_id INTEGER UNSIGNED NULL,
  path MEDIUMTEXT NOT NULL,
  parent_path MEDIUMTEXT NULL,
  priority INTEGER UNSIGNED NULL,
  INDEX trees_FKIndex1(id),
  INDEX trees_FKIndex2(parent_id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(parent_id)
    REFERENCES objects(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE versions (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `object_id` INTEGER UNSIGNED NOT NULL,
  `revision` INTEGER UNSIGNED NOT NULL,
  `user_id` INTEGER UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `diff` TEXT NOT NULL,
  PRIMARY KEY(id),
  INDEX objects_index(object_id),
  INDEX user_index(user_id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(user_id)
    REFERENCES users(id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE questions (
  id INTEGER UNSIGNED NOT NULL,
  question_type_id INTEGER UNSIGNED NOT NULL,
  max_chars INTEGER UNSIGNED NULL,
  PRIMARY KEY(id),
  INDEX questions_FKIndex1(id),
  INDEX questions_FKIndex2(question_type_id),
  FOREIGN KEY(id)
    REFERENCES contents(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE object_users (
  object_id INTEGER UNSIGNED NOT NULL,
  user_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(object_id, user_id),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE permissions (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  ugid INTEGER UNSIGNED NOT NULL,
  switch SET('user','group') NOT NULL,
  flag INTEGER UNSIGNED NULL,
  PRIMARY KEY(`id`),
  INDEX permissions_obj_inkdex(object_id),
  INDEX permissions_ugid_switch(`ugid`, `switch`),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE lang_texts (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  lang CHAR(3) NOT NULL,
  `name` VARCHAR(255) NULL,
  `text` TEXT NULL,
  PRIMARY KEY(id),
  INDEX lang_texts_FKIndex1(object_id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE streams (
  id INTEGER UNSIGNED NOT NULL,
  path VARCHAR(255) NOT NULL ,
  name VARCHAR(255) NULL,
  mime_type VARCHAR(60) NULL,
  size INTEGER UNSIGNED NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES contents(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE areas (
  id INTEGER UNSIGNED NOT NULL,
  public_name VARCHAR(255) NULL,
  public_url VARCHAR(255) NULL,
  staging_url VARCHAR(255) NULL,
  email VARCHAR(255) NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE sections (
  id INTEGER UNSIGNED NOT NULL,
  syndicate ENUM('on','off') DEFAULT 'on',
  priority_order ENUM('asc','desc') DEFAULT 'asc',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE videos (
  id INTEGER UNSIGNED NOT NULL,
  provider VARCHAR( 255 ) NULL ,
  uid VARCHAR( 255 ) NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES streams(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE faq_questions (
  id INTEGER UNSIGNED NOT NULL,
  question_text MEDIUMTEXT NULL,
  answer_text MEDIUMTEXT NULL,
  name VARCHAR(50) NULL,
  surname VARCHAR(50) NULL,
  PRIMARY KEY(id),
  INDEX faq_questions_FKIndex1(id),
  FOREIGN KEY(id)
    REFERENCES contents(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE categories (
  `id` int(10) unsigned NOT NULL auto_increment,
  `area_id` int(10) unsigned default NULL,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `object_type_id` int(10) unsigned default NULL,
  `priority` int(10) unsigned default NULL,
  `status` enum('on','off','draft','staging') NOT NULL default 'draft',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `area_name_type` (`area_id`,`name`,`object_type_id`),
  KEY `object_type_id` (`object_type_id`),
  KEY `index_label` (`label`),
  KEY `index_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE images (
  id INTEGER UNSIGNED NOT NULL,
  width INT(5) UNSIGNED NULL,
  height INT(5) UNSIGNED NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES streams(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE authors (
  id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(60) NULL,
  surname VARCHAR(60) NULL,
  search_string VARCHAR(255) NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES contents(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE object_relations (
  object_id INTEGER UNSIGNED NOT NULL,
  id INTEGER UNSIGNED NOT NULL,
  switch varchar(63) NOT NULL default 'attach',
  priority int(11) default NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE date_items (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  `start` DATETIME NULL,
  `end` DATETIME NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE geo_tags (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  object_id INTEGER UNSIGNED NOT NULL,
  latitude FLOAT(9,6) NULL,
  longitude FLOAT(9,6) NULL,
  address VARCHAR(255) NULL,
  gmaps_lookat MEDIUMTEXT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(object_id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE books (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  author_1 TINYTEXT NULL,
  author_2 TINYTEXT NULL,
  isbn VARCHAR(32) NULL,
  editor VARCHAR(64) NULL,
  year DATE NULL,
  place VARCHAR(255) NULL,
  lang VARCHAR(60) NULL,
  weight DOUBLE NULL,
  dim_x DOUBLE NULL,
  dim_y DOUBLE NULL,
  note TINYTEXT NULL,
  category VARCHAR(255) NULL,
  position VARCHAR(255) NULL,
  inv VARCHAR(255) NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES contents(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
)ENGINE=InnoDB DEFAULT CHARSET=utf8 ;


CREATE TABLE links (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(255) default NULL,
  `target` enum('_self','_blank','parent','top','popup') default NULL,
  PRIMARY KEY(id),
  KEY `idx_url` (`url`),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE cards (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name    VARCHAR(64) NULL,
  surname VARCHAR(64) NULL,
  person_title VARCHAR(32) NULL,
  gender VARCHAR(32) NULL,
  birthdate DATE NULL,
  deathdate DATE NULL,
  company BOOL NOT NULL DEFAULT '0',
  company_name VARCHAR(128) NULL,
  company_kind VARCHAR(64) NULL,
  street_address VARCHAR(255) NULL,
  street_number VARCHAR(32) NULL,
  city VARCHAR(255) NULL,
  zipcode VARCHAR(32) NULL,
  country VARCHAR(128) NULL,
  state VARCHAR(128) NULL,
  email VARCHAR(128) NULL,
  email2 VARCHAR(128) NULL,
  phone VARCHAR(32) NULL,
  phone2 VARCHAR(32) NULL,
  fax VARCHAR(32) NULL,
  website VARCHAR(128) NULL,
  privacy_level TINYINT( 1 ) NOT NULL DEFAULT '0',
  newsletter_email VARCHAR(255) NULL,
  mail_status enum('blocked','valid') NOT NULL default 'valid',
  mail_bounce int(10) unsigned NOT NULL default '0',
  mail_last_bounce_date datetime default NULL,
  mail_html tinyint(1) NOT NULL default '1',
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES objects(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

CREATE TABLE mail_messages (
  id INTEGER UNSIGNED NOT NULL,
  mail_status ENUM('draft', 'unsent','pending','sent') DEFAULT 'draft' NOT NULL,
  start_sending DATETIME DEFAULT NULL,
  end_sending DATETIME DEFAULT NULL,
  sender VARCHAR(255) NOT NULL,
  replay_to VARCHAR(255) NOT NULL,
  bounce_to VARCHAR(255) NOT NULL,
  priority INTEGER UNSIGNED NULL,
  signature VARCHAR(255) NOT NULL,
  stylesheet VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(id)
    REFERENCES contents(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mail_groups` (
  `id` INTEGER UNSIGNED NOT NULL auto_increment,
  `area_id` INTEGER UNSIGNED NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `visible` tinyint(1) NOT NULL default '1',
  `security` enum('all','none') NOT NULL default 'all',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `group_name` (`group_name`),
  KEY `area_id` (`area_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `mail_group_cards` (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `mail_group_id` INTEGER UNSIGNED NOT NULL,
  `card_id` INTEGER UNSIGNED NOT NULL,
  `status` enum('pending','confirmed') NOT NULL default 'pending',
  `command` enum('confirm','delete','modify') NOT NULL default 'confirm',
  `hash` varchar(255) default NULL,
  `created` datetime default NULL,
  PRIMARY KEY(id),
  INDEX `card_id_index` (`card_id`),
  INDEX `mail_group_id_index` (`mail_group_id`),
  FOREIGN KEY(card_id)
    REFERENCES cards(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(mail_group_id)
    REFERENCES mail_groups(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE mail_jobs (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  mail_message_id INTEGER UNSIGNED NOT NULL,
  card_id INTEGER UNSIGNED NOT NULL,
  status ENUM ('unsent','sent','failed') NOT NULL DEFAULT 'unsent',
  sending_date DATETIME NULL,
  created DATETIME NULL,
  modified DATETIME NULL,
  priority INTEGER UNSIGNED NULL,
  PRIMARY KEY(id),
  INDEX card_id_index(card_id),
  INDEX mail_message_id_index(mail_message_id),
  FOREIGN KEY(mail_message_id)
    REFERENCES mail_messages(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION,
  FOREIGN KEY(card_id)
    REFERENCES cards(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `modules` (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `label` varchar(32) default NULL,
  `path` varchar(255) NOT NULL,
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`),
  UNIQUE KEY name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;


CREATE TABLE `permission_modules` (
  `id` int(10) NOT NULL auto_increment,
  module_id INTEGER UNSIGNED NOT NULL,
  ugid INTEGER UNSIGNED NOT NULL,
  switch SET('user','group') NULL,
  flag INTEGER UNSIGNED NULL,
  PRIMARY KEY  (`id`),
  INDEX permission_modules_FKIndex1(module_id),
  INDEX permission_modules_FKIndex3(ugid),
  FOREIGN KEY(module_id)
    REFERENCES modules(id)
      ON DELETE CASCADE
      ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;


CREATE TABLE `search_texts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL,
  `lang` varchar(3) NOT NULL,
  `content` mediumtext NOT NULL,
  `relevance` tinyint(4) NOT NULL default '1' COMMENT 'importance (1-10) range',
  PRIMARY KEY  (`id`),
  KEY `object_id` (`object_id`,`lang`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='search texts table';

-- ------------------------------------------
-- Permessi
-- ------------------------------------------

CREATE  VIEW `view_permissions` AS 
(
SELECT 
permissions.object_id,
userid AS name,
permissions.switch,
permissions.flag
FROM
permissions INNER JOIN users ON permissions.ugid = users.id AND permissions.switch = 'user' 
)
UNION
(
SELECT  
permissions.object_id,
name,
permissions.switch,
permissions.flag
FROM
permissions INNER JOIN groups ON permissions.ugid = groups.id AND permissions.switch = 'group' 
)
;

-- ------------------------------------------
-- Tree
-- ------------------------------------------
CREATE  VIEW `view_trees` AS 
SELECT
trees.*,
objects.object_type_id,
objects.status,
objects.title,
objects.nickname,
objects.lang
FROM
trees INNER JOIN objects ON trees.id = objects.id 
ORDER BY parent_path, priority
;
