INSERT INTO groups (name, backend_auth, immutable ) VALUES ('manager', true, true);

INSERT INTO modules (label, name, url, status, priority) VALUES ('users', 'users', 'users', 'on', '14');

-- --------------------------------------
-- new administrator permissions
-- --------------------------------------

-- module users
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='users'), (SELECT id FROM groups WHERE name='administrator'), 'group', '3');

-- --------------------------------------
-- defaults tree.menu field
-- set menu = 0 for contents and  = 1 for sections if NULL
-- --------------------------------------

UPDATE trees SET menu = 0 where menu is NULL AND EXISTS 
(SELECT id FROM objects WHERE objects.id = trees.id AND objects.object_type_id NOT IN (1,3));

UPDATE trees SET menu = 1 where menu is NULL AND EXISTS 
(SELECT id FROM objects WHERE objects.id = trees.id AND objects.object_type_id IN (1,3));

-- --------------------------------------
-- manager permissions
-- --------------------------------------

-- module areas
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='areas'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module translations
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='translations'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module documents
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='documents'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module news
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='news'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module galleries
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='galleries'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module events
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='events'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module webmarks
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='webmarks'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module addressbook
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='addressbook'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module newsletter
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='newsletter'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module statistics
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='statistics'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module tags
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='tags'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module comments
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='comments'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module multimedia
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='multimedia'), (SELECT id FROM groups WHERE name='manager'), 'group', '3' );

-- module users
INSERT INTO permission_modules ( module_id , ugid , switch , flag )
VALUES ((SELECT id FROM modules WHERE name='users'), (SELECT id FROM groups WHERE name='manager'), 'group', '3');