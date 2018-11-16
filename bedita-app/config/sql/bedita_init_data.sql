--
-- Basic initialization data
--

INSERT INTO object_types (id, name, module_name) VALUES (1, 'area', 'areas');
INSERT INTO object_types (id, name, module_name) VALUES (3, 'section', 'areas');
INSERT INTO object_types (id, name, module_name) VALUES (10, 'b_e_file', 'multimedia');
INSERT INTO object_types (id, name, module_name) VALUES (12, 'image', 'multimedia');
INSERT INTO object_types (id, name, module_name) VALUES (30, 'application', 'multimedia');
INSERT INTO object_types (id, name, module_name) VALUES (31, 'audio', 'multimedia');
INSERT INTO object_types (id, name, module_name) VALUES (32, 'video', 'multimedia');
INSERT INTO object_types (id, name, module_name) VALUES (29, 'gallery', 'galleries');
INSERT INTO object_types (id, name, module_name) VALUES (13, 'comment', 'comments');
INSERT INTO object_types (id, name, module_name) VALUES (18, 'short_news', 'news');
INSERT INTO object_types (id, name, module_name) VALUES (21, 'event', 'events');
INSERT INTO object_types (id, name, module_name) VALUES (22, 'document', 'documents');
INSERT INTO object_types (id, name, module_name) VALUES (33, 'link', 'webmarks');
INSERT INTO object_types (id, name, module_name) VALUES (34, 'card', 'addressbook');
INSERT INTO object_types (id, name, module_name) VALUES (35, 'mail_message', 'newsletter');
INSERT INTO object_types (id, name, module_name) VALUES (36, 'mail_template', 'newsletter');
INSERT INTO object_types (id, name, module_name) VALUES (39, 'editor_note', NULL);
INSERT INTO object_types (id, name, module_name) VALUES (51, 'caption', 'multimedia');

-- ----------------------------------
-- default user and groups
-- ----------------------------------
INSERT INTO users (userid , realname , passwd ) VALUES ('bedita', 'BEdita', MD5( 'bedita' ));

INSERT INTO groups ( name, backend_auth, immutable ) VALUES ('administrator', true, true);
INSERT INTO groups ( name, backend_auth, immutable ) VALUES ('manager', true, false);
INSERT INTO groups ( name, backend_auth, immutable ) VALUES ('editor', true, false);
INSERT INTO groups ( name, backend_auth, immutable ) VALUES ('reader', true, false);
INSERT INTO groups ( name, backend_auth, immutable ) VALUES ('translator', true, false);
INSERT INTO groups ( name, backend_auth, immutable ) VALUES ('frontend', false, false);

INSERT INTO groups_users ( user_id , group_id ) VALUES (1, 1);

-- ---------------------------
-- module data
-- ---------------------------
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('1','publications', 'areas', 'areas', 'on', '1');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('2','admin', 'admin', 'admin', 'on', '15');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('3','translations', 'translations', 'translations', 'on', '8');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('4','users', 'users', 'users', 'on', '14');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('6','documents', 'documents', 'documents', 'on', '2');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('7','news', 'news', 'news', 'on', '9');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('8','galleries', 'galleries', 'galleries', 'on', '5');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('10','events', 'events', 'events', 'on', '3');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('12','webmarks', 'webmarks', 'webmarks', 'on', '12');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('16','addressbook', 'addressbook', 'addressbook', 'on', '10');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('18','newsletter', 'newsletter', 'newsletter', 'on', '11');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('23','statistics', 'statistics', 'statistics', 'on', '16');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('24','tags', 'tags', 'tags', 'on', '6');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('25','comments', 'comments', 'comments', 'on', '7');
INSERT INTO modules (id,label, name, url, status, priority) VALUES ('26','multimedia', 'multimedia', 'multimedia', 'on', '4');


-- --------------------------------------
-- default empty frontend/publication
-- --------------------------------------

INSERT INTO objects (object_type_id, status, title, nickname, lang, ip_created, user_created, user_modified) 
VALUES (1, 'on', 'Publication', 'publication', 'eng', '127.0.0.1', 1, 1);
INSERT INTO areas (id, public_name) 
VALUES (1, 'bedita publication');
INSERT INTO sections (id, syndicate, priority_order)
VALUES (1, 'off', 'asc');
INSERT INTO trees (id, area_id, parent_id, object_path, parent_path, priority, menu) VALUES
(1, 1, NULL, '/1', '/', 1, 1);

