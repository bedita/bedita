ALTER TABLE `streams` ADD `original_name`  VARCHAR(255) NULL COMMENT 'original name for uploaded file';
ALTER TABLE `trees` MODIFY `menu` INTEGER UNSIGNED NOT NULL;
ALTER TABLE `trees` ALTER COLUMN `menu` SET DEFAULT 0;
ALTER TABLE `geo_tags` ADD `title` MEDIUMTEXT NULL COMMENT 'geotag name/title';
ALTER TABLE `date_items` ADD `params` TEXT NULL COMMENT 'calendar params: e.g. days of week';

