--
-- Basic initialization data
--

INSERT INTO object_types (id, name, module) VALUES
(1, 'area', 'areas'),
(3, 'section', 'areas'),
(10, 'befile', 'multimedia'),
(12, 'image', 'multimedia'),
(31, 'audio', 'multimedia'),
(32, 'video', 'multimedia'),
(29, 'gallery', 'galleries'),
(13, 'comment', 'comments'),
(18, 'shortnews', 'news'),
(19, 'bibliography', 'bibliographies'),
(20, 'book', 'books'),
(21, 'event', 'events'),
(22, 'document', 'documents'),
(33, 'link', NULL),
(35, 'mailmessage', 'newsletter'),
(36, 'mailtemplate', 'newsletter'),
(37, 'author', NULL),
(38, 'biblioitem', NULL),
(34, 'card', 'addressbook');

-- ----------------------------------
-- default user and groups
-- ----------------------------------
INSERT INTO `users` ( id, `userid` , `realname` , `passwd` ) VALUES (1, 'bedita', 'BEdita', MD5( 'bedita' ));

INSERT INTO `groups` ( `name` ) VALUES ('administrator');
INSERT INTO `groups` ( `name` ) VALUES ('guest');
INSERT INTO `groups` ( `name` ) VALUES ('editor');
INSERT INTO `groups` ( `name` ) VALUES ('reader');
INSERT INTO `groups` ( `name` ) VALUES ('frontend');

INSERT INTO `groups_users` ( `user_id` , `group_id` ) VALUES (1, (SELECT id FROM groups WHERE name = 'administrator'));

-- ---------------------------
-- module data
-- ---------------------------
INSERT INTO `modules` (`id`,`label`, `name`, `path`, `status`) VALUES
('1','publishing', 'areas', 'areas', 'on'),
('2','admin', 'admin', 'admin', 'on'),
('3','translations', 'translations', 'translations', 'on'),
('6','documents', 'documents', 'documents', 'on'),
('7','news', 'news', 'news', 'on'),
('8','galleries', 'galleries', 'galleries', 'on'),
('10','events', 'events', 'events', 'on'),
('11','bibliographies', 'bibliographies', 'bibliographies', 'on'),
('12','webmarks', 'webmarks', 'webmarks', 'on'),
('13','books', 'books', 'books', 'on'),
('16','addressbook', 'addressbook', 'addressbook', 'on'),
('18','newsletter', 'newsletter', 'newsletter', 'on'),
('23','statistics', 'statistics', 'statistics', 'on'),
('24','tags', 'tags', 'tags', 'on'),
('25','comments', 'comments', 'comments', 'on'),
('26','multimedia', 'multimedia', 'multimedia', 'on');



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
VALUES ((SELECT id FROM modules WHERE name = 'bibliographies'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'addressbook'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'newsletter'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'statistics'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'webmarks'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );



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
VALUES ((SELECT id FROM modules WHERE name = 'bibliographies'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'addressbook'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'newsletter'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'statistics'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'webmarks'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );


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
VALUES ((SELECT id FROM modules WHERE name = 'bibliographies'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'addressbook'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'newsletter'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'statistics'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE name = 'webmarks'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

