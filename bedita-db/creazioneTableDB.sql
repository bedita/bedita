DROP TABLE IF EXISTS areas;
CREATE TABLE areas (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  lang CHAR(7) NULL,
  tipo SET('site','newsletter') NULL,
  name VARCHAR(255) NULL,
  servername VARCHAR(255) NULL,
  `status` ENUM('on','off','private','hidden') NULL,
  PRIMARY KEY(id)
);


DROP TABLE IF EXISTS group_types ;
CREATE TABLE group_types (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NULL,
  PRIMARY KEY(id)
);
INSERT INTO group_types (id, name) VALUES(1, 'categoria') ;
INSERT INTO group_types (id, name) VALUES(2, 'tipologia') ;
INSERT INTO group_types (id, name) VALUES(3, 'sezione') ;
INSERT INTO group_types (id, name) VALUES(4, 'soggetto') ;
INSERT INTO group_types (id, name) VALUES(5, 'catLibreria') ;
INSERT INTO group_types (id, name) VALUES(6, 'catFaq') ;
INSERT INTO group_types (id, name) VALUES(7, 'catTimeline') ;
INSERT INTO group_types (id, name) VALUES(8, 'catCartiglio') ;

DROP TABLE IF EXISTS groups;
CREATE TABLE groups (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  group_type_id INTEGER UNSIGNED NOT NULL,
  status SET('on','off') NULL,
  name VARCHAR(255) NULL,
  PRIMARY KEY(id),
  INDEX groups_FKIndex1(group_type_id)
);

DROP TABLE IF EXISTS areas_contents_groups;
CREATE TABLE areas_contents_groups (
  area_id INTEGER UNSIGNED NULL,
  content_id INTEGER UNSIGNED NULL,
  group_id INTEGER UNSIGNED NULL,
  prior INTEGER UNSIGNED NULL,
  percento INTEGER UNSIGNED NULL,
  inline ENUM('Y','N') NULL,
  INDEX areas_contents_groups_FKIndex1(group_id),
  INDEX areas_contents_groups_FKIndex2(content_id),
  INDEX areas_contents_groups_FKIndex3(area_id),
  INDEX areas_contents_groups_FKIndex12(area_id, group_id),
  INDEX areas_contents_groups_FKIndex13(area_id, content_id),
  INDEX areas_contents_groups_FKIndex14(area_id, group_id, content_id)
);

DROP TABLE IF EXISTS lang_texts;
CREATE TABLE lang_texts (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  lang CHAR(7) NOT NULL,
  source_id INTEGER UNSIGNED NOT NULL,
  name_table VARCHAR(255) NOT NULL,
  field_name VARCHAR(255) NOT NULL,
  smallText VARCHAR(255) NULL,
  text TEXT NULL,
  PRIMARY KEY(id),
  INDEX lang_texts_FKIndex1(source_id),
  INDEX lang_texts_FKIndex4(source_id, name_table, field_name)
);


DROP TABLE IF EXISTS content_types;
CREATE TABLE content_types (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NULL,
  PRIMARY KEY(id)
);
INSERT INTO content_types (id, name) VALUES(1, 'evento') ;
INSERT INTO content_types (id, name) VALUES(2, 'biblio') ;
INSERT INTO content_types (id, name) VALUES(3, 'doc') ;
INSERT INTO content_types (id, name) VALUES(4, 'galleria') ;
INSERT INTO content_types (id, name) VALUES(5, 'news') ;
INSERT INTO content_types (id, name) VALUES(6, 'autore') ;
INSERT INTO content_types (id, name) VALUES(7, 'libro') ;
INSERT INTO content_types (id, name) VALUES(8, 'libreria') ;

