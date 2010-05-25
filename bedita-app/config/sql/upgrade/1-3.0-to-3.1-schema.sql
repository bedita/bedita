ALTER TABLE `modules` ADD `type` enum('core','plugin') DEFAULT 'core' NOT NULL;
ALTER TABLE `questions` ADD `text_ok` text DEFAULT NULL, ADD `text_fail` text DEFAULT NULL;
ALTER TABLE `annotations` DROP FOREIGN KEY `annotations_ibfk_2`;
ALTER TABLE `versions` ADD UNIQUE object_id_revision(object_id, revision);
ALTER TABLE `users` ADD UNIQUE email(email);
ALTER TABLE `event_logs` CHANGE `user` `userid` VARCHAR(32) NOT NULL COMMENT 'event user';
ALTER TABLE `event_logs` CHANGE `level` `log_level` set('debug','info','warn','err') NOT NULL default 'info' COMMENT 'log level (debug/info/warn/err)';
ALTER TABLE `properties` CHANGE `object_type_id` `object_type_id` INT( 10 ) UNSIGNED NULL;
ALTER TABLE `trees` CHANGE `path` `path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `trees` CHANGE `parent_path` `parent_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `trees` ADD UNIQUE `path` ( `path` );

CREATE TABLE history (
  `id` INTEGER UNSIGNED NOT NULL  AUTO_INCREMENT,
  `user_id` INTEGER UNSIGNED DEFAULT NULL,
  `object_id` INTEGER UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NULL,
  `area_id` INTEGER UNSIGNED DEFAULT NULL COMMENT 'NULL in backend history',
  `path` VARCHAR(255) NOT NULL,
  `created` DATETIME NULL,
  PRIMARY KEY(id),
  INDEX (`object_id`),
  INDEX (`user_id`),
  INDEX (`area_id`),
  INDEX (`path`),
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
  INDEX id_index(id),
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
