# upgrade relations attach with inverse relation "attached_to"
UPDATE object_relations
SET switch = "attached_to"
WHERE
object_relations.switch = "attach"
AND
object_relations.id in
(
	SELECT id FROM
	(
		SELECT objects.id
		FROM objects
		WHERE 
		objects.object_type_id = 12
		OR objects.object_type_id = 30
		OR objects.object_type_id = 31
		OR objects.object_type_id = 32
		OR objects.object_type_id = 10
	) as tempTable
);

#upgrade relations download with inverse relation "downloadable_in"
UPDATE object_relations
SET switch = "downloadable_in"
WHERE
object_relations.switch = "download"
AND
object_relations.id in
(
	SELECT id FROM
	(
		SELECT objects.id
		FROM objects
		WHERE 
		objects.object_type_id = 12
		OR objects.object_type_id = 30
		OR objects.object_type_id = 31
		OR objects.object_type_id = 32
		OR objects.object_type_id = 10
	) as tempTable2
);