DROP TABLE IF EXISTS contents;
CREATE TABLE contents (
  id INTEGER UNSIGNED NOT NULL,
  content_type_id INTEGER UNSIGNED NOT NULL,
  administrator_id INTEGER UNSIGNED NULL,
  `status` SET('on','off','draft') NULL,
  `data` DATE NULL,
  inizio DATE NULL,
  fine DATE NULL,
  lang CHAR(7) NULL,
  titolo VARCHAR(255) NULL,
  sottotitolo VARCHAR(255) NULL,
  testo TEXT NULL,
  testolungo TEXT NULL,
  administrator_nome VARCHAR(255) NULL,
  allow_comments SET('Y','N') NULL DEFAULT 'Y',
  PRIMARY KEY(id),
  INDEX contents_FKIndex1(administrator_id),
  INDEX contents_FKIndex2(content_type_id)
);

DROP TABLE IF EXISTS contents_contents;
CREATE TABLE contents_contents (
  first_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  second_id INTEGER UNSIGNED NULL,
  relation_type SET('parent','lang','brother') NULL,
  percento INTEGER UNSIGNED NULL,
  prior INTEGER UNSIGNED NULL,
  inline ENUM('Y','N') NULL,  
  PRIMARY KEY(first_id, second_id)
);

DROP TABLE IF EXISTS author_features;
CREATE TABLE author_features (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  content_id INTEGER UNSIGNED NOT NULL,
  nome VARCHAR(32) NULL,
  cognome VARCHAR(32) NULL,
  poetica TEXT NULL,
  biografia TEXT NULL,
  stringa VARCHAR(255) NULL,
  PRIMARY KEY(id),
  INDEX library_characteristics_FKIndex1(content_id)
);

DROP TABLE IF EXISTS biblio_contents;
CREATE TABLE biblio_contents (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  multimedia_object_id INTEGER UNSIGNED NULL,
  book_id INTEGER UNSIGNED NULL,
  content_id INTEGER UNSIGNED NOT NULL,
  testo TEXT NULL,
  codice_ricerca VARCHAR(255) NULL,
  switch SET('txt','book') NULL,
  PRIMARY KEY(id),
  INDEX biblio_contents_FKIndex2(book_id),
  INDEX biblio_contents_FKIndex3(multimedia_object_id),
  INDEX biblio_contents_FKIndex4(content_id)
);

DROP TABLE IF EXISTS calendars;
CREATE TABLE calendars (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  content_id INTEGER UNSIGNED NOT NULL,
  inizio DATE NULL,
  fine DATE NULL,
  PRIMARY KEY(id, content_id),
  INDEX calendars_FKIndex1(content_id)
);

DROP TABLE IF EXISTS links;
CREATE TABLE links (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  obj_id INTEGER UNSIGNED NOT NULL,
  obj_type SET('content','timeline','cartiglio') NULL,
  link_swtich SET('url','coord','mapsgoogle') NULL,
  description TINYTEXT NOT NULL,
  title VARCHAR(255) NULL,
  link_status SET('on','off','draf') NULL,
  url1 VARCHAR(255) NULL,
  url2 VARCHAR(255) NULL,
  url3 VARCHAR(255) NULL,
  coord1 DOUBLE NULL,
  coord2 DOUBLE NULL,
  coord3 DOUBLE NULL,
  target VARCHAR(40) NULL,
  PRIMARY KEY(id),
  INDEX links_FKIndex1(obj_id, obj_type)
);

DROP TABLE IF EXISTS forms;
CREATE TABLE forms (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  titolo VARCHAR(255) NULL,
  descrizione TEXT NULL,
  dataInsert DATETIME NULL,
  status ENUM('on','off') NULL,
  author VARCHAR(255) NULL,
  PRIMARY KEY(id)
);

DROP TABLE IF EXISTS queries;
CREATE TABLE queries (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  form_id INTEGER UNSIGNED NOT NULL,
  lang CHAR(7) NULL,
  testo VARCHAR(255) NULL,
  prior INTEGER UNSIGNED NULL,
  status SET('on','off') NULL,
  max_char INTEGER UNSIGNED NULL,
  max_val INTEGER UNSIGNED NULL,
  tipo ENUM('multipleChoise','singleChoise','openText','checkOpen','grade') NULL,
  PRIMARY KEY(id),
  INDEX queries_FKIndex1(form_id)
);

