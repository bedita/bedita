--
-- Basic initialization data
--

INSERT INTO object_types (id, name, module) VALUES
(1, 'area', 'areas'),
(3, 'section', 'areas'),
(10, 'b_e_file', 'multimedia'),
(12, 'image', 'multimedia'),
(30, 'application', 'multimedia'),
(31, 'audio', 'multimedia'),
(32, 'video', 'multimedia'),
(29, 'gallery', 'galleries'),
(13, 'comment', 'comments'),
(18, 'short_news', 'news'),
(20, 'book', 'books'),
(21, 'event', 'events'),
(22, 'document', 'documents'),
(33, 'link', 'webmarks'),
(34, 'card', 'addressbook'),
(35, 'mail_message', 'newsletter'),
(36, 'mail_template', 'newsletter'),
(37, 'author', NULL),
(39, 'editor_note', NULL),
(40, 'question', 'questionnaires'),
(41, 'questionnaire', 'questionnaires'),
(42, 'questionnaire_result', 'questionnaires');

-- ----------------------------------
-- default user and groups
-- ----------------------------------
INSERT INTO `users` ( id, `userid` , `realname` , `passwd` ) VALUES (1, 'bedita', 'BEdita', MD5( 'bedita' ));

INSERT INTO `groups` ( `name`, `backend_auth`, `immutable` ) VALUES 
('administrator', 1, 1),
('editor', 1, 0),
('reader', 1, 0),
('frontend', 0, 0),
('translator', 1, 1);

INSERT INTO `groups_users` ( `user_id` , `group_id` ) VALUES (1, (SELECT id FROM groups WHERE name = 'administrator'));

-- ---------------------------
-- module data
-- ---------------------------
INSERT INTO `modules` (`id`,`label`, `name`, `path`, `status`, `priority`) VALUES
('1','publishing', 'areas', 'areas', 'on', '1'),
('2','admin', 'admin', 'admin', 'on', '15'),
('3','translations', 'translations', 'translations', 'on', '8'),
('6','documents', 'documents', 'documents', 'on', '2'),
('7','news', 'news', 'news', 'on', '9'),
('8','galleries', 'galleries', 'galleries', 'on', '5'),
('10','events', 'events', 'events', 'on', '3'),
('12','webmarks', 'webmarks', 'webmarks', 'on', '12'),
('13','books', 'books', 'books', 'on', '13'),
('14', 'questionnaires', 'questionnaires', 'questionnaires', 'on', '15'),
('16','addressbook', 'addressbook', 'addressbook', 'on', '10'),
('18','newsletter', 'newsletter', 'newsletter', 'on', '11'),
('23','statistics', 'statistics', 'statistics', 'on', '16'),
('24','tags', 'tags', 'tags', 'on', '6'),
('25','comments', 'comments', 'comments', 'on', '7'),
('26','multimedia', 'multimedia', 'multimedia', 'on', '4');


-- --------------------------------------
-- default empty frontend/publishing
-- --------------------------------------

INSERT INTO `objects` (`id`, `object_type_id`, `status`, `title`, `nickname`, `lang`, `ip_created`, `user_created`, `user_modified`) 
VALUES (1, 1, 'on', 'Publication', 'publication', 'eng', '127.0.0.1', 1, 1);
INSERT INTO `areas` (`id`, `public_name`) 
VALUES (1, 'bedita publication');
INSERT INTO `sections` (`id`, `syndicate`, `priority_order`)
VALUES (1, 'off', 'asc');
INSERT INTO `trees` (`id`, `parent_id`, `path`, `parent_path`, `priority`) VALUES
(1, NULL, '/1', '/', 1);

-- administrator permissions
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'admin'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3');

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'areas'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'documents'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'galleries'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'multimedia'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'news'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'events'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'tags'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'comments'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'translations'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'books'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'addressbook'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'newsletter'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'statistics'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'webmarks'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'questionnaires'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );


-- editor perms
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'areas'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'documents'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'galleries'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'multimedia'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'news'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'events'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'tags'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'comments'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'translations'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'books'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'addressbook'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'newsletter'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'statistics'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'webmarks'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'questionnaires'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

-- reader perms
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'areas'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'documents'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'galleries'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'multimedia'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'news'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'events'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'tags'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'comments'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'translations'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'books'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'addressbook'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'newsletter'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'statistics'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'webmarks'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'questionnaires'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

-- translator perms
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'translations'), (SELECT id FROM groups WHERE name = 'translator'), 'group', '3' );
