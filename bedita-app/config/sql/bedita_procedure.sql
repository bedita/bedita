-- ---------------------------------------------------
-- 	MODULE PERMISSIONS
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
	SET _idprm	= (SELECT id FROM permission_modules WHERE module_id = (SELECT id FROM modules WHERE name = _MDL)
	AND ugid = _UGID AND switch = _SWITCH) ;

	IF _idprm > 0 THEN
		UPDATE permission_modules SET flag = _FLAG WHERE id = _idprm ;
	ELSE
		INSERT permission_modules (module_id, ugid, switch, flag) VALUES ((SELECT id FROM modules WHERE name = _MDL) , _UGID, _SWITCH, _FLAG) ;
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
	DELETE FROM permission_modules WHERE module_id = (SELECT id FROM modules WHERE name = _MDL)
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