DROP TABLE IF EXISTS answers;
CREATE TABLE answers (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  querie_id INTEGER UNSIGNED NOT NULL,
  testo VARCHAR(255) NULL,
  prior INTEGER UNSIGNED NULL,
  PRIMARY KEY(id),
  INDEX answers_FKIndex1(querie_id)
);

DROP TABLE IF EXISTS results ;
CREATE TABLE results (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  answer_id INTEGER UNSIGNED NOT NULL,
  session VARCHAR(255) NULL,
  IP VARCHAR(16) NULL,
  dataInsert DATETIME NULL,
  txt VARCHAR(255) NULL,
  value INTEGER UNSIGNED NULL,
  PRIMARY KEY(id),
  INDEX results_FKIndex1(answer_id)
);

DROP TABLE IF EXISTS contents_forms ;
CREATE TABLE contents_forms (
  form_id INTEGER UNSIGNED NOT NULL,
  content_id INTEGER UNSIGNED NOT NULL,
  inline ENUM('Y','N') NULL,
  PRIMARY KEY(form_id, content_id),
  INDEX forms_has_contents_FKIndex1(form_id),
  INDEX forms_has_contents_FKIndex2(content_id)
);


DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  content_id INTEGER UNSIGNED NOT NULL,
  ip VARCHAR(16) NULL,
  name VARCHAR(100) NULL,
  email VARCHAR(255) NULL,
  url VARCHAR(255) NULL,
  testo TEXT NULL,
  `status` ENUM('on','off','draft') NULL,
  created DATETIME NULL,
  modified DATETIME NULL,
  PRIMARY KEY(id),
  INDEX comments_FKIndex1(content_id)
);

DROP TABLE IF EXISTS banned_ips ;
CREATE TABLE banned_ips (
  ip VARCHAR(16) NOT NULL,
  PRIMARY KEY(ip)
);

DROP TABLE IF EXISTS multimedia_objects;
CREATE TABLE multimedia_objects (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  titolo VARCHAR(255) NULL,
  descrizione TEXT NULL,
  filePath VARCHAR(255) NULL,
  fileName VARCHAR(255) NULL,
  fileType VARCHAR(255) NULL,
  fileSize VARCHAR(255) NULL,
  `status` ENUM('on','off') NULL,
  created DATETIME NULL,
  PRIMARY KEY(id)
);

DROP TABLE IF EXISTS contents_multimedia_objects;
CREATE TABLE contents_multimedia_objects (
  object_id INTEGER UNSIGNED NOT NULL,
  tipo ENUM('firstImage','audioVideo','inline','attachment','fileStatico') NOT NULL,
  content_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(object_id, tipo, content_id),
  INDEX contents_objects_FKIndex2(object_id),
  INDEX contents_objects_FKIndex21(content_id)
);

DROP TABLE IF EXISTS administrators ;
CREATE TABLE administrators (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(32) NULL,
  passw VARCHAR(12) NULL,
  email VARCHAR(35) NULL,
  nome VARCHAR(30) NULL,
  cognome VARCHAR(30) NULL,
  `status` ENUM('on','off') NULL,
  PRIMARY KEY(id)
);

/* *************************

Importazioni vecchi dati 

************************* */

/* BEGIN --- ammnistratori */

INSERT INTO administrators
SELECT 
*
FROM BEDITA_OLD.bibl_users ; 


/* END --- ammnistratori */


/* BEGIN --- aree */
INSERT INTO areas 
SELECT 
ID,
'it' AS lang,
'site' AS tipo,
nome,
NULL,
areastatus
FROM BEDITA_OLD.aree ;

INSERT INTO lang_texts (source_id, lang, name_table, field_name, smallText)
SELECT 
id,
'it',
'areas',
'name',
name 
FROM areas
;

/* END --- aree */

/* BEGIN --- gruppi */
DROP TABLE IF EXISTS  tmpIDGroups ;
CREATE TABLE IF NOT EXISTS `tmpIDGroups` (
   `oldID` int(11) ,
   `ID` int(11),
   group_type_id INTEGER UNSIGNED NOT NULL
) ;

