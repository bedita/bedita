DROP TABLE IF EXISTS `authors`;
DELETE FROM `object_types` WHERE `name` = 'author';
ALTER TABLE `object_relations` ADD  `params` TEXT NULL COMMENT 'relation properties values';
ALTER TABLE  `permissions` ADD UNIQUE permissions_obj_ug_sw_fl(`object_id`, `ugid`, `switch`,`flag`);