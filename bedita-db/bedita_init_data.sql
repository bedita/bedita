--
-- Basic initialization data
--

INSERT INTO object_types (id, name, module) VALUES
(1, 'area', 'areas'),
(2, 'newsletter', NULL),
(3, 'section', 'areas'),

(4, 'questionnaire', NULL),
(5, 'faq', NULL),
(29, 'gallery', 'galleries'),
(6, 'cartigli', NULL),

(7, 'scroll', NULL),
(8, 'timeline', NULL),
(9, 'community', NULL),

(10, 'befile', 'attachments'),
(12, 'image', 'multimedia'),

(13, 'comment', 'comments'),
(14, 'faqquestion', NULL),
(15, 'question', NULL),

(16, 'answer', NULL),
(17, 'objectuser', NULL),
(18, 'shortnews', 'news'),

(19, 'bibliography', NULL),
(20, 'book', NULL),
(21, 'event', 'events'),

(22, 'document', 'documents'),
(23, 'documentptrobject', NULL),
(24, 'documentptrextern', NULL),

(25, 'documentptrfile', NULL),
(26, 'documentptrservice', NULL),
(27, 'documentrule', NULL),

(28, 'author', NULL),
(30, 'biblioitem', NULL),

(31, 'audio', 'multimedia'),
(32, 'video', 'multimedia'),

(33, 'link', NULL),

(34, 'address', 'addressbook');
	



INSERT INTO `question_types` (`id`, `label`) VALUES
(1, 'multiple choice'),
(2, 'single choice'),
(3, 'text'),
(4, 'checkOpen'),
(5, 'degree'),
(6, 'simple text');

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
('tags', '#888888', 'tags', 'on'),
('comments', '#F08080', 'comments', 'on'),
('translations', '#FF00FF', 'translations', 'on'),
('books', NULL, 'books', 'on'),
('bibliographies', NULL, 'bibliographies', 'on'),
('addressbook', NULL, 'addressbook', 'on'),
('newsletter', NULL, 'newsletter', 'on');



-- administrator permissions
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'admin'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3');

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'areas'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'documents'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'galleries'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'multimedia'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'attachments'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'news'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'events'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'tags'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'comments'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'translations'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'books'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'bibliographies'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'addressbook'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'newsletter'), (SELECT id FROM groups WHERE name = 'administrator'), 'group', '3' );



-- editor perms
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'areas'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'documents'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'galleries'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'multimedia'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'attachments'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'news'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'events'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'tags'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'comments'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'translations'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'books'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'bibliographies'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'addressbook'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'newsletter'), (SELECT id FROM groups WHERE name = 'editor'), 'group', '3' );

-- reader perms
INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'areas'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'documents'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'galleries'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'multimedia'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'attachments'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'news'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'events'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'tags'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'comments'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'translations'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'books'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'bibliographies'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'addressbook'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES ((SELECT id FROM modules WHERE label = 'newsletter'), (SELECT id FROM groups WHERE name = 'reader'), 'group', '1' );