INSERT INTO `tmpIDGroups`
SELECT 
BEDITA_OLD.`aree`.ID, 
BEDITA_OLD.`aree`.ID,
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'sezione')
FROM BEDITA_OLD.`aree`
WHERE `refcatID` IS NOT NULL ;

INSERT INTO groups  
SELECT 
ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'sezione'),
areastatus AS status,
nome
FROM BEDITA_OLD.`aree`
WHERE `refcatID` IS NOT NULL ;


INSERT INTO lang_texts (source_id, lang, name_table, field_name, smallText)
SELECT 
ID,
'it',
'groups',
'name',
nome 
FROM BEDITA_OLD.`aree` 
WHERE `refcatID` IS NOT NULL ;

INSERT INTO areas_contents_groups 
SELECT 
refcatID AS IDArea,
NULL,
ID, 
prior,
NULL,  
NULL 
FROM BEDITA_OLD.`aree`
WHERE `refcatID` IS NOT NULL ;

INSERT INTO `tmpIDGroups`
SELECT 
ID,
(SELECT MAX( ID ) FROM BEDITA_CAKE.groups) + ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'categoria')
FROM BEDITA_OLD.`eventi_categorie` ;

INSERT INTO `groups`
SELECT 
tmpIDGroups.ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'categoria'),
old.status, 
old.nome 
FROM BEDITA_OLD.`eventi_categorie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'categoria');

INSERT INTO areas_contents_groups 
SELECT DISTINCT 
old.IDareaEvento AS IDArea,
NULL,  
tmpIDGroups.ID AS IDCategoria,
0 AS prior,
NULL,  
NULL 
FROM BEDITA_OLD.`eventi_categorie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'categoria');

INSERT INTO lang_texts (source_id, lang, name_table, field_name, smallText)
SELECT 
tmpIDGroups.ID,
'it',
'groups',
'name',
old.nome 
FROM BEDITA_OLD.`eventi_categorie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'categoria');

INSERT INTO `tmpIDGroups`
SELECT 
ID,
(SELECT MAX( ID ) FROM BEDITA_CAKE.groups) + ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'tipologia')
FROM BEDITA_OLD.`eventi_tipologie` ;

INSERT INTO `groups`
SELECT 
tmpIDGroups.ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'tipologia'),
'on', 
old.nome 
FROM BEDITA_OLD.`eventi_tipologie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'tipologia');

INSERT INTO lang_texts (source_id, lang, name_table, field_name, smallText)
SELECT 
tmpIDGroups.ID,
'it',
'groups',
'name',
old.nome 
FROM BEDITA_OLD.`eventi_tipologie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'tipologia');

INSERT INTO areas_contents_groups 
SELECT DISTINCT 
old.IDareaTipo AS IDArea,
NULL,  
tmpIDGroups.ID,
0 AS prior,
NULL,  
NULL 
FROM BEDITA_OLD.`eventi_tipologie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'tipologia');

/* BEGIN NOTA --- SOLO SALABORSA */
/*
Autori - grupppi - ingredienti.
ingredienti diventano groups di tipo 'soggetto' dell'area 'salaborsa ragazzi'.
Il gruppo 'autori bolognesi' diventa soggetto di 'salaborsa'
*/

INSERT INTO `tmpIDGroups`
SELECT 
BEDITA_OLD.`au_categorie`.ID,
(SELECT MAX( ID ) FROM BEDITA_CAKE.groups) + ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'soggetto')
FROM BEDITA_OLD.`au_categorie` ;

INSERT INTO `groups`
SELECT 
tmpIDGroups.ID, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'soggetto'),
old.status, 
old.nome 
FROM BEDITA_OLD.`au_categorie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'soggetto');

INSERT INTO lang_texts (source_id, lang, name_table, field_name, smallText)
SELECT 
tmpIDGroups.ID,
'it',
'groups',
'name',
old.nome
FROM BEDITA_OLD.`au_categorie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'soggetto');

