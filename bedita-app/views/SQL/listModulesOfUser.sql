SELECT 
modules.*,
IF (modules.status = 'on',1,0) AS bool_status,
IF (modules_users.user_id IS NOT NULL,1,0) AS allowed
FROM 
modules LEFT JOIN modules_users ON modules.id = modules_users.module_id AND user_id = {$userID|SQLvar}
ORDER BY modules.label


