/* BEGIN --- multimedia */
/* PER TEST: DELETE FROM multimedia_objects ; */
/*
NOTA !!!
l'applicativo di coversione deve gestire i duplicati multimedia_objects

NOTA !!!
La tabella: c_ipbanlist		porebbe non esistere sul DB d'origine
							commentare il passaggio di dati in 'banned_ips'
*/

DROP TABLE IF EXISTS  tmpIDMultimedia ;
CREATE TABLE IF NOT EXISTS `tmpIDMultimedia` (
  `oldID` int(11), 
  `ID` int(11),
  tab_name VARCHAR(255) 
) ;

DROP TABLE IF EXISTS  tmp ;
CREATE TABLE IF NOT EXISTS `tmp` (
  id int(11),
  titolo VARCHAR(255) NULL,
  descrizione TEXT NULL,
  filePath VARCHAR(255) NULL,
  fileName VARCHAR(255) NULL,
  fileType VARCHAR(255) NULL,
  fileSize VARCHAR(255) NULL,
  `status` ENUM('on','off') NULL,
  created DATETIME NULL,
  tab_name VARCHAR(255) 
) ;

INSERT INTO BEDITA_CAKE.tmp 
SELECT DISTINCT 
old.ID,
old.titolo,
old.descrizione,
old.fileName,
old.fileName,
old.fileType,
NULL,
old.status,
old.data,
'g_multimedia'
FROM 
BEDITA_OLD.g_multimedia AS old ;

INSERT INTO tmpIDMultimedia 
SELECT 
tmp.ID,
tmp.ID,
tmp.tab_name
FROM 
BEDITA_CAKE.tmp ;

INSERT INTO multimedia_objects  
SELECT 
tmpIDMultimedia.ID, 
tmp.titolo, 
tmp.descrizione,
tmp.filePath,
tmp.fileName,
tmp.fileType,
tmp.fileSize,
tmp.`status`,
tmp.created
FROM tmpIDMultimedia INNER JOIN tmp ON tmpIDMultimedia.oldID = tmp.ID 
WHERE tmpIDMultimedia.tab_name = 'g_multimedia' ;

DELETE FROM tmp ;
INSERT INTO BEDITA_CAKE.tmp 
SELECT DISTINCT 
old.ID,
old.titolo,
NULL,
old.fileName,
old.fileName,
old.fileType,
NULL,
'on',
NULL,
't_materiali'
FROM 
BEDITA_OLD.t_materiali AS old ;

INSERT INTO tmpIDMultimedia 
SELECT 
tmp.ID,
(SELECT MAX( BEDITA_CAKE.multimedia_objects.ID ) + tmp.ID FROM BEDITA_CAKE.multimedia_objects), 
tmp.tab_name
FROM 
BEDITA_CAKE.tmp ;

INSERT INTO multimedia_objects  
SELECT 
tmpIDMultimedia.ID, 
tmp.titolo, 
tmp.descrizione,
tmp.filePath,
tmp.fileName,
tmp.fileType,
tmp.fileSize,
tmp.`status`,
tmp.created
FROM tmpIDMultimedia INNER JOIN tmp ON tmpIDMultimedia.oldID = tmp.ID 
WHERE tmpIDMultimedia.tab_name = 't_materiali' ;

DELETE FROM tmp ;
INSERT INTO BEDITA_CAKE.tmp 
SELECT DISTINCT 
old.ID,
NULL,
NULL,
old.immagine,
NULL,
NULL,
NULL,
'on',
NULL,
'eventi-firstimage'
FROM 
BEDITA_OLD.eventi AS old 
WHERE (immagine IS NOT NULL AND  immagine <> '') ;

INSERT INTO tmpIDMultimedia 
SELECT 
tmp.ID,
(SELECT MAX( BEDITA_CAKE.multimedia_objects.ID ) + tmp.ID FROM BEDITA_CAKE.multimedia_objects), 
tmp.tab_name
FROM 
BEDITA_CAKE.tmp ;