INSERT INTO areas_contents_groups 
SELECT DISTINCT 
(SELECT id FROM areas WHERE name = 'bsb ragazzi'),
NULL,  
tmpIDGroups.ID,
0 AS prior,
NULL,  
NULL 
FROM BEDITA_OLD.`au_categorie` AS old INNER JOIN tmpIDGroups ON old.ID = tmpIDGroups.oldID AND group_type_id = (SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'soggetto');


INSERT INTO `tmpIDGroups`
SELECT 
10000,
(SELECT MAX( ID ) FROM BEDITA_CAKE.groups) + 1, 
(SELECT id FROM BEDITA_CAKE.group_types WHERE BEDITA_CAKE.group_types.name = 'soggetto')
 ;

INSERT INTO `groups`
SELECT 
tmpIDGroups.ID, 
(SELECT id FROM group_types WHERE name = 'soggetto'),
'on', 
'Autori Bolognesi' 
FROM tmpIDGroups WHERE oldID = 10000 ;


INSERT INTO lang_texts (source_id, lang, name_table, field_name, smallText)
SELECT 
tmpIDGroups.ID,
'it',
'groups',
'name',
'Autori Bolognesi' 
FROM tmpIDGroups WHERE oldID = 10000 ;


INSERT INTO areas_contents_groups 
SELECT DISTINCT 
(SELECT id FROM areas WHERE name = 'biblioteca sala borsa'),
NULL,  
tmpIDGroups.ID,
0 AS prior,
NULL,  
NULL 
FROM tmpIDGroups WHERE oldID = 10000 ;



/* END NOTA --- SOLO SALABORSA */


/* END --- gruppi */


/* BEGIN -- Creazione Viste gruppi */

DROP VIEW IF EXISTS view_tree_areas_groups ;
CREATE VIEW view_tree_areas_groups AS 
SELECT 
ACG.area_id,  
areas.lang AS area_lang,
groups.id, 
groups.status, 
groups.group_type_id AS type_id,
group_types.name AS type,
ACG.prior,
groups.name,
LT.id AS lang_id,
LT.lang,
LT.smallText AS nameLang 
FROM
areas_contents_groups AS ACG INNER JOIN groups ON ACG.group_id = groups.id
INNER JOIN areas ON ACG.area_id = areas.id 
INNER JOIN group_types ON groups.group_type_id = group_types.id
LEFT JOIN lang_texts AS LT ON groups.ID = LT.source_id AND name_table = 'groups' AND field_name = 'name'
ORDER BY area_id ;


DROP VIEW IF EXISTS view_categories ;
CREATE VIEW view_categories AS  
SELECT * FROM view_tree_areas_groups WHERE type = 'categoria' ORDER BY area_id, id ;

DROP VIEW IF EXISTS view_tipologies ;
CREATE VIEW view_tipologies AS  
SELECT * FROM view_tree_areas_groups WHERE type = 'tipologia' ORDER BY area_id, id ;

DROP VIEW IF EXISTS view_sections ;
CREATE VIEW view_sections AS  
SELECT * FROM view_tree_areas_groups WHERE type = 'sezione' ORDER BY area_id, id ;

DROP VIEW IF EXISTS view_subjects ;
CREATE VIEW view_subjects AS  
SELECT * FROM view_tree_areas_groups WHERE type = 'soggetto' ORDER BY area_id, id ;

/* END --- Creazione Viste gruppi */


/* BEGIN --- Creazione Viste contenuti */
DROP VIEW IF EXISTS view_short_contents ;
CREATE VIEW view_short_contents AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data,
contents.content_type_id,
content_types.name AS content_type, 
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM 
contents INNER JOIN content_types ON contents.content_type_id  = content_types.id
;

DROP VIEW IF EXISTS view_short_events ;
CREATE VIEW view_short_events AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data,
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM
contents
WHERE content_type_id = (SELECT id FROM content_types WHERE name = 'evento')
;

DROP VIEW IF EXISTS view_short_bibliographies ;
CREATE VIEW view_short_bibliographies AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data, 
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM
contents
WHERE content_type_id = (SELECT id FROM content_types WHERE name = 'biblio')
;

