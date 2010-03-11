ALTER TABLE `modules` ADD `type` enum('core','plugin') DEFAULT 'core' NOT NULL;
ALTER TABLE `questions` ADD `text_ok` text DEFAULT NULL, ADD `text_fail` text DEFAULT NULL;
ALTER TABLE `annotations` DROP FOREIGN KEY `annotations_ibfk_2`;
ALTER TABLE `versions` ADD UNIQUE object_id_revision(object_id, revision);
ALTER TABLE `users` ADD UNIQUE email(email);

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
