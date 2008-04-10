--
-- Basic initialization data
--

INSERT INTO object_types (id, name) VALUES
(1, 'area'),
(2, 'newsletter'),
(3, 'section'),

(4, 'questionnaire'),
(5, 'faq'),
(29, 'gallery'),
(6, 'cartigli'),

(7, 'scroll'),
(8, 'timeline'),
(9, 'community'),

(10, 'befile'),
(12, 'image'),

(13, 'comment'),
(14, 'faqquestion'),
(15, 'question'),

(16, 'answer'),
(17, 'objectuser'),
(18, 'shortnews'),

(19, 'bibliography'),
(20, 'book'),
(21, 'event'),

(22, 'document'),
(23, 'documentptrobject'),
(24, 'documentptrextern'),

(25, 'documentptrfile'),
(26, 'documentptrservice'),
(27, 'documentrule'),

(28, 'author'),
(30, 'biblioitem'),

(31, 'audio'),
(32, 'video'),

(33, 'link')
;

INSERT INTO `question_types` (`id`, `label`) VALUES
(1, 'multiple choice'),
(2, 'single choice'),
(3, 'text'),
(4, 'checkOpen'),
(5, 'degree'),
(6, 'simple text')
;

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
INSERT INTO `modules` (`label`, `color`, `path`, `status`) VALUES
('admin', '#000000', 'admin', 'on'),
('areas', '#ff9933', 'areas', 'on'),
('documents', '#ff6600', 'documents', 'on'),
('galleries', '#ffcc33', 'galleries', 'on'),
('multimedia', '#ff3456', 'multimedia', 'on'),
('attachments', '#ff34aa', 'attachments', 'on'),
('news', '#cc00ff', 'news', 'on'),
('events', '#3399CC', 'events', 'on'),
('tags', '#888888', 'tags', 'on');

-- administrator permissions
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'admin'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3') ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'areas'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'documents'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'galleries'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'multimedia'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'attachments'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'news'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'events'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'tags'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' ) ;

-- editor perms
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'areas'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'documents'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'galleries'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'multimedia'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'attachments'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'news'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'events'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'tags'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' ) ;

-- reader perms
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'areas'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'documents'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'galleries'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'multimedia'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'attachments'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'news'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'events'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' ) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'tags'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' ) ;