DROP VIEW IF EXISTS view_short_documents ;
CREATE VIEW view_short_documents AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data, 
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM
contents
WHERE content_type_id = (SELECT id FROM content_types WHERE name = 'doc')
;

DROP VIEW IF EXISTS view_short_galleries ;
CREATE VIEW view_short_galleries AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data, 
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM
contents
WHERE content_type_id = (SELECT id FROM content_types WHERE name = 'galleria')
;

DROP VIEW IF EXISTS view_short_news ;
CREATE VIEW view_short_news AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data, 
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM
contents
WHERE content_type_id = (SELECT id FROM content_types WHERE name = 'news')
;

DROP VIEW IF EXISTS view_short_authors ;
CREATE VIEW view_short_authors AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data, 
author_features.nome,
author_features.cognome,
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM
contents INNER JOIN author_features ON contents.id = author_features.content_id
WHERE content_type_id = (SELECT id FROM content_types WHERE name = 'autore')
;

DROP VIEW IF EXISTS view_short_books ;
CREATE VIEW view_short_books AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data, 
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM
contents
WHERE content_type_id = (SELECT id FROM content_types WHERE name = 'libro')
;

DROP VIEW IF EXISTS view_short_libraries ;
CREATE VIEW view_short_libraries AS 
SELECT
contents.ID,
contents.status,
contents.titolo,
contents.lang,
contents.inizio,
contents.fine,
contents.data, 
(SELECT IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)) AS valida
FROM
contents
WHERE content_type_id = (SELECT id FROM content_types WHERE name = 'libreria')
;

DROP VIEW IF EXISTS view_long_contents ;
CREATE VIEW view_long_contents AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_contents AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;

DROP VIEW IF EXISTS view_long_events ;
CREATE VIEW view_long_events AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_events AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;


DROP VIEW IF EXISTS view_long_bibliographies ;
CREATE VIEW view_long_bibliographies AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_bibliographies AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;

DROP VIEW IF EXISTS view_long_documents ;
CREATE VIEW view_long_documents AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_documents AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;

DROP VIEW IF EXISTS view_long_galleries ;
CREATE VIEW view_long_galleries AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_galleries AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;

DROP VIEW IF EXISTS view_long_news ;
CREATE VIEW view_long_news AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_news AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;

DROP VIEW IF EXISTS view_long_authors ;
CREATE VIEW view_long_authors AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_authors AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;

DROP VIEW IF EXISTS view_long_books ;
CREATE VIEW view_long_books AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_books AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;

DROP VIEW IF EXISTS view_long_libraries ;
CREATE VIEW view_long_libraries AS 
SELECT
V.*,
fileStatico.id AS filestatico_id,
fileStatico.status AS filestatico_status,
fileStatico.filePath AS filestatico_filePath,

firstImage.id AS firstImage_id,
firstImage.status AS firstImage_status,
firstImage.filePath AS firstImage_filePath,

audioVideo.id AS audioVideo_id,
audioVideo.status AS audioVideo_status,
audioVideo.filePath AS audioVideo_filePath

FROM
view_short_libraries AS V
LEFT JOIN contents_multimedia_objects AS CFS ON V.id = CFS.content_id AND CFS.tipo = 'fileStatico' LEFT JOIN multimedia_objects AS fileStatico ON CFS.object_id = fileStatico.id
LEFT JOIN contents_multimedia_objects AS CFS2 ON V.id = CFS2.content_id AND CFS2.tipo = 'firstImage' LEFT JOIN multimedia_objects AS firstImage ON CFS2.object_id = firstImage.id
LEFT JOIN contents_multimedia_objects AS CFS3 ON V.id = CFS3.content_id AND CFS3.tipo = 'audioVideo' LEFT JOIN multimedia_objects AS audioVideo ON CFS3.object_id = audioVideo.id
;

