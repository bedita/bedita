#upgrade relations attach with inverse relation "attached_to"

# Se l'oggetto è una immagine(12), un audio(31), un video (32), o un be_file(10) 
# e lo switch della relazione è "question", allora lo switch va cambiato in "question_of"

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