INSERT INTO multimedia_objects  
SELECT 
tmpIDMultimedia.ID, 
tmp.titolo, 
tmp.descrizione,
tmp.filePath,
tmp.fileName,
tmp.fileType,
tmp.fileSize,
tmp.`status`,
tmp.created
FROM tmpIDMultimedia INNER JOIN tmp ON tmpIDMultimedia.oldID = tmp.ID 
WHERE tmpIDMultimedia.tab_name = 'eventi-firstimage' ;

DELETE FROM tmp ;
INSERT INTO BEDITA_CAKE.tmp 
SELECT DISTINCT 
old.ID,
NULL,
NULL,
old.file,
old.file,
NULL,
NULL,
'on',
NULL,
'eventi-filestatico'
FROM 
BEDITA_OLD.eventi AS old 
WHERE (file IS NOT NULL AND  file <> '') ;

INSERT INTO tmpIDMultimedia 
SELECT 
tmp.ID,
(SELECT MAX( BEDITA_CAKE.multimedia_objects.ID ) + tmp.ID FROM BEDITA_CAKE.multimedia_objects), 
tmp.tab_name
FROM 
BEDITA_CAKE.tmp ;

INSERT INTO multimedia_objects  
SELECT 
tmpIDMultimedia.ID, 
tmp.titolo, 
tmp.descrizione,
tmp.filePath,
tmp.fileName,
tmp.fileType,
tmp.fileSize,
tmp.`status`,
tmp.created
FROM tmpIDMultimedia INNER JOIN tmp ON tmpIDMultimedia.oldID = tmp.ID 
WHERE tmpIDMultimedia.tab_name = 'eventi-filestatico' ;

DELETE FROM tmp ;
INSERT INTO BEDITA_CAKE.tmp 
SELECT DISTINCT 
old.ID,
NULL,
NULL,
old.immagine,
NULL,
NULL,
NULL,
'on',
NULL,
'eventi-audiofile'
FROM 
BEDITA_OLD.eventi AS old 
WHERE (audioFile IS NOT NULL AND  audioFile <> '') ;

INSERT INTO tmpIDMultimedia 
SELECT 
tmp.ID,
(SELECT MAX( BEDITA_CAKE.multimedia_objects.ID ) + tmp.ID FROM BEDITA_CAKE.multimedia_objects), 
tmp.tab_name
FROM 
BEDITA_CAKE.tmp ;

INSERT INTO multimedia_objects  
SELECT 
tmpIDMultimedia.ID, 
tmp.titolo, 
tmp.descrizione,
tmp.filePath,
tmp.fileName,
tmp.fileType,
tmp.fileSize,
tmp.`status`,
tmp.created
FROM tmpIDMultimedia INNER JOIN tmp ON tmpIDMultimedia.oldID = tmp.ID 
WHERE tmpIDMultimedia.tab_name = 'eventi-audiofile' ;

DELETE FROM tmp ;
INSERT INTO BEDITA_CAKE.tmp 
SELECT DISTINCT 
old.ID,
NULL,
NULL,
old.imgBio,
NULL,
NULL,
NULL,
'on',
NULL,
'au_autori'
FROM 
BEDITA_OLD.au_autori AS old 
WHERE (imgBio IS NOT NULL AND  imgBio <> '') ;

INSERT INTO tmpIDMultimedia 
SELECT 
tmp.ID,
(SELECT MAX( BEDITA_CAKE.multimedia_objects.ID ) + tmp.ID FROM BEDITA_CAKE.multimedia_objects), 
tmp.tab_name
FROM 
BEDITA_CAKE.tmp ;

INSERT INTO multimedia_objects  
SELECT 
tmpIDMultimedia.ID, 
tmp.titolo, 
tmp.descrizione,
tmp.filePath,
tmp.fileName,
tmp.fileType,
tmp.fileSize,
tmp.`status`,
tmp.created
FROM tmpIDMultimedia INNER JOIN tmp ON tmpIDMultimedia.oldID = tmp.ID 
WHERE tmpIDMultimedia.tab_name = 'au_autori' ;



