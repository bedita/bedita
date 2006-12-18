
DROP TABLE IF EXISTS `areas`;
CREATE TABLE IF NOT EXISTS `areas` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `servername` varchar(255) default NULL,
  `status` enum('on','off','private','hidden') NOT NULL default 'on',
  PRIMARY KEY  (`id`),
  KEY `IDXName` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `bibl_users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `passw` varchar(12) NOT NULL default '',
  `email` varchar(35) default NULL,
  `nome` varchar(30) default NULL,
  `cognome` varchar(30) default NULL,
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) TYPE=MyISAM AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `contenuti`
-- 

DROP TABLE IF EXISTS `contents`;
CREATE TABLE IF NOT EXISTS `contents` (
  `id` int(11) NOT NULL auto_increment,
  `titolo` varchar(255) NOT NULL default '',
  `sottotitolo` varchar(255) NOT NULL default '',
  `testo` text,
  `data` date NOT NULL default '0000-00-00',
  `status` set('on','off','draft') NOT NULL default 'draft',
  `scadenza` date default NULL,
  `testolungo` text,
  `tipo` enum('evento','doc','biblio','galleria') NOT NULL default 'evento',
  `user_id` int(11) default NULL,
  `userName` tinytext,
  PRIMARY KEY  (`id`),
  KEY `status` (`status`),
  KEY `tipo` (`tipo`),
  FULLTEXT KEY `idxcerca` (`titolo`,`sottotitolo`,`testo`,`testolungo`)
) TYPE=MyISAM AUTO_INCREMENT=1414 ;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `contenuti_gruppi`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `tipo` enum('categoria','tipologia','sezione') NOT NULL default 'sezione',
  `nome` varchar(255) NOT NULL default '0',
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `contenuti_aree_gruppi`
-- 

DROP TABLE IF EXISTS `areas_contents_groups`;
CREATE TABLE IF NOT EXISTS `areas_contents_groups` (
  `area_id` int(11) default NULL,
  `content_id` int(11) default NULL,
  `group_id` int(11) default NULL,
  `child_id` int(11) default NULL,
  `prior` int(11) default NULL,
  `inline` enum('Y','N') default NULL
) TYPE=MyISAM;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `g_multimedia`
-- 

DROP TABLE IF EXISTS `objects`;
CREATE TABLE IF NOT EXISTS `objects` (
  `id` int(11) NOT NULL auto_increment,
  `titolo` varchar(64) NOT NULL default '',
  `descrizione` text,
  `fileName` varchar(48) default NULL,
  `fileType` varchar(48) default NULL,
  `status` enum('on','off') NOT NULL default 'on',
  `data` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=106 ;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `g_multimedia`
-- 

DROP TABLE IF EXISTS `contents_objects`;
CREATE TABLE IF NOT EXISTS `contents_objects` (
  `content_id` int(11) default NULL,
  `object_id` int(11) default NULL,
  `prior` int(2) default NULL,
  `type` enum('firstImage','audioVideo','inline','attachment') NOT NULL default 'on'
) TYPE=MyISAM ;



-- --------------------------------------------------------

-- 
-- Importazione vecchi dati dati
-- 

/* aree */
DROP TABLE IF EXISTS `areas`;
CREATE TABLE IF NOT EXISTS `areas` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `servername` varchar(255) default NULL,
  `status` enum('on','off','private','hidden') NOT NULL default 'on',
  PRIMARY KEY  (`id`),
  KEY `IDXName` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=39 ;

INSERT INTO areas SELECT * FROM aree ;



/* gruppi */
DROP TABLE IF EXISTS  tmp_gruppi ;
CREATE TABLE IF NOT EXISTS `tmp_gruppi` (
  `id` int(11) NOT NULL auto_increment,
  `IDOld` int(11) NOT NULL ,
  `tipo` enum('categoria','tipologia','sezione') NOT NULL default 'sezione',
  `nome` varchar(255) NOT NULL default '0',
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `tmp_gruppi` (IDOld, tipo, nome, status)
SELECT  ID AS IDOld, tipo, nome, status FROM contenuti_gruppi ;

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL auto_increment,
  `tipo` enum('categoria','tipologia','sezione') NOT NULL default 'sezione',
  `nome` varchar(255) NOT NULL default '0',
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

INSERT INTO `groups`
SELECT id, tipo, nome, `status` FROM `tmp_gruppi` ;


/* contenuti */
DROP TABLE IF EXISTS `contents`;
CREATE TABLE IF NOT EXISTS `contents` (
  `id` int(11) NOT NULL auto_increment,
  `titolo` varchar(255) NOT NULL default '',
  `sottotitolo` varchar(255) NOT NULL default '',
  `testo` text,
  `data` date NOT NULL default '0000-00-00',
  `status` set('on','off','draft') NOT NULL default 'draft',
  `scadenza` date default NULL,
  `testolungo` text,
  `tipo` enum('evento','doc','biblio','galleria') NOT NULL default 'evento',
  `user_id` int(11) default NULL,
  `userName` tinytext,
  PRIMARY KEY  (`id`),
  KEY `status` (`status`),
  KEY `tipo` (`tipo`),
  FULLTEXT KEY `idxcerca` (`titolo`,`sottotitolo`,`testo`,`testolungo`)
) TYPE=MyISAM AUTO_INCREMENT=1414 ;


INSERT INTO contents
SELECT 
id, 
`titolo`,
`sottotitolo`, 
`testo`,
`data`,
`status`,
`scadenza`,
`testolungo`,
`tipo`,
`authorID`,
`authorName`
FROM 
contenuti ;

/* Associazioni aree, gruppi, contentui, child */
DROP TABLE IF EXISTS `areas_contents_groups`;
CREATE TABLE IF NOT EXISTS `areas_contents_groups` (
  `area_id` int(11) default NULL,
  `content_id` int(11) default NULL,
  `group_id` int(11) default NULL,
  `child_id` int(11) default NULL,
  `prior` int(11) default NULL,
  `inline` enum('Y','N') default NULL
) TYPE=MyISAM;

INSERT INTO areas_contents_groups 
SELECT DISTINCT 
contenuti_aree_gruppi.IDArea,
contenuti_aree_gruppi.IDContenuto,
tmp_gruppi.id AS IDGruppo,
contenuti_aree_gruppi.IDChild,
contenuti_aree_gruppi.prior,
contenuti_aree_gruppi.inline
FROM 
contenuti_aree_gruppi LEFT JOIN tmp_gruppi ON contenuti_aree_gruppi.IDGruppo = tmp_gruppi.IDOld AND contenuti_aree_gruppi.TipoGruppo = tmp_gruppi.tipo


/* User */
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `passw` varchar(12) NOT NULL default '',
  `email` varchar(35) default NULL,
  `nome` varchar(30) default NULL,
  `cognome` varchar(30) default NULL,
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) TYPE=MyISAM AUTO_INCREMENT=22 ;

INSERT INTO users SELECT * FROM biblio_users ;


/* Oggetti multimediali */
DROP TABLE IF EXISTS `objects`;
CREATE TABLE IF NOT EXISTS `objects` (
  `id` int(11) NOT NULL auto_increment,
  `titolo` varchar(64) NOT NULL default '',
  `descrizione` text,
  `fileName` varchar(48) default NULL,
  `fileType` varchar(48) default NULL,
  `status` enum('on','off') NOT NULL default 'on',
  `data` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=106 ;


INSERT INTO objects
SELECT 
ID, titolo, descrizione, fileName, fileType, status, data
FROM g_multimedia ;

INSERT INTO objects (descrizione, fileName, status)
SELECT 
NULL AS dida, 
contenuti.immagine AS fileName,
'on' AS status 
FROM 
contenuti 
WHERE 
contenuti.immagine IS NOT NULL ;

INSERT INTO objects (descrizione, fileName, status)
SELECT 
NULL AS dida, 
contenuti.audioFile AS fileName,
'on' AS status 
FROM 
contenuti 
WHERE 
contenuti.audioFile IS NOT NULL ;

/* inserisce le associazioni */
DROP TABLE IF EXISTS `contents_objects`;
CREATE TABLE IF NOT EXISTS `contents_objects` (
  `content_id` int(11) default NULL,
  `object_id` int(11) default NULL,
  `prior` int(2) default NULL,
  `type` enum('firstImage','audioVideo','inline','attachment') NOT NULL default 'firstImage'
) TYPE=MyISAM ;

/* contenuti - immagine principale */
INSERT INTO contents_objects 
SELECT
contenuti.ID, 
objects.id, 
NULL, 
'firstImage'
FROM
contenuti INNER JOIN objects ON contenuti.immagine = objects.fileName
WHERE
contenuti.immagine IS NOT NULL ;

/* contenuti - audioVideo file */
INSERT INTO contents_objects 
SELECT
contenuti.ID, 
objects.id, 
NULL, 
'audioVideo'
FROM
contenuti INNER JOIN objects ON contenuti.audioFile = objects.fileName
WHERE
contenuti.audioFile IS NOT NULL ;

/* contenuti - immagini */
INSERT INTO contents_objects 
SELECT 
IDContenuto,
ID, 
prior,
'inline'
FROM g_multimedia 
WHERE 
inline = 1

/* contenuti - attachemnet */
INSERT INTO contents_objects 
SELECT 
IDContenuto,
ID, 
prior,
'attachment'
FROM g_multimedia 
WHERE 
inline = 0