-- --------------------------------------
-- default empty section (used as home page)
-- --------------------------------------
INSERT INTO objects (object_type_id, status, title, nickname, description, lang, ip_created, user_created, user_modified)
VALUES (3, 'on', 'Section One', 'section-one', 'This is a section example. Remember that the first active section is loaded as home page by default. If any section is found then the publication is used as home page. Edit, move or delete this section as you wish.', 'eng', '127.0.0.1', 1, 1);
INSERT INTO sections (id, syndicate, priority_order)
VALUES (2, 'off', 'asc');
INSERT INTO trees (id, area_id, parent_id, object_path, parent_path, priority, menu) VALUES
(2, 1, 1, '/1/2', '/1', 1, 1);

-- --------------------------------------
-- administrator permissions
-- --------------------------------------

-- module areas
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (1, 1, 'group', '3' );

-- module admin
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (2, 1, 'group', '3');

-- module translations
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (3, 1, 'group', '3' );

-- module documents
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (6, 1, 'group', '3' );

-- module news
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (7, 1, 'group', '3' );

-- module galleries
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (8, 1, 'group', '3' );

-- module events
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (10, 1, 'group', '3' );

-- module webmarks
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (12, 1, 'group', '3' );

-- module addressbook
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (16, 1, 'group', '3' );

-- module newsletter
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (18, 1, 'group', '3' );

-- module statistics
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (23, 1, 'group', '3' );

-- module tags
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (24, 1, 'group', '3' );

-- module comments
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (25, 1, 'group', '3' );

-- module multimedia
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (26, 1, 'group', '3' );

-- module users
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (4, 1, 'group', '3');

-- --------------------------------------
-- manager permissions
-- --------------------------------------

-- module areas
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (1, 2, 'group', '3' );

-- module translations
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (3, 2, 'group', '3' );

-- module documents
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (6, 2, 'group', '3' );

-- module news
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (7, 2, 'group', '3' );

-- module galleries
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (8, 2, 'group', '3' );

-- module events
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (10, 2, 'group', '3' );

-- module webmarks
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (12, 2, 'group', '3' );

-- module addressbook
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (16, 2, 'group', '3' );

-- module newsletter
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (18, 2, 'group', '3' );

-- module statistics
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (23, 2, 'group', '3' );

-- module tags
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (24, 2, 'group', '3' );

-- module comments
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (25, 2, 'group', '3' );

-- module multimedia
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (26, 2, 'group', '3' );

-- module users
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (4, 2, 'group', '3');

-- --------------------------------------
-- editor perms
-- --------------------------------------

-- module areas
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (1, 3, 'group', '3' );

-- module translations
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (3, 3, 'group', '3' );

-- module documents
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (6, 3, 'group', '3' );

-- module news
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (7, 3, 'group', '3' );

-- module galleries
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (8, 3, 'group', '3' );

-- module events
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (10, 3, 'group', '3' );

-- module webmarks
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (12, 3, 'group', '3' );

-- module addressbook
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (16, 3, 'group', '3' );

-- module newsletter
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (18, 3, 'group', '3' );

-- module statistics
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (23, 3, 'group', '3' );

-- module tags
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (24, 3, 'group', '3' );

-- module comments
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (25, 3, 'group', '3' );

-- module multimedia
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (26, 3, 'group', '3' );


-- --------------------------------------
-- reader perms
-- --------------------------------------

-- module areas
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (1, 4, 'group', '1' );

-- module translations
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (3, 4, 'group', '1' );

-- module documents
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (6, 4, 'group', '1' );

-- module news
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (7, 4, 'group', '1' );

-- module galleries
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (8, 4, 'group', '1' );

-- module events
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (10, 4, 'group', '1' );

-- module webmarks
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (12, 4, 'group', '1' );

-- module addressbook
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (16, 4, 'group', '1' );

-- module newsletter
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (18, 4, 'group', '1' );

-- module statistics
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (23, 4, 'group', '1' );

-- module tags
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (24, 4, 'group', '1' );

-- module comments
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (25, 4, 'group', '1' );

-- module multimedia
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (26, 4, 'group', '1' );


-- --------------------------------------
-- translator perms
-- --------------------------------------

-- module translations
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (3, 5, 'group', '3' );