DELETE FROM tmp ;
INSERT INTO BEDITA_CAKE.tmp 
SELECT DISTINCT 
old.ID,
NULL,
NULL,
old.imgCover,
old.imgCover,
NULL,
NULL,
'on',
NULL,
's_segnature'
FROM 
BEDITA_OLD.s_segnature AS old 
WHERE (imgCover IS NOT NULL AND  imgCover <> '') ;

INSERT INTO tmpIDMultimedia 
SELECT 
tmp.ID,
(SELECT MAX( BEDITA_CAKE.multimedia_objects.ID ) + tmp.ID FROM BEDITA_CAKE.multimedia_objects), 
tmp.tab_name
FROM 
BEDITA_CAKE.tmp ;

INSERT INTO multimedia_objects  
SELECT 
tmpIDMultimedia.ID, 
tmp.titolo, 
tmp.descrizione,
tmp.filePath,
tmp.fileName,
tmp.fileType,
tmp.fileSize,
tmp.`status`,
tmp.created
FROM tmpIDMultimedia INNER JOIN tmp ON tmpIDMultimedia.oldID = tmp.ID 
WHERE tmpIDMultimedia.tab_name = 's_segnature' ;

/* END --- multimedia */



/* BEGIN --- contenuti */

DROP TABLE IF EXISTS  tmpIDContents ;
CREATE TABLE IF NOT EXISTS `tmpIDContents` (
   `oldID` int(11) ,
   `ID` int(11),
   content_type_id INTEGER UNSIGNED NOT NULL
) ;

/* Inserimento ID contenuti */

INSERT INTO `tmpIDContents`
SELECT 
ID,
ID, 
(SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'evento')
FROM BEDITA_OLD.eventi 
WHERE 
BEDITA_OLD.eventi.tipo = 'evento' ; 
;                                                                                            

INSERT INTO `tmpIDContents`
SELECT 
ID,
ID, 
(SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'doc')
FROM BEDITA_OLD.eventi 
WHERE 
BEDITA_OLD.eventi.tipo = 'doc' ; 
;

INSERT INTO `tmpIDContents`
SELECT 
ID,
ID, 
(SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'biblio')
FROM BEDITA_OLD.eventi 
WHERE 
BEDITA_OLD.eventi.tipo = 'biblio' ; 
;

INSERT INTO contents (id, content_type_id)
SELECT 
ID, 
content_type_id
FROM tmpIDContents ;

INSERT INTO `tmpIDContents`
SELECT 
BEDITA_OLD.g_gallerie.ID,
(SELECT MAX( BEDITA_CAKE.contents.ID ) + BEDITA_OLD.g_gallerie.ID FROM BEDITA_CAKE.contents), 
(SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'galleria')
FROM BEDITA_OLD.g_gallerie ;

INSERT INTO contents (id, content_type_id)
SELECT 
ID, 
content_type_id
FROM tmpIDContents 
WHERE
content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'galleria') ;

INSERT INTO `tmpIDContents`
SELECT 
BEDITA_OLD.news.ID,
(SELECT MAX( BEDITA_CAKE.contents.ID ) + BEDITA_OLD.news.ID FROM BEDITA_CAKE.contents), 
(SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'news')
FROM BEDITA_OLD.news ;

INSERT INTO contents (id, content_type_id)
SELECT 
ID, 
content_type_id
FROM tmpIDContents 
WHERE
content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'news') ;

INSERT INTO `tmpIDContents`
SELECT 
BEDITA_OLD.au_autori.ID,
(SELECT MAX( BEDITA_CAKE.contents.ID ) + BEDITA_OLD.au_autori.ID FROM BEDITA_CAKE.contents), 
(SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'autore')
FROM BEDITA_OLD.au_autori ;

INSERT INTO contents (id, content_type_id)
SELECT 
ID, 
content_type_id
FROM tmpIDContents
WHERE
content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'autore') ;

