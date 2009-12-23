-- update `name` field in `object_types` table
UPDATE `object_types` SET `name` = 'b_e_file' WHERE `object_types`.`id` =10;
UPDATE `object_types` SET `name` = 'short_news' WHERE `object_types`.`id` =18;
UPDATE `object_types` SET `name` = 'mail_message' WHERE `object_types`.`id` =35;
UPDATE `object_types` SET `name` = 'mail_template' WHERE `object_types`.`id` =36;
UPDATE `object_types` SET `name` = 'biblio_item' WHERE `object_types`.`id` =38;
UPDATE `object_types` SET `name` = 'editor_note' WHERE `object_types`.`id` =39;
UPDATE `object_types` SET `name` = 'questionnaire_result' WHERE `object_types`.`id` =42;