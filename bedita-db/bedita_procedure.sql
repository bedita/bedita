--- Procedure e funzioni per l abero
DROP PROCEDURE  IF EXISTS deleteTreeWithParent ;
delimiter //
CREATE PROCEDURE deleteTreeWithParent (_ID INT, _IDPARENT INT)
DETERMINISTIC
BEGIN
DECLARE pathID MEDIUMTEXT DEFAULT '' ;
DECLARE pathDel MEDIUMTEXT DEFAULT '' ;

DECLARE done INT DEFAULT 0;
DECLARE curs CURSOR FOR SELECT path FROM trees WHERE id = _ID AND parent_id = _IDPARENT ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

OPEN curs;

REPEAT
	FETCH curs INTO pathID ;
	IF NOT done THEN
		SET pathDel  = IF(pathID IS NULL, '', CONCAT(pathID, '%')) ;
		DELETE FROM trees WHERE path LIKE  pathDel ;
	END IF;
UNTIL done END REPEAT;

END
//
delimiter ;

DROP PROCEDURE  IF EXISTS deleteTree ;
delimiter //
CREATE PROCEDURE deleteTree (_ID INT)
DETERMINISTIC
BEGIN
DECLARE pathID MEDIUMTEXT DEFAULT '' ;
DECLARE pathDel MEDIUMTEXT DEFAULT '' ;

DECLARE done INT DEFAULT 0;
DECLARE curs CURSOR FOR SELECT path FROM trees WHERE id = _ID ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

OPEN curs;

REPEAT
	FETCH curs INTO pathID ;
	IF NOT done THEN
		SET pathDel  = IF(pathID IS NULL, '', CONCAT(pathID, '%')) ;
		DELETE FROM trees WHERE path LIKE  pathDel ;
	END IF;
UNTIL done END REPEAT;

END
//
delimiter ;












