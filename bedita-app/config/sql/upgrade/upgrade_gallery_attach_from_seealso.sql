# upgrade relations seealso with relation "attach"
# solo per ieB, dove la seealso sopperiva alla mancanza di attach di galleries

# Se l'oggetto(id) è una gallery(29)
# e lo switch della relazione è "seealso", allora lo switch va cambiato in "attached_to"

UPDATE object_relations
SET switch = "attached_to"
WHERE
object_relations.switch = "seealso"
AND
object_relations.id in
(
	SELECT id FROM
	(
		SELECT objects.id
		FROM objects
		WHERE 
		objects.object_type_id = 29
	) as tempTable
);


# Se l'oggetto è qualsiasi, e l'oggetto collegato(object_id) è una gallery(29)
# e lo switch della relazione è "seealso", allora lo switch va cambiato in "attach"
# con un incremento priority di 100 per sicurezza (che i nuovi attach vadano in fondo insomma)

UPDATE object_relations
SET switch = "attach", priority = priority + 100
WHERE
object_relations.switch = "seealso"
AND
object_relations.object_id in
(
	SELECT id FROM
	(
		SELECT objects.id
		FROM objects
		WHERE 
		objects.object_type_id = 29
	) as tempTable
);
