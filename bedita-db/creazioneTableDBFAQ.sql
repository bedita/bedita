
DELETE FROM faq_owners ;
INSERT INTO faq_owners (nome)
SELECT DISTINCT assegnato_a
FROM BEDITA_OLD.ref_domanda
WHERE 
assegnato_a IS NOT NULL AND LENGTH(TRIM(assegnato_a)) > 0 ;

DELETE FROM faq_statistic_fields ;
INSERT INTO faq_statistic_fields (label, switch)
SELECT DISTINCT eta,
'eta'
FROM BEDITA_OLD.ref_domanda
WHERE 
eta IS NOT NULL AND LENGTH(TRIM(eta)) > 0 AND TRIM(eta) <> '--'
UNION
SELECT DISTINCT studio,
'studio'
FROM BEDITA_OLD.ref_domanda
WHERE 
studio IS NOT NULL AND LENGTH(TRIM(studio)) > 0 AND TRIM(studio) <> '--'
UNION
SELECT DISTINCT motivazione,
'motivazione'
FROM BEDITA_OLD.ref_domanda
WHERE 
motivazione IS NOT NULL AND LENGTH(TRIM(motivazione)) > 0 AND TRIM(motivazione) <> '--'
;

INSERT INTO `tmpIDGroups`
SELECT 
ID,
(SELECT MAX( ID ) FROM BEDITA_CAKE.groups) + ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'catFaq')
FROM BEDITA_OLD.ref_domandeattrib ;

DELETE FROM groups WHERE group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'catFaq') ;
INSERT INTO `groups`
SELECT 
tmpIDGroups.ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'catFaq'),
'on', 
old.attrib 
FROM BEDITA_OLD.ref_domandeattrib AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'catFaq');
;


/* Inserisce le domande e risposte */
DELETE FROM faqs ;
INSERT INTO faqs
SELECT 
BEDITA_OLD.ref_domanda.id,
NULL,
"it",
M.id,
S.id,
E.id,
O.id,
BEDITA_OLD.ref_domanda.nome,
comune,
email,
telefono,
giorno,
datarisposta,
vistato,
spedito,
fonti,
domanda,
risposta,
note,
minuti,
bibliotecario,
provenienza,
NULL,
'N'
FROM 
BEDITA_OLD.ref_domanda 
LEFT JOIN BEDITA_CAKE.faq_statistic_fields AS M ON TRIM(BEDITA_OLD.ref_domanda.motivazione) = M.label AND M.switch = 'motivazione'
LEFT JOIN BEDITA_CAKE.faq_statistic_fields AS S ON TRIM(BEDITA_OLD.ref_domanda.studio) = S.label AND S.switch = 'studio'
LEFT JOIN BEDITA_CAKE.faq_statistic_fields AS E ON TRIM(BEDITA_OLD.ref_domanda.eta) = E.label AND E.switch = 'eta'
LEFT JOIN BEDITA_CAKE.faq_owners AS O ON TRIM(BEDITA_OLD.ref_domanda.assegnato_a) = O.nome
;

DROP TABLE IF EXISTS  tmp ;
CREATE TABLE IF NOT EXISTS `tmp` (
  oldID int(11),
  ID int(11)
) ;

INSERT INTO tmp 
SELECT 
id,
(SELECT MAX( BEDITA_CAKE.faqs.ID ) + BEDITA_OLD.ref_rispostepubb.ID  FROM BEDITA_CAKE.faqs)
FROM 
BEDITA_OLD.ref_rispostepubb ;

INSERT INTO faqs
SELECT 
tmp.ID,
BEDITA_OLD.ref_rispostepubb.id,
"it",
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
BEDITA_OLD.ref_rispostepubb.data,
ultimamodifica,
NULL,
NULL,
NULL,
domanda,
risposta,
NULL,
NULL,
NULL,
NULL,
NULL,
pubblico
FROM 
tmp INNER JOIN BEDITA_OLD.ref_rispostepubb ON tmp.oldID = BEDITA_OLD.ref_rispostepubb.id
;

/* solo sala borsa */
DELETE FROM areas_faqs_faq_groups ;
INSERT INTO areas_faqs_faq_groups 
SELECT DISTINCT 
BEDITA_OLD.ref_join_dom_att.id_domanda as faq_id,
(SELECT id  FROM areas WHERE name = 'biblioteca sala borsa') AS area_id,
tmpIDGroups.ID AS group_id
FROM 
tmpIDGroups 
INNER JOIN BEDITA_OLD.ref_join_dom_att ON tmpIDGroups.oldID = BEDITA_OLD.ref_join_dom_att.id_attrib AND  group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'catFaq')
;