/* Inserimento dati principali */
REPLACE INTO contents  
SELECT
NEW.ID,
NEW.content_type_id,
OLD.authorID, 
OLD.status, 
OLD.data, 
OLD.data, 
OLD.scadenza, 
'it', 
OLD.titolo, 
OLD.sottotitolo, 
OLD.testo, 
OLD.testolungo, 
OLD.authorName, 
OLD.allow_comments 
FROM 
BEDITA_OLD.eventi AS OLD INNER JOIN BEDITA_CAKE.tmpIDContents NEW ON OLD.ID = NEW.oldID AND NEW.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'evento') 
WHERE OLD.tipo = 'evento';

REPLACE INTO contents  
SELECT
NEW.ID,
NEW.content_type_id,
OLD.authorID, 
OLD.status, 
OLD.data, 
OLD.data, 
OLD.scadenza, 
'it', 
OLD.titolo, 
OLD.sottotitolo, 
OLD.testo, 
OLD.testolungo, 
OLD.authorName, 
OLD.allow_comments 
FROM 
BEDITA_OLD.eventi AS OLD INNER JOIN BEDITA_CAKE.tmpIDContents NEW ON OLD.ID = NEW.oldID AND NEW.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'biblio') 
WHERE OLD.tipo = 'biblio';

REPLACE INTO contents  
SELECT
NEW.ID,
NEW.content_type_id,
OLD.authorID, 
OLD.status, 
OLD.data, 
OLD.data, 
OLD.scadenza, 
'it', 
OLD.titolo, 
OLD.sottotitolo, 
OLD.testo, 
OLD.testolungo, 
OLD.authorName, 
OLD.allow_comments 
FROM 
BEDITA_OLD.eventi AS OLD INNER JOIN BEDITA_CAKE.tmpIDContents NEW ON OLD.ID = NEW.oldID AND NEW.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'doc') 
WHERE OLD.tipo = 'doc';

REPLACE INTO contents  
SELECT
NEW.ID,
NEW.content_type_id,
NULL, 
OLD.status, 
NULL, 
NULL, 
NULL, 
'it', 
OLD.titolo, 
NULL, 
NULL, 
NULL, 
NULL, 
'N' 
FROM 
BEDITA_OLD.g_gallerie AS OLD INNER JOIN BEDITA_CAKE.tmpIDContents AS NEW ON OLD.ID = NEW.oldID AND NEW.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'galleria') 
;

REPLACE INTO contents  
SELECT
NEW.ID,
NEW.content_type_id,
NULL, 
'on', 
OLD.data, 
OLD.data, 
OLD.scadenza, 
'it', 
NULL, 
NULL, 
OLD.testo, 
NULL, 
NULL, 
'N' 
FROM 
BEDITA_OLD.news AS OLD INNER JOIN BEDITA_CAKE.tmpIDContents NEW ON OLD.ID = NEW.oldID AND NEW.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'news') 
;

REPLACE INTO contents  
SELECT
NEW.ID,
NEW.content_type_id,
NULL, 
OLD.status, 
NULL, 
NULL, 
NULL, 
'it', 
NULL, 
NULL, 
NULL, 
NULL, 
NULL, 
'N' 
FROM 
BEDITA_OLD.au_autori AS OLD INNER JOIN BEDITA_CAKE.tmpIDContents NEW ON OLD.ID = NEW.oldID AND NEW.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'autore') 
;

/* Inserimento associazioni con gli oggetti multimediali */
INSERT INTO contents_multimedia_objects 
SELECT
multimedia_objects.id,
'firstImage',
contents.id
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID 
INNER JOIN tmpIDMultimedia ON tmpIDContents.oldID = tmpIDMultimedia.oldID AND tmpIDMultimedia.tab_name = 'eventi-firstimage'
INNER JOIN multimedia_objects ON tmpIDMultimedia.ID = multimedia_objects.id
;

INSERT INTO contents_multimedia_objects 
SELECT
multimedia_objects.id,
'firstImage',
contents.id
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID 
INNER JOIN tmpIDMultimedia ON tmpIDContents.oldID = tmpIDMultimedia.oldID AND tmpIDMultimedia.tab_name = 'au_autori'
INNER JOIN multimedia_objects ON tmpIDMultimedia.ID = multimedia_objects.id
;

