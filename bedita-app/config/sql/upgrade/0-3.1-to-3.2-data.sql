INSERT INTO groups ( name, backend_auth, immutable ) VALUES ('manager', true, true);

INSERT INTO modules (id,label, name, url, status, priority) VALUES ('4','users', 'users', 'users', 'on', '14');

-- --------------------------------------
-- new administrator permissions
-- --------------------------------------

-- module users
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (4, (SELECT id FROM groups WHERE name='administrator'), 'group', '3');


-- --------------------------------------
-- manager permissions
-- --------------------------------------

-- module areas
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (1, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module translations
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (3, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module documents
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (6, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module news
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (7, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module galleries
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (8, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module events
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (10, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module webmarks
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (12, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module addressbook
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (16, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module newsletter
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (18, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module statistics
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (23, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module tags
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (24, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module comments
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (25, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module multimedia
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (26, (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module users
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES (4, (SELECT id FROM groups WHERE name='manager'), 'group', '3');