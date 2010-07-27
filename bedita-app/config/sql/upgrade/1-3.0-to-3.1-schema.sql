ALTER TABLE `modules` ADD `type` enum('core','plugin') DEFAULT 'core' NOT NULL;
ALTER TABLE `annotations` DROP FOREIGN KEY `annotations_ibfk_2`;
ALTER TABLE `versions` ADD UNIQUE object_id_revision(object_id, revision);
ALTER TABLE `users` ADD UNIQUE email(email);
ALTER TABLE `trees` CHANGE `path` `object_path` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `trees` CHANGE `parent_path` `parent_path` VARCHAR( 255 );
ALTER TABLE `trees` ADD UNIQUE `object_path` ( `object_path` );

ALTER TABLE `event_logs` CHANGE `user` `userid` VARCHAR(32) NOT NULL COMMENT 'event user';
ALTER TABLE `event_logs` CHANGE `level` `log_level` VARCHAR(10) NOT NULL default 'info' COMMENT 'log level (debug, info, warn, err)';
ALTER TABLE `properties` CHANGE `object_type_id` `object_type_id` INT( 10 ) UNSIGNED NULL;
ALTER TABLE  `cards` CHANGE  `state`  `state_name` VARCHAR( 128 );
ALTER TABLE `contents` CHANGE `start` `start_date` DATETIME;
ALTER TABLE `contents` CHANGE `end` `end_date` DATETIME;
ALTER TABLE `date_items` CHANGE `start` `start_date` DATETIME;
ALTER TABLE `date_items` CHANGE `end` `end_date` DATETIME;
ALTER TABLE `modules` CHANGE `path` `url` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `streams` CHANGE `path` `uri` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `mail_logs` CHANGE `level` `log_level` VARCHAR(10) NOT NULL DEFAULT 'info' COMMENT '(info, warn, err)';
ALTER TABLE  `modules` CHANGE  `type`  `module_type` VARCHAR( 10 ) NOT NULL DEFAULT  'core' COMMENT  '(core, plugin)';
ALTER TABLE  `object_types` CHANGE  `module`  `module_name` VARCHAR( 32 );
ALTER TABLE  `objects` CHANGE  `current`  `valid` BOOL NULL DEFAULT '1';
ALTER TABLE  `products` CHANGE  `depth`  `product_depth` DOUBLE;
ALTER TABLE  `streams` CHANGE  `size`  `file_size` INT( 10 );
ALTER TABLE  `users` CHANGE  `level`  `user_level` tinyint(1) DEFAULT 0 NOT NULL;
ALTER TABLE  `videos` CHANGE  `uid`  `video_uid` VARCHAR( 255 );
ALTER TABLE  `applications` CHANGE  `text_dir`  `text_dir` VARCHAR( 10 ) NULL DEFAULT 'ltr' COMMENT 'text orientation (ltr:left to right;rtl: right to left)';
ALTER TABLE  `cards` CHANGE  `mail_status`  `mail_status` VARCHAR( 10 ) NOT NULL DEFAULT 'valid' COMMENT 'status of email address (valid/blocked)';
ALTER TABLE  `categories` CHANGE  `status`  `status` VARCHAR( 10 ) NOT NULL DEFAULT 'on' COMMENT 'status of category (on/off)';
ALTER TABLE  `hash_jobs` CHANGE  `status`  `status` VARCHAR( 10 ) NOT NULL default 'pending' COMMENT 'job status, can be pending/expired/closed/failed';
ALTER TABLE  `links` CHANGE  `target`  `target` VARCHAR( 10 ) NULL DEFAULT NULL COMMENT '(_self, _blank, parent, top, popup)';
ALTER TABLE  `mail_groups` CHANGE  `security`  `security` VARCHAR( 10 ) NOT NULL DEFAULT 'all' COMMENT 'secure level (all, none)';
ALTER TABLE  `mail_group_cards` CHANGE  `status`  `status` VARCHAR( 10 ) NOT NULL DEFAULT  'pending' COMMENT 'describe subscription status (pending, confirmed)';
ALTER TABLE  `mail_jobs` CHANGE  `status`  `status` VARCHAR( 10 ) NOT NULL DEFAULT  'unsent' COMMENT  'job status (unsent, pending, sent, failed)';
ALTER TABLE  `mail_messages` CHANGE  `mail_status`  `mail_status` VARCHAR( 10 ) NOT NULL DEFAULT  'unsent' COMMENT 'sending status (unsent, pending, injob, sent)';
ALTER TABLE  `modules` CHANGE  `status`  `status` VARCHAR( 10 ) NOT NULL DEFAULT  'on' COMMENT  '(on, off)';
ALTER TABLE  `objects` CHANGE  `status`  `status` VARCHAR( 10 ) NULL DEFAULT  'draft' COMMENT  '(on, off, draft)';
ALTER TABLE  `objects` CHANGE  `comments`  `comments` VARCHAR( 10 ) NULL DEFAULT  'off' COMMENT  'define if an object is commentable (on, off, moderated)';
ALTER TABLE  `sections` CHANGE  `syndicate`  `syndicate` VARCHAR( 10 ) NULL DEFAULT  'on' COMMENT  '(on, off)';
ALTER TABLE  `sections` CHANGE  `priority_order`  `priority_order` VARCHAR( 10 ) NULL DEFAULT  'asc' COMMENT 'order of objects inserted in section (asc, desc)';
ALTER TABLE  `users` CHANGE  `comments`  `comments` VARCHAR( 10 ) NULL DEFAULT NULL COMMENT  'notify new comments option (never, mine, all)';
ALTER TABLE  `users` CHANGE  `notes`  `notes` VARCHAR( 10 ) NULL DEFAULT NULL COMMENT  'notify new notes option (never, mine, all)';
ALTER TABLE  `banned_ips` CHANGE  `status`  `status` VARCHAR( 10 ) NOT NULL DEFAULT  'ban' COMMENT  'ip status (ban, accept)';
ALTER TABLE `permissions` CHANGE `switch` `switch` VARCHAR( 10 ) NOT NULL COMMENT 'permission type (user,group)';
ALTER TABLE `permission_modules` CHANGE `switch` `switch` VARCHAR( 10 ) NULL DEFAULT NULL COMMENT 'permission type (user,group)';
ALTER TABLE `properties` CHANGE `property_type` `property_type` VARCHAR( 10 ) NOT NULL COMMENT '(number, date, text, options)';
ALTER TABLE `contents` DROP `type`;
ALTER TABLE  `cards` DROP  `street_number`;
ALTER TABLE `object_properties` ADD PRIMARY KEY ( `id` );
ALTER TABLE `object_properties` DROP INDEX `id_index`;
ALTER TABLE `event_logs` DROP KEY user_idx;
ALTER TABLE `event_logs` ADD KEY userid_idx (`userid`);
ALTER TABLE `mail_jobs` CHANGE `recipient` `recipient` VARCHAR( 255 ) NULL COMMENT 'email recipient, used if card_is and mail_message_id are null';
ALTER TABLE `mail_jobs` ADD KEY recipient_index (`recipient`);
ALTER TABLE `mail_jobs` ADD KEY status_index (`status`);

CREATE TABLE history (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INTEGER UNSIGNED DEFAULT NULL,
  `object_id` INTEGER UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NULL,
  `area_id` INTEGER UNSIGNED DEFAULT NULL COMMENT 'NULL in backend history',
  `url` VARCHAR(255) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