INSERT INTO contents_multimedia_objects 
SELECT
multimedia_objects.id,
'fileStatico',
contents.id
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID 
INNER JOIN tmpIDMultimedia ON tmpIDContents.oldID = tmpIDMultimedia.oldID AND tmpIDMultimedia.tab_name = 'eventi-filestatico'
INNER JOIN multimedia_objects ON tmpIDMultimedia.ID = multimedia_objects.id
;

INSERT INTO contents_multimedia_objects 
SELECT
multimedia_objects.id,
'audioVideo',
contents.id
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID 
INNER JOIN tmpIDMultimedia ON tmpIDContents.oldID = tmpIDMultimedia.oldID AND tmpIDMultimedia.tab_name = 'eventi-audiofile'
INNER JOIN multimedia_objects ON tmpIDMultimedia.ID = multimedia_objects.id
;

INSERT INTO contents_multimedia_objects 
SELECT
tmpIDMultimedia.ID,
'inline',
contents.id
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID 
INNER JOIN BEDITA_OLD.g_gallerie_multimedia ON tmpIDContents.oldID = BEDITA_OLD.g_gallerie_multimedia.IDgall
INNER JOIN tmpIDMultimedia ON BEDITA_OLD.g_gallerie_multimedia.IDmulti = tmpIDMultimedia.oldID AND tmpIDMultimedia.tab_name = 'g_multimedia'
WHERE 
tmpIDContents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'galleria')
;

INSERT INTO contents_multimedia_objects 
SELECT
tmpIDMultimedia.ID,
'attachment',
contents.id
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID 
INNER JOIN BEDITA_OLD.t_materiali ON tmpIDContents.oldID = BEDITA_OLD.t_materiali.IDevento
INNER JOIN tmpIDMultimedia ON BEDITA_OLD.t_materiali.ID = tmpIDMultimedia.oldID AND tmpIDMultimedia.tab_name = 't_materiali' ;

/* Dati specifici per i diversi tipi di contenuto */
INSERT INTO author_features (content_id, nome, cognome, poetica, biografia, stringa) 
SELECT
contents.id,
old.nome,
old.cognome,
old.poetica,
old.biografia,
old.stringa
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID AND tmpIDContents.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'autore')
INNER JOIN BEDITA_OLD.au_autori AS old ON tmpIDContents.oldID = old.ID 
WHERE 
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'autore')
;

INSERT INTO biblio_contents (multimedia_object_id, book_id, content_id, testo, codice_ricerca, switch)
SELECT
BEDITA_CAKE.tmpIDMultimedia.ID,
NULL, 
contents.ID, 
old.testo,
old.BID,
'txt'
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID 
INNER JOIN BEDITA_OLD.s_segnature_eventi ON tmpIDContents.oldID = BEDITA_OLD.s_segnature_eventi.IDevento 
INNER JOIN BEDITA_OLD.s_segnature AS old ON BEDITA_OLD.s_segnature_eventi.IDsegnatura = old.id 
LEFT JOIN BEDITA_CAKE.tmpIDMultimedia ON old.id = BEDITA_CAKE.tmpIDMultimedia.oldID
WHERE 
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'biblio')
;


/* END --- contenuti */

/* BEGIN --- Relazioni tra contenuti */

INSERT INTO contents_contents (first_id, second_id, relation_type, percento, prior, inline) 
SELECT DISTINCT 
target.id AS first_id,
source.id AS second_id,
'parent',
NULL,
old.prior,
NULL
FROM
contents AS source 
INNER JOIN tmpIDContents AS T1 ON source.id = T1.ID AND source.ID = T1.oldID
INNER JOIN BEDITA_OLD.eventi_self_eventi AS old ON T1.oldID = old.IDevento
LEFT JOIN tmpIDContents AS T2 ON old.IDparent = T2.oldID
LEFT JOIN contents AS target ON T2.ID = target.id AND target.id = T2.oldID
WHERE 
source.content_type_id = target.content_type_id
;

