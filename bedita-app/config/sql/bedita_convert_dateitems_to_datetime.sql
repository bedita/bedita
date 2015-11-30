-- PLEASE, ENSURE YOU HAVE A BACKUP OF YOUR DATA AVAILABLE.
-- THIS OPERATION MIGHT TAKE A LONG TIME FOR LARGE DATABASES.

-- Add new columns.
ALTER TABLE `date_items`
    ADD `start_date_tmp` DATETIME NULL DEFAULT NULL COMMENT 'start date, can be NULL' AFTER `end_date`,
    ADD `end_date_tmp` DATETIME NULL DEFAULT NULL COMMENT 'end date, can be NULL' AFTER `start_date_tmp`;

----------------------------------
--                              --
-- RUN CONVERSION SCRIPT NOW!!! --
--                              --
----------------------------------

-- Once you have run the conversion script, uncomment the following queries and execute them to complete conversion.

-- -- Drop old columns.
-- ALTER TABLE `date_items`
--     DROP `start_date`,
--     DROP `end_date`;

-- -- Rename new columns.
-- ALTER TABLE `date_items`
--     CHANGE `start_date_tmp` `start_date` DATETIME NULL DEFAULT NULL COMMENT 'start date, can be NULL',
--     CHANGE `end_date_tmp` `end_date` DATETIME NULL DEFAULT NULL COMMENT 'end date, can be NULL';
