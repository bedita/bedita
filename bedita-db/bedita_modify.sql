ALTER TABLE `base_documents` ADD `gallery_id` INT NULL ,
ADD `question_id` INT NULL ;

-- Aggiunta modulo
INSERT INTO `modules` (`label`, `color`, `path`, `status`) VALUES ('attachments', '#ff34aa', 'attachments', 'on') ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES (
(SELECT id FROM modules WHERE label = 'attachment'),
(SELECT id FROM groups WHERE name = 'administrator'),
'group', '15'
) ;

DROP VIEW IF EXISTS `view_files`;

CREATE  VIEW `view_files` AS 
SELECT 
streams.*, objects.title, objects.status, objects.object_type_id
FROM 
files INNER JOIN streams ON files.id = streams.id
INNER JOIN objects ON files.id = objects.id ;
