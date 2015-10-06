-- PLEASE, ENSURE YOU HAVE A BACKUP OF YOUR DATA AVAILABLE.
-- THIS OPERATION MIGHT TAKE A LONG TIME FOR LARGE DATABASES.

-- Rename old columns.
ALTER TABLE `date_items`
    CHANGE `start_date` `start_date_tmp` DATETIME NULL DEFAULT NULL,
    CHANGE `end_date` `end_date_tmp` DATETIME NULL DEFAULT NULL;

-- Create new columns.
ALTER TABLE `date_items`
    ADD `start_date` BIGINT NULL DEFAULT NULL COMMENT 'start timestamp, can be NULL' AFTER `object_id`,
    ADD `end_date` BIGINT NULL DEFAULT NULL COMMENT 'end timestamp, can be NULL' AFTER `start_date`;

-- Convert data.
UPDATE `date_items` SET
    `start_date` = UNIX_TIMESTAMP(`start_date_tmp`),
    `end_date` = UNIX_TIMESTAMP(`end_date_tmp`);

-- Drop old columns.
ALTER TABLE `date_items`
    DROP `start_date_tmp`,
    DROP `end_date_tmp`;

-- Optional: create legacy view.
CREATE VIEW `date_items_legacy` AS
    SELECT `id`, `object_id`, FROM_UNIXTIME(`start_date`) AS `start_date`, FROM_UNIXTIME(`end_date`) AS `end_date`, `params` FROM `date_items`;