/*
Viste che esprimono la relazione tra i contenuti
*/
DROP VIEW IF EXISTS view_contents_galleries ;
CREATE VIEW view_contents_galleries AS 
SELECT DISTINCT 
contents.id,
contents_contents.inline,
galleries.id AS gallerie_id
FROM
contents INNER JOIN contents_contents ON contents.id = contents_contents.first_id
LEFT JOIN contents AS galleries ON contents_contents.second_id = galleries.id AND contents_contents.relation_type = 'brother'
WHERE 
contents.content_type_id <> (SELECT id FROM content_types where name = 'galleria')
AND
galleries.content_type_id = (SELECT id FROM content_types where name = 'galleria')
;

DROP VIEW IF EXISTS view_events_contents ;
CREATE VIEW  view_events_contents AS 
SELECT DISTINCT 
contents.id,
contents_contents.prior,
second_content.id AS second_id,
second_content.content_type_id AS second_type_id,
content_types.name AS second_type_name
FROM
contents INNER JOIN contents_contents ON contents.id = contents_contents.first_id
LEFT JOIN contents AS second_content ON contents_contents.second_id = second_content.id AND contents_contents.relation_type = 'brother'
LEFT JOIN content_types ON second_content.content_type_id  = content_types.id
WHERE 
contents.content_type_id = (SELECT id FROM content_types where name = 'evento')
AND
second_content.content_type_id IN (SELECT id FROM content_types where name = 'biblio' OR name = 'doc' OR name = 'librieria'  OR name = 'libro' )
;

DROP VIEW IF EXISTS view_biblios_biblios ;
CREATE VIEW view_biblios_biblios AS 
SELECT DISTINCT 
contents.id,
contents_contents.prior,
biblios.id AS child_id
FROM
contents INNER JOIN contents_contents ON contents.id = contents_contents.first_id
LEFT JOIN contents AS biblios ON contents_contents.second_id = biblios.id AND contents_contents.relation_type = 'parent'
WHERE 
contents.content_type_id = (SELECT id FROM content_types where name = 'biblio')
AND
biblios.content_type_id = (SELECT id FROM content_types where name = 'biblio')
ORDER BY id, prior
;

DROP VIEW IF EXISTS view_docs_docs ;
CREATE VIEW view_docs_docs AS 
SELECT DISTINCT 
contents.id,
contents_contents.prior,
docs.id AS child_id
FROM
contents INNER JOIN contents_contents ON contents.id = contents_contents.first_id
LEFT JOIN contents AS docs ON contents_contents.second_id = docs.id AND contents_contents.relation_type = 'parent'
WHERE 
contents.content_type_id = (SELECT id FROM content_types where name = 'doc')
AND
docs.content_type_id = (SELECT id FROM content_types where name = 'doc')
ORDER BY id, prior
;

DROP VIEW IF EXISTS view_authors_authors ;
CREATE VIEW view_authors_authors AS 
SELECT DISTINCT 
contents.id,
contents_contents.percento,
authors.id AS second_id
FROM
contents INNER JOIN contents_contents ON contents.id = contents_contents.first_id
LEFT JOIN contents AS authors ON contents_contents.second_id = authors.id AND contents_contents.relation_type = 'brother'
WHERE 
contents.content_type_id = (SELECT id FROM content_types where name = 'autore')
AND
authors.content_type_id = (SELECT id FROM content_types where name = 'autore')
UNION 
SELECT DISTINCT 
contents.id,
contents_contents.percento,
authors.id AS second_id
FROM
contents INNER JOIN contents_contents ON contents.id = contents_contents.second_id
LEFT JOIN contents AS authors ON contents_contents.first_id = authors.id AND contents_contents.relation_type = 'brother'
WHERE 
contents.content_type_id = (SELECT id FROM content_types where name = 'autore')
AND
authors.content_type_id = (SELECT id FROM content_types where name = 'autore')
ORDER BY id
;

DROP VIEW IF EXISTS view_biblio_contents ;
CREATE VIEW view_biblio_contents AS 
SELECT DISTINCT
biblio_contents.*,
view_long_books.status,
view_long_books.titolo,
view_long_books.lang,
view_long_books.valida,
view_long_books.filestatico_id,
view_long_books.filestatico_status,
view_long_books.filestatico_filePath,
view_long_books.firstImage_id,
view_long_books.firstImage_status,
view_long_books.firstImage_filePath,
view_long_books.audioVideo_id,
view_long_books.audioVideo_status,
view_long_books.audioVideo_filePath
	