DROP PROCEDURE  IF EXISTS appendChildTree ;
delimiter //
CREATE PROCEDURE appendChildTree (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE pathParent MEDIUMTEXT DEFAULT '' ;
DECLARE pathID MEDIUMTEXT DEFAULT '' ;
DECLARE _priority INT  ;

SET pathParent  = (SELECT path FROM trees WHERE id = _IDParent) ;
SET pathID  	= IF(pathParent IS NULL, CONCAT('/', _ID), CONCAT(pathParent, '/', _ID)) ;
SET pathParent 	= IF(pathParent IS NULL, '/', pathParent) ;
SET _priority  	= (SELECT (MAX(priority)+1) FROM trees WHERE parent_id = _IDParent) ;
SET _priority  	= IF(_priority IS NULL, 1, _priority) ;

INSERT INTO `trees` ( `id` , `parent_id` , `path` , `parent_path` , `priority` ) VALUES (_ID, _IDParent , pathID, pathParent , _priority) ;

END
//
delimiter ;


DROP PROCEDURE  IF EXISTS moveChildTreeUp ;
delimiter //
CREATE PROCEDURE moveChildTreeUp (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE _priority INT ;
DECLARE _minPriority INT ;
DECLARE _pathParent MEDIUMTEXT ;

SET _pathParent  	= (SELECT path FROM trees WHERE id = _IDParent) ;
SET _priority  	 	= (SELECT priority FROM trees WHERE id = _ID AND parent_id) ;
SET _minPriority  	= (SELECT MIN(priority) FROM trees WHERE id = _ID AND parent_id) ;

IF  _priority > _minPriority THEN
	BEGIN
	 UPDATE trees SET priority = _priority WHERE parent_path = _pathParent AND priority = (_priority - 1) ;
	 UPDATE trees SET priority = (_priority - 1) WHERE id = _ID AND parent_id = _IDParent ;
	 END ;
END IF ;

END
//
delimiter ;


DROP PROCEDURE  IF EXISTS moveChildTreeDown ;
delimiter //
CREATE PROCEDURE moveChildTreeDown (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE _priority INT ;
DECLARE _maxPriority INT ;
DECLARE _pathParent MEDIUMTEXT ;

SET _pathParent  	= (SELECT path FROM trees WHERE id = _IDParent) ;
SET _priority  	 	= (SELECT priority FROM trees WHERE id = _ID AND parent_id) ;
SET _maxPriority  	= (SELECT MAX(priority) FROM trees WHERE id = _ID AND parent_id) ;

IF  _priority < _maxPriority THEN
	BEGIN
	 UPDATE trees SET priority = _priority WHERE parent_path = _pathParent AND priority = (_priority + 1) ;
	 UPDATE trees SET priority = (_priority + 1) WHERE id = _ID AND parent_id = _IDParent ;
	 END ;
END IF ;

END
//
delimiter ;


DROP PROCEDURE  IF EXISTS moveChildTreeFirst ;
delimiter //
CREATE PROCEDURE moveChildTreeFirst (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE _priority INT ;
DECLARE _idCurr INT ;
DECLARE curs CURSOR FOR SELECT id, priority FROM trees WHERE parent_id = _IDParent ORDER BY priority ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

OPEN curs;

REPEAT
	FETCH curs INTO _idCurr, _priority ;

	IF NOT done THEN
		IF _idCurr = _ID THEN
			UPDATE trees SET priority = 1 WHERE id = _ID AND parent_id = _IDParent ;
			SET done = 1 ;
		ELSE
			UPDATE trees SET priority = (_priority+1) WHERE id = _idCurr AND parent_id = _IDParent ;
		END IF ;
	END IF;
UNTIL done END REPEAT;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS moveChildTreeLast ;
delimiter //
CREATE PROCEDURE moveChildTreeLast (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE _priority INT ;
DECLARE _maxPriority INT ;
DECLARE _idCurr INT ;
DECLARE curs CURSOR FOR SELECT id, priority FROM trees WHERE parent_id = _IDParent ORDER BY priority DESC ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SET _maxPriority  	= (SELECT MAX(priority) FROM trees WHERE id = _ID AND parent_id) ;

OPEN curs;

REPEAT
	FETCH curs INTO _idCurr, _priority ;

	IF NOT done THEN
		IF _idCurr = _ID THEN
			UPDATE trees SET priority = _maxPriority WHERE id = _ID AND parent_id = _IDParent ;
			SET done = 1 ;
		ELSE
			UPDATE trees SET priority = (_priority-1) WHERE id = _idCurr AND parent_id = _IDParent ;
		END IF ;
	END IF;
UNTIL done END REPEAT;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS switchChildTree ;
delimiter //
CREATE PROCEDURE switchChildTree (_ID INT, _IDParent INT, _PRIOR INT)
DETERMINISTIC
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE _priority INT ;
DECLARE _old_priority INT ;


DECLARE _maxPriority INT ;

SET _maxPriority  	= (SELECT MAX(priority) FROM trees WHERE parent_id = _IDParent) ;
SET _priority		= IF(_PRIOR > _maxPriority, _maxPriority, _PRIOR) ;
SET _old_priority	= (SELECT priority FROM trees WHERE id = _ID AND parent_id = _IDParent) ;

UPDATE trees SET priority = _old_priority
WHERE parent_id = _IDParent  AND priority = _priority ;

UPDATE trees SET priority = _priority  WHERE id = _ID AND parent_id = _IDParent ;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS moveTree ;
delimiter //
CREATE PROCEDURE moveTree (_ID INT, _IDOldParent INT, _IDNewParent INT)
DETERMINISTIC
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE _oldPath MEDIUMTEXT ;
DECLARE _newPath MEDIUMTEXT ;
DECLARE _oldPathParent MEDIUMTEXT ;
DECLARE _newPathParent MEDIUMTEXT ;

SET _oldPath 		= (SELECT path FROM trees WHERE id = _ID AND parent_id = _IDOldParent) ;
SET _oldPathParent 	= (SELECT parent_path FROM trees WHERE id = _ID AND parent_id = _IDOldParent) ;
SET _newPathParent 	= (SELECT path FROM trees WHERE id = _IDNewParent) ;
SET _newPath		= REPLACE(_oldPath, _oldPathParent, _newPathParent) ;

UPDATE trees SET path = _newPath, parent_path = _newPathParent, parent_id = _IDNewParent WHERE path LIKE _oldPath ;

UPDATE trees SET
path = REPLACE(path, _oldPath, _newPath) , parent_path = REPLACE(parent_path, _oldPath, _newPath)
WHERE path  LIKE CONCAT(_oldPath, '%') ;

END
//
delimiter ;

DROP FUNCTION  IF EXISTS isParentTree ;
delimiter //
CREATE FUNCTION isParentTree (_IDParent INT, _IDChild INT) RETURNS INT
DETERMINISTIC
BEGIN
DECLARE _pathParent MEDIUMTEXT ;
DECLARE ret INT ;

SET _pathParent = (SELECT path FROM trees WHERE id = _IDParent) ;
SET ret = IF((SELECT id FROM trees WHERE path LIKE CONCAT(_pathParent, '%') AND id = _IDChild) IS NULL, 0, 1) ;

RETURN ret ;

END
//
delimiter ;

DROP PROCEDURE  IF EXISTS cloneTree ;
delimiter //
CREATE PROCEDURE cloneTree (_ID INT, _IDOLD INT)
DETERMINISTIC
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE _idparent INT ;
DECLARE curs CURSOR FOR SELECT parent_id FROM trees WHERE path  LIKE CONCAT('%/', _IDOLD) ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

-- clona gli oggetti foglia
OPEN curs;
REPEAT
	FETCH curs INTO _idparent ;
	IF NOT done THEN
	 	CALL appendChildTree(_ID, _idparent) ;
	END IF;
UNTIL done END REPEAT;

-- clona le ramificazioni
INSERT INTO trees
SELECT
id,
_ID AS parent_id,
REPLACE(path, _IDOLD, _ID) AS path,
REPLACE(parent_path, _IDOLD, _ID) AS parent_path,
priority
FROM trees
WHERE
path LIKE CONCAT('%/', _IDOLD, '/%') ;

END
//
delimiter ;

-- ---------------------------------------------------
-- ---------------------------------------------------
-- ---------------------------------------------------

DROP PROCEDURE  IF EXISTS appendChildBibliography ;
delimiter //
CREATE PROCEDURE appendChildBibliography (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE _priority INT ;

SET _priority  	= (SELECT (MAX(priority)+1) FROM content_bases_objects WHERE object_id = _IDParent AND switch = 'BIBLIOS') ;
SET _priority	= IF(_priority IS NULL, 1, _priority) ;

INSERT INTO `content_bases_objects` ( `id` , `object_id` , `switch` , `priority` ) VALUES (_ID, _IDParent , 'BIBLIOS', _priority) ;

END
//
delimiter ;

DROP PROCEDURE  IF EXISTS removeAllChildrenBibliography ;
delimiter //
CREATE PROCEDURE removeAllChildrenBibliography (_IDParent INT)
DETERMINISTIC
BEGIN

DELETE FROM content_bases_objects WHERE `object_id` = _IDParent AND switch = 'BIBLIOS' ;

END
//
delimiter ;


DROP PROCEDURE  IF EXISTS moveChildBibliographyUp ;
delimiter //
CREATE PROCEDURE moveChildBibliographyUp (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE _priority INT ;
DECLARE _minPriority INT ;

SET _priority  	 	= (SELECT priority FROM content_bases_objects  WHERE object_id = _IDParent AND id = _ID AND switch = 'BIBLIOS') ;
SET _minPriority  	= (SELECT MIN(priority) FROM content_bases_objects WHERE object_id = _IDParent AND switch = 'BIBLIOS') ;

IF  _priority > _minPriority THEN
	BEGIN
	 UPDATE content_bases_objects SET priority = _priority WHERE object_id = _IDParent AND switch = 'BIBLIOS' AND priority = (_priority - 1) ;
	 UPDATE content_bases_objects SET priority = (_priority - 1) WHERE object_id = _IDParent AND id = _ID AND switch = 'BIBLIOS' ;
	 END ;
END IF ;

END
//
delimiter ;


DROP PROCEDURE  IF EXISTS moveChildBibliographyDown ;
delimiter //
CREATE PROCEDURE moveChildBibliographyDown (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE _priority INT ;
DECLARE _maxPriority INT ;

SET _priority  	 	= (SELECT priority FROM content_bases_objects  WHERE object_id = _IDParent AND id = _ID AND switch = 'BIBLIOS') ;
SET _maxPriority  	= (SELECT MAX(priority) FROM content_bases_objects WHERE object_id = _IDParent AND switch = 'BIBLIOS') ;

IF  _priority < _maxPriority THEN
	BEGIN
	 UPDATE content_bases_objects SET priority = _priority WHERE object_id = _IDParent AND switch = 'BIBLIOS' AND priority = (_priority + 1) ;
	 UPDATE content_bases_objects SET priority = (_priority + 1) WHERE object_id = _IDParent AND id = _ID AND switch = 'BIBLIOS' ;
	END ;
END IF ;

END
//
delimiter ;


DROP PROCEDURE  IF EXISTS moveChildBibliographyFirst ;
delimiter //
CREATE PROCEDURE moveChildBibliographyFirst (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE _priority INT ;
DECLARE _idCurr INT ;
DECLARE curs CURSOR FOR SELECT id, priority FROM content_bases_objects WHERE object_id = _IDParent AND switch = 'BIBLIOS' ORDER BY priority ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

OPEN curs;

REPEAT
	FETCH curs INTO _idCurr, _priority ;

	IF NOT done THEN
		IF _idCurr = _ID THEN
			UPDATE content_bases_objects SET priority = 1 WHERE object_id = _IDParent AND id = _ID AND switch = 'BIBLIOS' ;
			SET done = 1 ;
		ELSE
			UPDATE content_bases_objects SET priority = (_priority+1) WHERE object_id = _IDParent AND id = _ID AND switch = 'BIBLIOS' ;
		END IF ;
	END IF;
UNTIL done END REPEAT;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS moveChildBibliographyLast ;
delimiter //
CREATE PROCEDURE moveChildBibliographyLast (_ID INT, _IDParent INT)
DETERMINISTIC
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE _priority INT ;
DECLARE _maxPriority INT ;
DECLARE _idCurr INT ;
DECLARE curs CURSOR FOR SELECT id, priority FROM content_bases_objects WHERE object_id = _IDParent AND switch = 'BIBLIOS' ORDER BY priority DESC ;

DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SET _maxPriority  	= (SELECT MAX(priority) FROM content_bases_objects WHERE object_id = _IDParent AND switch = 'BIBLIOS' ORDER BY priority) ;

OPEN curs;

REPEAT
	FETCH curs INTO _idCurr, _priority ;

	IF NOT done THEN
		IF _idCurr = _ID THEN
			UPDATE content_bases_objects SET priority = _maxPriority WHERE object_id = _IDParent AND id = _ID AND switch = 'BIBLIOS'  ;
			SET done = 1 ;
		ELSE
			UPDATE content_bases_objects SET priority = (_priority-1) WHERE object_id = _IDParent AND id = _ID AND switch = 'BIBLIOS'  ;
		END IF ;
	END IF;
UNTIL done END REPEAT;
END
//
delimiter ;


-- ---------------------------------------------------
-- ---------------------------------------------------
-- ---------------------------------------------------

DROP PROCEDURE  IF EXISTS replacePermission ;
delimiter //
CREATE PROCEDURE replacePermission (_OBJID INT, _USERGROUP VARCHAR(255), _SWITCH VARCHAR(40), _FLAG INT)
DETERMINISTIC
BEGIN

DECLARE _UGID INT DEFAULT 0;
DECLARE _idprm INT DEFAULT 0;

SET _UGID 	= IF(_SWITCH = 'user', (SELECT ID FROM users WHERE userid = _USERGROUP), (SELECT ID FROM groups WHERE name = _USERGROUP)) ;

IF _UGID > 0 THEN
	SET _idprm	= (SELECT ID FROM permissions WHERE object_id = _OBJID AND ugid = _UGID AND switch = _SWITCH) ;

	IF _idprm > 0 THEN
		UPDATE permissions SET flag = _FLAG WHERE id = _idprm ;
	ELSE
		INSERT permissions (object_id, ugid, switch, flag) VALUES (_OBJID, _UGID, _SWITCH, _FLAG) ;
	END IF ;
END IF ;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS deletePermission ;
delimiter //
CREATE PROCEDURE deletePermission (_OBJID INT, _USERGROUP VARCHAR(255), _SWITCH VARCHAR(40))
DETERMINISTIC
BEGIN

DECLARE _UGID INT DEFAULT 0;
DECLARE _idprm INT DEFAULT 0;

SET _UGID 	= IF(_SWITCH = 'user', (SELECT ID FROM users WHERE userid = _USERGROUP), (SELECT ID FROM groups WHERE name = _USERGROUP)) ;

IF _UGID > 0 THEN
	DELETE FROM permissions WHERE object_id = _OBJID AND ugid = _UGID AND switch = _SWITCH ;
END IF ;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS replacePermissionTree ;
delimiter //
CREATE PROCEDURE replacePermissionTree (_OBJID INT, _USERGROUP VARCHAR(255), _SWITCH VARCHAR(40), _FLAG INT)
DETERMINISTIC
BEGIN

DECLARE _UGID INT DEFAULT 0;
DECLARE _idprm INT DEFAULT 0;

-- Per trovare gli ID di un ramo
DECLARE pathID MEDIUMTEXT DEFAULT '' ;
DECLARE pathSearch MEDIUMTEXT DEFAULT '' ;
DECLARE _idCurr INT ;
DECLARE done INT DEFAULT 0;
DECLARE curs CURSOR FOR SELECT id FROM trees WHERE path LIKE pathSearch ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SET _UGID 	= IF(_SWITCH = 'user', (SELECT ID FROM users WHERE userid = _USERGROUP), (SELECT ID FROM groups WHERE name = _USERGROUP)) ;

IF _UGID > 0 THEN
	-- Trova gli oggetti coinvolti
	SET pathID       = (SELECT path FROM trees WHERE id = _OBJID) ;
	SET pathSearch   = IF(pathID IS NULL, '', CONCAT(pathID, '%')) ;

	OPEN curs;
	REPEAT
		FETCH curs INTO _idCurr ;

		SET _idprm	= (SELECT ID FROM permissions WHERE object_id = _idCurr AND ugid = _UGID AND switch = _SWITCH) ;

		IF _idprm > 0 THEN
			UPDATE permissions SET flag = _FLAG WHERE id = _idprm ;
		ELSE
			INSERT permissions (object_id, ugid, switch, flag) VALUES (_idCurr, _UGID, _SWITCH, _FLAG) ;
		END IF ;

	UNTIL done END REPEAT;

END IF ;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS deletePermissionTree ;
delimiter //
CREATE PROCEDURE deletePermissionTree (_OBJID INT, _USERGROUP VARCHAR(255), _SWITCH VARCHAR(40))
DETERMINISTIC
BEGIN

DECLARE _UGID INT DEFAULT 0;
DECLARE _idprm INT DEFAULT 0;

-- Per trovare gli ID di un ramo
DECLARE pathID MEDIUMTEXT DEFAULT '' ;
DECLARE pathSearch MEDIUMTEXT DEFAULT '' ;
DECLARE _idCurr INT ;
DECLARE done INT DEFAULT 0;
DECLARE curs CURSOR FOR SELECT id FROM trees WHERE path LIKE pathSearch ;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SET _UGID 	= IF(_SWITCH = 'user', (SELECT ID FROM users WHERE userid = _USERGROUP), (SELECT ID FROM groups WHERE name = _USERGROUP)) ;

IF _UGID > 0 THEN
	-- Trova gli oggetti coinvolti
	SET pathID       = (SELECT path FROM trees WHERE id = _OBJID) ;
	SET pathSearch   = IF(pathID IS NULL, '', CONCAT(pathID, '%')) ;

	OPEN curs;
	REPEAT
		FETCH curs INTO _idCurr ;

		DELETE FROM permissions WHERE object_id = _idCurr AND ugid = _UGID AND switch = _SWITCH ;

	UNTIL done END REPEAT;

END IF ;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS deleteAllPermissionTree ;
delimiter //
CREATE PROCEDURE deleteAllPermissionTree (_OBJID INT)
DETERMINISTIC
BEGIN

DELETE FROM permissions WHERE object_id
IN
(SELECT id FROM trees WHERE path LIKE CONCAT((SELECT path FROM trees WHERE id = _OBJID), '%'))
;
END
//
delimiter ;

DROP FUNCTION  IF EXISTS prmsUserByID ;
delimiter //
CREATE FUNCTION prmsUserByID (_USERID VARCHAR(40), _IDOBJ VARCHAR(32), _PRMS INT) RETURNS INT
DETERMINISTIC
BEGIN
DECLARE prmsG INT DEFAULT 0 ;
DECLARE prmsU INT DEFAULT 0 ;

SET prmsG = (
	SELECT DISTINCT
	(permissions.flag & _PRMS) AS perms
	FROM
	permissions
	WHERE
	permissions.object_id = _IDOBJ
	AND
	(
	permissions.ugid IN
		(
		SELECT groups_users.`group_id`
		FROM
		users INNER JOIN groups_users ON users.id = groups_users.user_id
		WHERE users.userid = _USERID
		)
	OR

	permissions.ugid =
		(
		SELECT id FROM groups WHERE name = 'guest'
		)
	)
	AND
	permissions.switch = 'group'
	AND
	(permissions.flag & _PRMS)
	) ;
SET prmsG  = IF(prmsG IS NULL, 0, prmsG) ;

SET prmsU  = (
	SELECT DISTINCT
	(permissions.flag & _PRMS) AS perms
	FROM
	permissions
	WHERE
	permissions.ugid =
	(
	SELECT id FROM users WHERE userid = _USERID
	)
	AND
	permissions.switch = 'user'
	AND
	(permissions.flag & _PRMS)
	AND
	permissions.object_id = _IDOBJ
) ;
SET prmsU  = IF(prmsU IS NULL, 0, prmsU) ;


RETURN (prmsG|prmsU) ;

END
//
delimiter ;

DROP FUNCTION  IF EXISTS prmsGroupByName ;
delimiter //
CREATE FUNCTION prmsGroupByName (_GROUPNAME VARCHAR(40), _IDOBJ VARCHAR(32), _PRMS INT) RETURNS INT
DETERMINISTIC
BEGIN
DECLARE prmsG INT DEFAULT 0 ;

SET prmsG = (
	SELECT DISTINCT
	(permissions.flag & _PRMS) AS perms
	FROM
	permissions
	WHERE
	permissions.object_id = _IDOBJ
	AND
	(
	permissions.ugid =
		(
		SELECT id FROM groups WHERE name = _GROUPNAME
		)
	)
	AND
	permissions.switch = 'group'
	AND
	(permissions.flag & _PRMS)
	) ;
SET prmsG  = IF(prmsG IS NULL, 0, prmsG) ;

RETURN (prmsG) ;

END
//
delimiter ;

DROP PROCEDURE  IF EXISTS clonePermission ;
delimiter //
CREATE PROCEDURE clonePermission (_OBJID INT, _NEWID INT)
DETERMINISTIC
BEGIN

INSERT permissions (object_id, ugid, switch, flag) SELECT _NEWID, ugid, switch, flag FROM permissions WHERE object_id = _OBJID ;

END
//
delimiter ;


-- ---------------------------------------------------
-- Permessi sui moduli
-- ---------------------------------------------------

DROP PROCEDURE  IF EXISTS replacePermissionModule ;
delimiter //
CREATE PROCEDURE replacePermissionModule (_MDL VARCHAR(255), _USERGROUP VARCHAR(255), _SWITCH VARCHAR(40), _FLAG INT)
DETERMINISTIC
BEGIN

DECLARE _UGID INT DEFAULT 0;
DECLARE _idprm INT DEFAULT 0;

SET _UGID 	= IF(_SWITCH = 'user', (SELECT ID FROM users WHERE userid = _USERGROUP), (SELECT ID FROM groups WHERE name = _USERGROUP)) ;

IF _UGID > 0 THEN
	SET _idprm	= (SELECT id FROM permission_modules WHERE module_id = (SELECT id FROM modules WHERE label = _MDL)
	AND ugid = _UGID AND switch = _SWITCH) ;

	IF _idprm > 0 THEN
		UPDATE permission_modules SET flag = _FLAG WHERE id = _idprm ;
	ELSE
		INSERT permission_modules (module_id, ugid, switch, flag) VALUES ((SELECT id FROM modules WHERE label = _MDL) , _UGID, _SWITCH, _FLAG) ;
	END IF ;
END IF ;
END
//
delimiter ;

DROP PROCEDURE  IF EXISTS deletePermissionModule ;
delimiter //
CREATE PROCEDURE deletePermissionModule (_MDL VARCHAR(255), _USERGROUP VARCHAR(255), _SWITCH VARCHAR(40))
DETERMINISTIC
BEGIN

DECLARE _UGID INT DEFAULT 0;
DECLARE _idprm INT DEFAULT 0;

SET _UGID 	= IF(_SWITCH = 'user', (SELECT ID FROM users WHERE userid = _USERGROUP), (SELECT ID FROM groups WHERE name = _USERGROUP)) ;

IF _UGID > 0 THEN
	DELETE FROM permission_modules WHERE module_id = (SELECT id FROM modules WHERE label = _MDL)
	AND ugid = _UGID AND switch = _SWITCH ;
END IF ;
END
//
delimiter ;

DROP FUNCTION  IF EXISTS prmsModuleUserByID ;
delimiter //
CREATE FUNCTION prmsModuleUserByID (_USERID VARCHAR(40), _MDL VARCHAR(255), _PRMS INT) RETURNS INT
DETERMINISTIC
BEGIN
DECLARE prmsG INT DEFAULT 0 ;
DECLARE prmsU INT DEFAULT 0 ;

SET prmsG = (
	SELECT DISTINCT
	BIT_OR(permission_modules.flag & _PRMS) AS perms
	FROM
	permission_modules
	WHERE
	permission_modules.module_id = (SELECT id FROM modules WHERE path = _MDL)
	AND
	(
	permission_modules.ugid IN
		(
		SELECT groups_users.`group_id`
		FROM
		users INNER JOIN groups_users ON users.id = groups_users.user_id
		WHERE users.userid = _USERID
		)
	OR

	permission_modules.ugid =
		(
		SELECT id FROM groups WHERE name = 'guest'
		)
	)
	AND
	permission_modules.switch = 'group'
	AND
	(permission_modules.flag & _PRMS)
	) ;
SET prmsG  = IF(prmsG IS NULL, 0, prmsG) ;

SET prmsU  = (
	SELECT DISTINCT
	(permission_modules.flag & _PRMS) AS perms
	FROM
	permission_modules
	WHERE
	permission_modules.ugid =
	(
	SELECT id FROM users WHERE userid = _USERID
	)
	AND
	permission_modules.switch = 'user'
	AND
	(permission_modules.flag & _PRMS)
	AND
	permission_modules.module_id = (SELECT id FROM modules WHERE path = _MDL)
) ;
SET prmsU  = IF(prmsU IS NULL, 0, prmsU) ;


RETURN (prmsG|prmsU) ;

END
//
delimiter ;

DROP FUNCTION  IF EXISTS prmsModuleGroupByName ;
delimiter //
CREATE FUNCTION prmsModuleGroupByName (_GROUPNAME VARCHAR(40), _MDL VARCHAR(255), _PRMS INT) RETURNS INT
DETERMINISTIC
BEGIN
DECLARE prmsG INT DEFAULT 0 ;

SET prmsG = (
	SELECT DISTINCT
	BIT_OR(permission_modules.flag & _PRMS) AS perms
	FROM
	permission_modules
	WHERE
	permission_modules.module_id = (SELECT id FROM modules WHERE path = _MDL)
	AND
	(
	permission_modules.ugid =
		(
		SELECT id FROM groups WHERE name = _GROUPNAME
		)
	)
	AND
	permission_modules.switch = 'group'
	AND
	(permission_modules.flag & _PRMS)
	) ;
SET prmsG  = IF(prmsG IS NULL, 0, prmsG) ;

RETURN (prmsG) ;

END
//
delimiter ;