INSERT INTO contents_contents (first_id, second_id, relation_type, percento, prior, inline) 
SELECT DISTINCT 
target.id AS first_id,
source.id AS second_id,
'brother',
NULL,
old.prior,
NULL
FROM
contents AS source 
INNER JOIN tmpIDContents AS T1 ON source.id = T1.ID AND source.ID = T1.oldID
INNER JOIN BEDITA_OLD.eventi_self_eventi AS old ON T1.oldID = old.IDevento
LEFT JOIN tmpIDContents AS T2 ON old.IDparent = T2.oldID
LEFT JOIN contents AS target ON T2.ID = target.id AND target.id = T2.oldID
WHERE 
source.content_type_id <> target.content_type_id 
;

INSERT INTO contents_contents (first_id, second_id, relation_type, percento, prior, inline) 
SELECT DISTINCT 
target.id AS first_id,
source.id AS second_id,
'brother',
old.percent,
NULL,
NULL
FROM
contents AS source 
INNER JOIN tmpIDContents AS T1 ON source.id = T1.ID AND T1.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'autore')
INNER JOIN BEDITA_OLD.au_autori_selflookup AS old ON T1.oldID = old.ID_aut1
LEFT JOIN tmpIDContents AS T2 ON old.ID_aut2 = T2.oldID  AND T2.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'autore')
LEFT JOIN contents AS target ON T2.ID = target.id
WHERE 
source.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'autore')
AND target.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'autore') 
;

/* contenuto in relazione con le gallerie */
INSERT INTO contents_contents (first_id, second_id, relation_type, percento, prior, inline) 
SELECT DISTINCT 
source.id AS first_id,
target.ID,
'brother',
NULL,
NULL,
old.gallInline
FROM
contents AS source 
INNER JOIN tmpIDContents AS T1 ON source.id = T1.ID AND T1.ID = T1.oldID 
INNER JOIN BEDITA_OLD.g_lookup_gallerie_eventi AS old ON T1.oldID = old.IDevento
INNER JOIN tmpIDContents AS T2 ON old.IDgall = T2.oldID AND T2.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'galleria')
LEFT JOIN contents AS target ON T2.ID = target.id 
;


/* END --- Relazioni tra contenuti */


/* BEGIN --- Contenuti accessori (commenti, links, ....) */

INSERT INTO links (obj_id, obj_type, link_swtich, description, title, link_status, url1, url2, url3, coord1, coord2, coord3, target)
SELECT
contents.id,
'content',
'url',
NULL,
old.nome,
'on',
old.url,
NULL,
NULL,
old.dim_x,
old.dim_y,
NULL,
old.target
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID AND tmpIDContents.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'evento')
INNER JOIN BEDITA_OLD.links AS old ON tmpIDContents.oldID = old.id_Evento 
WHERE 
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'evento')
;

INSERT INTO links (obj_id, obj_type, link_swtich, description, title, link_status, url1, url2, url3, coord1, coord2, coord3, target)
SELECT
contents.id,
'content',
'url',
NULL,
old.nome,
'on',
old.url,
NULL,
NULL,
old.dim_x,
old.dim_y,
NULL,
old.target
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID AND tmpIDContents.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'biblio')
INNER JOIN BEDITA_OLD.links AS old ON tmpIDContents.oldID = old.id_Evento 
WHERE 
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'biblio')
;

INSERT INTO links (obj_id, obj_type, link_swtich, description, title, link_status, url1, url2, url3, coord1, coord2, coord3, target)
SELECT
contents.id,
'content',
'url',
NULL,
old.nome,
'on',
old.url,
NULL,
NULL,
old.dim_x,
old.dim_y,
NULL,
old.target
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID AND tmpIDContents.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'doc')
INNER JOIN BEDITA_OLD.links AS old ON tmpIDContents.oldID = old.id_Evento 
WHERE 
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'doc')
;