FROM
biblio_contents LEFT JOIN view_long_books ON biblio_contents.book_id = view_long_books.id
ORDER BY content_id, biblio_contents.id
;

/* Vista che esrpiem la relazione tra contenuti e aree */
DROP VIEW IF EXISTS view_areas_contents ;
CREATE VIEW view_areas_contents AS 
SELECT  DISTINCT
ACG.area_id,
ACG2.content_id
FROM 
areas_contents_groups AS ACG INNER JOIN areas_contents_groups AS ACG2 ON ACG.group_id = ACG2.group_id  AND ACG.area_id IS NOT NULL AND ACG2.content_id IS NOT NULL
UNION
SELECT  DISTINCT
ACG3.area_id,
ACG3.content_id
FROM 
areas_contents_groups AS ACG3 
WHERE 
ACG3.area_id IS NOT NULL 
AND
ACG3.content_id IS NOT NULL

/* END --- Creazione Viste contenuti */

/* BEGIN --- Creazione tabelle FAQ */
DROP TABLE IF EXISTS areas_faqs_faq_groups ;
CREATE TABLE areas_faqs_faq_groups (
  faq_id INTEGER UNSIGNED NOT NULL,
  area_id INTEGER UNSIGNED NOT NULL,
  group_id INTEGER UNSIGNED NOT NULL,
  INDEX faq_categories_has_faqs_FKIndex2(faq_id),
  INDEX faqs_faq_categories_FKIndex2(group_id),
  INDEX faqs_faq_groups_FKIndex3(area_id)
) ;

DROP TABLE IF EXISTS lang_faqs ;
CREATE TABLE lang_faqs (
  second_id INTEGER UNSIGNED NOT NULL,
  first_id INTEGER UNSIGNED NOT NULL,
  INDEX lang_faqs_FKIndex1(first_id),
  INDEX lang_faqs_FKIndex2(second_id)
) ;

DROP TABLE IF EXISTS faqs ;
CREATE TABLE faqs (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  pubblica_id INTEGER UNSIGNED NOT NULL,
  lang CHAR(7) NULL,
  motivazione_id INTEGER UNSIGNED NOT NULL,
  studio_id INTEGER UNSIGNED NOT NULL,
  eta_id INTEGER UNSIGNED NOT NULL,
  faq_owner_id INTEGER UNSIGNED NOT NULL,
  nome VARCHAR(255) NULL,
  comune VARCHAR(255) NULL,
  email VARCHAR(255) NULL,
  dataDomanda DATETIME NULL,
  dataRisposta DATETIME NULL,
  vistato SET('N','S') NULL,
  spedito SET('N','S') NULL,
  fonti TEXT NULL,
  domanda TEXT NULL,
  risposta TEXT NULL,
  note TEXT NULL,
  minuti INTEGER UNSIGNED NULL,
  bibliotecario VARCHAR(255) NULL,
  provenienza VARCHAR(16) NULL,
  dataModifica DATETIME NULL,
  pubblicata SET('S','N') NULL,
  PRIMARY KEY(id),
  INDEX faqs_FKIndex1(faq_owner_id),
  INDEX faqs_FKIndex2(pubblica_id),
  INDEX faqs_FKIndex3(eta_id),
  INDEX faqs_FKIndex4(studio_id),
  INDEX faqs_FKIndex5(motivazione_id)
);

DROP TABLE IF EXISTS faq_statistic_fields ;
CREATE TABLE faq_statistic_fields (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  label VARCHAR(255) NULL,
  switch SET('eta','studio','motivazione') NULL,
  PRIMARY KEY(id)
);

DROP TABLE IF EXISTS faq_owners ;
CREATE TABLE faq_owners (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(255) NULL,
  PRIMARY KEY(id)
); 

/* END --- Creazione tabelle FAQ */


