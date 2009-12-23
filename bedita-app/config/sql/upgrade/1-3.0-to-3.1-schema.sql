ALTER TABLE `modules` ADD `type` enum('core','plugin') DEFAULT 'core' NOT NULL;
ALTER TABLE `questions` ADD `text_ok` text DEFAULT NULL, ADD `text_fail` text DEFAULT NULL;