INSERT INTO calendars (content_id, inizio, fine)
SELECT
contents.id,
old.inizio,
old.fine
FROM 
contents 
INNER JOIN tmpIDContents ON contents.id = tmpIDContents.ID AND tmpIDContents.content_type_id = (SELECT id FROM content_types WHERE content_types.name = 'evento')
INNER JOIN BEDITA_OLD.calendario AS old ON tmpIDContents.oldID = old.IDevento 
WHERE 
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'evento')
;

INSERT INTO comments 
SELECT 
old.id,
T1.ID,
old.IP,
old.author,
old.email,
old.url,
old.testo,
old.status,
old.created_on,
old.modified_on
FROM 
BEDITA_OLD.c_commenti AS old INNER JOIN BEDITA_CAKE.tmpIDContents AS T1 ON old.eventoID = T1.ID AND T1.ID = T1.oldID
;

REPLACE INTO banned_ips
SELECT
IP
FROM 
BEDITA_OLD.c_ipbanlist
WHERE 
IP IS NOT NULL 
;

INSERT INTO forms (id, titolo, descrizione, author, dataInsert, status) 
SELECT 
ID,
titolo,
descrizione,
autore,
dataInsert,
status
FROM BEDITA_OLD.q_questionari
;

INSERT INTO answers (id, querie_id, testo, prior)
SELECT
ID,
domandaID,
testo,
prior
FROM
BEDITA_OLD.q_risposte
;

INSERT INTO queries (id, form_id, testo, prior, status, max_char, max_val, tipo, lang)
SELECT
ID,
questID,
testo, 
prior,
status,
maxChar,
maxVal,
tipoID,
'it'
FROM
BEDITA_OLD.q_domande
;

INSERT INTO contents_forms (form_id, content_id, inline)
SELECT 
IDquest,
IDevento,
questInline
FROM
BEDITA_OLD.q_lookup_questionari_eventi
;

INSERT INTO results (answer_id, session, IP, dataInsert, txt, value)
SELECT
rispostaID,
sessionID,
IP,
dataInsert,
testo,
valore
FROM 
BEDITA_OLD.q_risultati 
;

/* END --- Contenuti accessori (questionari, commenti, links, ....) */

/* BEGIN --- Contenuti, gruppi, aree */

INSERT INTO areas_contents_groups (area_id, content_id, group_id, prior, inline, percento) 
SELECT 
NULL, 
contents.id,
BEDITA_CAKE.tmpIDGroups.ID, 
oldRel.prior,
NULL,
NULL
FROM
contents 
INNER JOIN BEDITA_OLD.eventi_tipologie_lookup AS oldRel ON contents.id = oldRel.IDevento 
INNER JOIN BEDITA_CAKE.tmpIDGroups ON oldRel.IDtipo = BEDITA_CAKE.tmpIDGroups.oldID AND BEDITA_CAKE.tmpIDGroups.group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'tipologia')
WHERE
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'evento') 
ORDER BY 
contents.id, 
BEDITA_CAKE.tmpIDGroups.ID
;

INSERT INTO areas_contents_groups (area_id, content_id, group_id, prior, inline, percento) 
SELECT 
NULL, 
contents.id,
BEDITA_CAKE.tmpIDGroups.ID, 
NULL,
NULL,
NULL
FROM
contents 
INNER JOIN BEDITA_OLD.eventi_categorie_lookup AS oldRel ON contents.id = oldRel.IDevento 
INNER JOIN BEDITA_CAKE.tmpIDGroups ON oldRel.IDcat = BEDITA_CAKE.tmpIDGroups.oldID AND BEDITA_CAKE.tmpIDGroups.group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'categoria')
WHERE
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'biblio') 
ORDER BY 
contents.id, 
BEDITA_CAKE.tmpIDGroups.ID
;

INSERT INTO areas_contents_groups (area_id, content_id, group_id, prior, inline, percento) 
SELECT 
NULL, 
contents.id,
BEDITA_CAKE.tmpIDGroups.ID, 
oldRel.prior,
NULL,
NULL
FROM
contents 
INNER JOIN 
(
BEDITA_OLD.eventi_aree AS oldRel
INNER JOIN BEDITA_OLD.aree ON oldRel.IDarea = BEDITA_OLD.aree.ID AND BEDITA_OLD.aree.refcatID IS NOT NULL 
)
 ON contents.id = oldRel.IDevento 
