ALTER TABLE `streams` ADD `original_name`  VARCHAR(255) NULL COMMENT 'original name for uploaded file';
ALTER TABLE `trees` MODIFY `menu` INTEGER UNSIGNED NOT NULL;
ALTER TABLE `trees` ALTER COLUMN `menu` SET DEFAULT 0;

