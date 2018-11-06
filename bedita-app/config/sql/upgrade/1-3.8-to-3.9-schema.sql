ALTER TABLE `objects` ADD INDEX `objects_publisher_idx` (`publisher`);

INSERT INTO `object_types` (`id`, `name`, `module_name`) VALUES (42, 'caption', 'multimedia') ON DUPLICATE KEY UPDATE `id` = 42;