INNER JOIN BEDITA_CAKE.tmpIDGroups ON oldRel.IDarea = BEDITA_CAKE.tmpIDGroups.oldID AND BEDITA_CAKE.tmpIDGroups.group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'sezione')
WHERE
contents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'doc') 
ORDER BY 
contents.id, 
BEDITA_CAKE.tmpIDGroups.ID
;

INSERT INTO areas_contents_groups (area_id, content_id, group_id, prior, inline, percento) 
SELECT 
NULL,
BEDITA_CAKE.tmpIDContents.ID,
tmpIDGroups.ID,
NULL,
NULL,
BEDITA_OLD.au_autori_categorie.percent
FROM
groups INNER JOIN tmpIDGroups ON groups.id = tmpIDGroups.ID AND groups.group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'soggetto')
INNER JOIN BEDITA_OLD.au_autori_categorie ON tmpIDGroups.oldID = BEDITA_OLD.au_autori_categorie.ID_cat 
INNER JOIN BEDITA_CAKE.tmpIDContents ON BEDITA_OLD.au_autori_categorie.ID_aut = BEDITA_CAKE.tmpIDContents.oldID AND BEDITA_CAKE.tmpIDContents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'autore')
;

INSERT INTO areas_contents_groups (area_id, content_id, group_id, prior, inline, percento) 
SELECT 
NULL,
BEDITA_CAKE.tmpIDContents.ID,
(SELECT ID FROM  tmpIDGroups WHERE oldID = 10000),
NULL,
NULL,
NULL 
FROM
BEDITA_CAKE.tmpIDContents 
INNER JOIN BEDITA_OLD.au_gruppi_autori ON BEDITA_CAKE.tmpIDContents.oldID = BEDITA_OLD.au_gruppi_autori.IDautore AND BEDITA_OLD.au_gruppi_autori.IDgruppo = (SELECT ID FROM BEDITA_OLD.au_gruppi WHERE BEDITA_OLD.au_gruppi.nome = 'Autori bolognesi')
;

/* 
Inserisce le relazioni eventi, biblio, doc e aree non ancora espresse tramite i groups
*/
INSERT INTO areas_contents_groups (area_id, content_id, group_id, prior, inline, percento) 
SELECT
old.IDarea,
old.IDevento,
NULL,
old.prior,
NULL,
NULL
FROM 
BEDITA_OLD.eventi_aree AS old
LEFT JOIN 
(BEDITA_CAKE.areas_contents_groups AS T1 INNER JOIN BEDITA_CAKE.areas_contents_groups AS T2 ON T1.group_id = T2.group_id AND T1.area_id IS NOT NULL AND T2.content_id IS NOT NULL )
ON old.IDarea = T1.area_id AND old.IDevento = T2.content_id
GROUP BY old.IDevento
HAVING count(old.IDevento) < 2
;

/*
NOTA !!!
Specifico Biblioteca Sala Borsa.
Vegono inserite le relazioni autori area Xanadu.
*/
INSERT INTO areas_contents_groups (area_id, content_id, group_id, prior, inline, percento) 
SELECT
(SELECT id FROM BEDITA_CAKE.areas WHERE name = 'xanadu' ),
tmpIDContents.ID,
NULL,
NULL,
NULL,
NULL
FROM 
BEDITA_CAKE.tmpIDContents 
INNER JOIN BEDITA_OLD.au_gruppi_autori AS old ON BEDITA_CAKE.tmpIDContents.oldID = old.IDautore
INNER JOIN BEDITA_OLD.au_gruppi AS old2 ON old.IDgruppo = old2.ID AND old2.ID = (SELECT ID FROM BEDITA_OLD.au_gruppi WHERE nome = 'Autori Xanadu')
WHERE 
tmpIDContents.content_type_id = (SELECT id FROM BEDITA_CAKE.content_types WHERE BEDITA_CAKE.content_types.name = 'autore')
;






/* END --- Contenuti, gruppi, aree */

