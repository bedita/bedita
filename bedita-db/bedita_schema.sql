-- --------------------------------------------------------

-- 
-- Struttura della tabella `acos`
-- 

DROP TABLE IF EXISTS `acos`;
CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(11) NOT NULL auto_increment,
  `object_id` int(11) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `lft` int(11) default NULL,
  `rght` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `administrators`
-- 

DROP TABLE IF EXISTS `administrators`;
CREATE TABLE IF NOT EXISTS `administrators` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(32) default NULL,
  `passw` varchar(12) default NULL,
  `email` varchar(35) default NULL,
  `nome` varchar(30) default NULL,
  `cognome` varchar(30) default NULL,
  `status` enum('on','off') default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `answers`
-- 

DROP TABLE IF EXISTS `answers`;
CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `querie_id` int(10) unsigned NOT NULL,
  `testo` varchar(255) default NULL,
  `prior` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `answers_FKIndex1` (`querie_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=100 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `areas`
-- 

DROP TABLE IF EXISTS `areas`;
CREATE TABLE IF NOT EXISTS `areas` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lang` char(7) default NULL,
  `tipo` set('site','newsletter') default NULL,
  `name` varchar(255) default NULL,
  `servername` varchar(255) default NULL,
  `status` enum('on','off','private','hidden') default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `areas_contents_groups`
-- 

DROP TABLE IF EXISTS `areas_contents_groups`;
CREATE TABLE IF NOT EXISTS `areas_contents_groups` (
  `area_id` int(10) unsigned default NULL,
  `content_id` int(10) unsigned default NULL,
  `group_id` int(10) unsigned default NULL,
  `prior` int(10) unsigned default NULL,
  `percento` int(10) unsigned default NULL,
  `inline` enum('Y','N') default NULL,
  KEY `areas_contents_groups_FKIndex1` (`group_id`),
  KEY `areas_contents_groups_FKIndex2` (`content_id`),
  KEY `areas_contents_groups_FKIndex3` (`area_id`),
  KEY `areas_contents_groups_FKIndex12` (`area_id`,`group_id`),
  KEY `areas_contents_groups_FKIndex13` (`area_id`,`content_id`),
  KEY `areas_contents_groups_FKIndex14` (`area_id`,`group_id`,`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `areas_faqs_faq_groups`
-- 

DROP TABLE IF EXISTS `areas_faqs_faq_groups`;
CREATE TABLE IF NOT EXISTS `areas_faqs_faq_groups` (
  `faq_id` int(10) unsigned NOT NULL,
  `area_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  KEY `faq_categories_has_faqs_FKIndex2` (`faq_id`),
  KEY `faqs_faq_categories_FKIndex2` (`group_id`),
  KEY `faqs_faq_groups_FKIndex3` (`area_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- 
-- Struttura della tabella `aros`
-- 

DROP TABLE IF EXISTS `aros`;
CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(11) NOT NULL auto_increment,
  `foreign_key` int(11) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `lft` int(11) default NULL,
  `rght` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `aros_acos`
-- 

DROP TABLE IF EXISTS `aros_acos`;
CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(11) NOT NULL auto_increment,
  `aro_id` int(11) default NULL,
  `aco_id` int(11) default NULL,
  `_create` int(11) NOT NULL default '0',
  `_read` int(11) NOT NULL default '0',
  `_update` int(11) NOT NULL default '0',
  `_delete` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `author_features`
-- 

DROP TABLE IF EXISTS `author_features`;
CREATE TABLE IF NOT EXISTS `author_features` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content_id` int(10) unsigned NOT NULL,
  `nome` varchar(32) default NULL,
  `cognome` varchar(32) default NULL,
  `poetica` text,
  `biografia` text,
  `stringa` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `library_characteristics_FKIndex1` (`content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=203 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `banned_ips`
-- 

DROP TABLE IF EXISTS `banned_ips`;
CREATE TABLE IF NOT EXISTS `banned_ips` (
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- 
-- Struttura della tabella `biblio_contents`
-- 

DROP TABLE IF EXISTS `biblio_contents`;
CREATE TABLE IF NOT EXISTS `biblio_contents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `multimedia_object_id` int(10) unsigned default NULL,
  `book_id` int(10) unsigned default NULL,
  `content_id` int(10) unsigned NOT NULL,
  `testo` text,
  `codice_ricerca` varchar(255) default NULL,
  `switch` set('txt','book') default NULL,
  PRIMARY KEY  (`id`),
  KEY `biblio_contents_FKIndex2` (`book_id`),
  KEY `biblio_contents_FKIndex3` (`multimedia_object_id`),
  KEY `biblio_contents_FKIndex4` (`content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `calendars`
-- 

DROP TABLE IF EXISTS `calendars`;
CREATE TABLE IF NOT EXISTS `calendars` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content_id` int(10) unsigned NOT NULL,
  `inizio` date default NULL,
  `fine` date default NULL,
  PRIMARY KEY  (`id`,`content_id`),
  KEY `calendars_FKIndex1` (`content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=477 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `comments`
-- 

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content_id` int(10) unsigned NOT NULL,
  `ip` varchar(16) default NULL,
  `name` varchar(100) default NULL,
  `email` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `testo` text,
  `status` enum('on','off','draft') default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `comments_FKIndex1` (`content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;


-- 
-- Struttura della tabella `content_types`
-- 

DROP TABLE IF EXISTS `content_types`;
CREATE TABLE IF NOT EXISTS `content_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;


-- 
-- Struttura della tabella `contents`
-- 

DROP TABLE IF EXISTS `contents`;
CREATE TABLE IF NOT EXISTS `contents` (
  `id` int(10) unsigned NOT NULL,
  `content_type_id` int(10) unsigned NOT NULL,
  `administrator_id` int(10) unsigned default NULL,
  `status` set('on','off','draft') default NULL,
  `data` date default NULL,
  `inizio` date default NULL,
  `fine` date default NULL,
  `lang` char(7) default NULL,
  `titolo` varchar(255) default NULL,
  `sottotitolo` varchar(255) default NULL,
  `testo` text,
  `testolungo` text,
  `administrator_nome` varchar(255) default NULL,
  `allow_comments` set('Y','N') default 'Y',
  PRIMARY KEY  (`id`),
  KEY `contents_FKIndex1` (`administrator_id`),
  KEY `contents_FKIndex2` (`content_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `contents_contents`
-- 

DROP TABLE IF EXISTS `contents_contents`;
CREATE TABLE IF NOT EXISTS `contents_contents` (
  `first_id` int(10) unsigned NOT NULL auto_increment,
  `second_id` int(10) unsigned NOT NULL default '0',
  `relation_type` set('parent','lang','brother') default NULL,
  `percento` int(10) unsigned default NULL,
  `prior` int(10) unsigned default NULL,
  `inline` enum('Y','N') default NULL,
  PRIMARY KEY  (`first_id`,`second_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1803 ;


-- 
-- Struttura della tabella `contents_forms`
-- 

DROP TABLE IF EXISTS `contents_forms`;
CREATE TABLE IF NOT EXISTS `contents_forms` (
  `form_id` int(10) unsigned NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  `inline` enum('Y','N') default NULL,
  PRIMARY KEY  (`form_id`,`content_id`),
  KEY `forms_has_contents_FKIndex1` (`form_id`),
  KEY `forms_has_contents_FKIndex2` (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `contents_multimedia_objects`
-- 

DROP TABLE IF EXISTS `contents_multimedia_objects`;
CREATE TABLE IF NOT EXISTS `contents_multimedia_objects` (
  `object_id` int(10) unsigned NOT NULL,
  `tipo` enum('firstImage','audioVideo','inline','attachment','fileStatico') NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`object_id`,`tipo`,`content_id`),
  KEY `contents_objects_FKIndex2` (`object_id`),
  KEY `contents_objects_FKIndex21` (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `faq_owners`
-- 

DROP TABLE IF EXISTS `faq_owners`;
CREATE TABLE IF NOT EXISTS `faq_owners` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nome` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `faq_statistic_fields`
-- 

DROP TABLE IF EXISTS `faq_statistic_fields`;
CREATE TABLE IF NOT EXISTS `faq_statistic_fields` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(255) default NULL,
  `switch` set('eta','studio','motivazione') default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;


-- --------------------------------------------------------

-- 
-- Struttura della tabella `faqs`
-- 

DROP TABLE IF EXISTS `faqs`;
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL,
  `lang` char(7) default NULL,
  `motivazione_id` int(10) unsigned NOT NULL,
  `studio_id` int(10) unsigned NOT NULL,
  `eta_id` int(10) unsigned NOT NULL,
  `faq_owner_id` int(10) unsigned NOT NULL,
  `nome` varchar(255) default NULL,
  `comune` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `telefono` varchar(30) NOT NULL,
  `dataDomanda` datetime default NULL,
  `dataRisposta` datetime default NULL,
  `vistato` set('N','S') default NULL,
  `spedito` set('N','S') default NULL,
  `fonti` text,
  `domanda` text,
  `risposta` text,
  `note` text,
  `minuti` int(10) unsigned default NULL,
  `bibliotecario` varchar(255) default NULL,
  `provenienza` varchar(16) default NULL,
  `dataModifica` datetime default NULL,
  `pubblicata` set('S','N') default NULL,
  PRIMARY KEY  (`id`),
  KEY `faqs_FKIndex1` (`faq_owner_id`),
  KEY `faqs_FKIndex2` (`parent_id`),
  KEY `faqs_FKIndex3` (`eta_id`),
  KEY `faqs_FKIndex4` (`studio_id`),
  KEY `faqs_FKIndex5` (`motivazione_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2659 ;

-- --------------------------------------------------------
-- 
-- Struttura della tabella `forms`
-- 

DROP TABLE IF EXISTS `forms`;
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `titolo` varchar(255) default NULL,
  `descrizione` text,
  `dataInsert` datetime default NULL,
  `status` enum('on','off') default NULL,
  `author` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `group_types`
-- 

DROP TABLE IF EXISTS `group_types`;
CREATE TABLE IF NOT EXISTS `group_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;



-- 
-- Struttura della tabella `groups`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_type_id` int(10) unsigned NOT NULL,
  `status` set('on','off') default NULL,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `groups_FKIndex1` (`group_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=128 ;


-- 
-- Struttura della tabella `lang_faqs`
-- 

DROP TABLE IF EXISTS `lang_faqs`;
CREATE TABLE IF NOT EXISTS `lang_faqs` (
  `second_id` int(10) unsigned NOT NULL,
  `first_id` int(10) unsigned NOT NULL,
  KEY `lang_faqs_FKIndex1` (`first_id`),
  KEY `lang_faqs_FKIndex2` (`second_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- 
-- Struttura della tabella `lang_texts`
-- 

DROP TABLE IF EXISTS `lang_texts`;
CREATE TABLE IF NOT EXISTS `lang_texts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lang` char(7) NOT NULL,
  `source_id` int(10) unsigned NOT NULL,
  `name_table` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `smallText` varchar(255) default NULL,
  `text` text,
  PRIMARY KEY  (`id`),
  KEY `lang_texts_FKIndex1` (`source_id`),
  KEY `lang_texts_FKIndex4` (`source_id`,`name_table`,`field_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=89 ;

-- 
-- Struttura della tabella `links`
-- 

DROP TABLE IF EXISTS `links`;
CREATE TABLE IF NOT EXISTS `links` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `obj_id` int(10) unsigned NOT NULL,
  `obj_type` set('content','timeline','cartiglio') default NULL,
  `link_swtich` set('url','coord','mapsgoogle') default NULL,
  `description` tinytext NOT NULL,
  `title` varchar(255) default NULL,
  `link_status` set('on','off','draf') default NULL,
  `url1` varchar(255) default NULL,
  `url2` varchar(255) default NULL,
  `url3` varchar(255) default NULL,
  `coord1` double default NULL,
  `coord2` double default NULL,
  `coord3` double default NULL,
  `target` varchar(40) default NULL,
  PRIMARY KEY  (`id`),
  KEY `links_FKIndex1` (`obj_id`,`obj_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=333 ;


-- 
-- Struttura della tabella `modules`
-- 

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(10) NOT NULL auto_increment,
  `label` varchar(32) default NULL,
  `color` varchar(7) default NULL,
  `path` varchar(16) default NULL,
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;


-- 
-- Struttura della tabella `modules_users`
-- 

DROP TABLE IF EXISTS `modules_users`;
CREATE TABLE IF NOT EXISTS `modules_users` (
  `user_id` int(11) NOT NULL default '0',
  `module_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- 
-- Struttura della tabella `multimedia_objects`
-- 

DROP TABLE IF EXISTS `multimedia_objects`;
CREATE TABLE IF NOT EXISTS `multimedia_objects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `titolo` varchar(255) default NULL,
  `descrizione` text,
  `filePath` varchar(255) default NULL,
  `fileName` varchar(255) default NULL,
  `fileType` varchar(255) default NULL,
  `fileSize` varchar(255) default NULL,
  `status` enum('on','off') default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5902 ;


-- 
-- Struttura della tabella `queries`
-- 

DROP TABLE IF EXISTS `queries`;
CREATE TABLE IF NOT EXISTS `queries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `form_id` int(10) unsigned NOT NULL,
  `lang` char(7) default NULL,
  `testo` varchar(255) default NULL,
  `prior` int(10) unsigned default NULL,
  `status` set('on','off') default NULL,
  `max_char` int(10) unsigned default NULL,
  `max_val` int(10) unsigned default NULL,
  `tipo` enum('multipleChoise','singleChoise','openText','checkOpen','grade') default NULL,
  PRIMARY KEY  (`id`),
  KEY `queries_FKIndex1` (`form_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=96 ;

-- 
-- Struttura della tabella `results`
-- 

DROP TABLE IF EXISTS `results`;
CREATE TABLE IF NOT EXISTS `results` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `answer_id` int(10) unsigned NOT NULL,
  `session` varchar(255) default NULL,
  `IP` varchar(16) default NULL,
  `dataInsert` datetime default NULL,
  `txt` varchar(255) default NULL,
  `value` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `results_FKIndex1` (`answer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;


-- 
-- Struttura della tabella `tmp`
-- 

DROP TABLE IF EXISTS `tmp`;
CREATE TABLE IF NOT EXISTS `tmp` (
  `id` int(11) default NULL,
  `titolo` varchar(255) default NULL,
  `descrizione` text,
  `filePath` varchar(255) default NULL,
  `fileName` varchar(255) default NULL,
  `fileType` varchar(255) default NULL,
  `fileSize` varchar(255) default NULL,
  `status` enum('on','off') default NULL,
  `created` datetime default NULL,
  `tab_name` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- 
-- Struttura della tabella `tmpIDContents`
-- 

DROP TABLE IF EXISTS `tmpIDContents`;
CREATE TABLE IF NOT EXISTS `tmpIDContents` (
  `oldID` int(11) default NULL,
  `ID` int(11) default NULL,
  `content_type_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- 
-- Struttura della tabella `tmpIDGroups`
-- 

DROP TABLE IF EXISTS `tmpIDGroups`;
CREATE TABLE IF NOT EXISTS `tmpIDGroups` (
  `oldID` int(11) default NULL,
  `ID` int(11) default NULL,
  `group_type_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Struttura della tabella `tmpIDMultimedia`
-- 

DROP TABLE IF EXISTS `tmpIDMultimedia`;
CREATE TABLE IF NOT EXISTS `tmpIDMultimedia` (
  `oldID` int(11) default NULL,
  `ID` int(11) default NULL,
  `tab_name` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- 
-- Struttura della tabella `users`
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;


-- 
-- Struttura della tabella `view_areas_contents`
-- 

DROP VIEW IF EXISTS `view_areas_contents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_areas_contents` AS select distinct `ACG`.`area_id` AS `area_id`,`ACG2`.`content_id` AS `content_id` from (`areas_contents_groups` `ACG` join `areas_contents_groups` `ACG2` on(((`ACG`.`group_id` = `ACG2`.`group_id`) and (`ACG`.`area_id` is not null) and (`ACG2`.`content_id` is not null)))) union select distinct `ACG3`.`area_id` AS `area_id`,`ACG3`.`content_id` AS `content_id` from `areas_contents_groups` `ACG3` where ((`ACG3`.`area_id` is not null) and (`ACG3`.`content_id` is not null));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_authors_authors`
-- 

DROP VIEW IF EXISTS `view_authors_authors`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_authors_authors` AS select distinct `contents`.`id` AS `id`,`contents_contents`.`percento` AS `percento`,`authors`.`id` AS `second_id` from ((`contents` join `contents_contents` on((`contents`.`id` = `contents_contents`.`first_id`))) left join `contents` `authors` on(((`contents_contents`.`second_id` = `authors`.`id`) and (`contents_contents`.`relation_type` = _latin1'brother')))) where ((`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'autore'))) and (`authors`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'autore')))) union select distinct `contents`.`id` AS `id`,`contents_contents`.`percento` AS `percento`,`authors`.`id` AS `second_id` from ((`contents` join `contents_contents` on((`contents`.`id` = `contents_contents`.`second_id`))) left join `contents` `authors` on(((`contents_contents`.`first_id` = `authors`.`id`) and (`contents_contents`.`relation_type` = _latin1'brother')))) where ((`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'autore'))) and (`authors`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'autore')))) order by `id`;

-- 
-- Struttura della tabella `view_short_books`
-- 

DROP VIEW IF EXISTS `view_short_books`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_books` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from `contents` where (`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'libro')));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_long_books`
-- 

DROP VIEW IF EXISTS `view_long_books`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_books` AS select `v`.`id` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_books` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_biblio_contents`
-- 

DROP VIEW IF EXISTS `view_biblio_contents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_biblio_contents` AS select distinct `biblio_contents`.`id` AS `id`,`biblio_contents`.`multimedia_object_id` AS `multimedia_object_id`,`biblio_contents`.`book_id` AS `book_id`,`biblio_contents`.`content_id` AS `content_id`,`biblio_contents`.`testo` AS `testo`,`biblio_contents`.`codice_ricerca` AS `codice_ricerca`,`biblio_contents`.`switch` AS `switch`,`view_long_books`.`status` AS `status`,`view_long_books`.`titolo` AS `titolo`,`view_long_books`.`lang` AS `lang`,`view_long_books`.`valida` AS `valida`,`view_long_books`.`filestatico_id` AS `filestatico_id`,`view_long_books`.`filestatico_status` AS `filestatico_status`,`view_long_books`.`filestatico_filePath` AS `filestatico_filePath`,`view_long_books`.`firstImage_id` AS `firstImage_id`,`view_long_books`.`firstImage_status` AS `firstImage_status`,`view_long_books`.`firstImage_filePath` AS `firstImage_filePath`,`view_long_books`.`audioVideo_id` AS `audioVideo_id`,`view_long_books`.`audioVideo_status` AS `audioVideo_status`,`view_long_books`.`audioVideo_filePath` AS `audioVideo_filePath` from (`biblio_contents` left join `view_long_books` on((`biblio_contents`.`book_id` = `view_long_books`.`ID`))) order by `biblio_contents`.`content_id`,`biblio_contents`.`id`;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_biblios_biblios`
-- 

DROP VIEW IF EXISTS `view_biblios_biblios`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_biblios_biblios` AS select distinct `contents`.`id` AS `id`,`contents_contents`.`prior` AS `prior`,`biblios`.`id` AS `child_id` from ((`contents` join `contents_contents` on((`contents`.`id` = `contents_contents`.`first_id`))) left join `contents` `biblios` on(((`contents_contents`.`second_id` = `biblios`.`id`) and (`contents_contents`.`relation_type` = _latin1'parent')))) where ((`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'biblio'))) and (`biblios`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'biblio')))) order by `contents`.`id`,`contents_contents`.`prior`;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_tree_areas_groups`
-- 

DROP VIEW IF EXISTS `view_tree_areas_groups`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_tree_areas_groups` AS select `ACG`.`area_id` AS `area_id`,`areas`.`lang` AS `area_lang`,`groups`.`id` AS `id`,`groups`.`status` AS `status`,`groups`.`group_type_id` AS `type_id`,`group_types`.`name` AS `type`,`ACG`.`prior` AS `prior`,`groups`.`name` AS `name`,`LT`.`id` AS `lang_id`,`LT`.`lang` AS `lang`,`LT`.`smallText` AS `nameLang` from ((((`areas_contents_groups` `ACG` join `groups` on((`ACG`.`group_id` = `groups`.`id`))) join `areas` on((`ACG`.`area_id` = `areas`.`id`))) join `group_types` on((`groups`.`group_type_id` = `group_types`.`id`))) left join `lang_texts` `LT` on(((`groups`.`id` = `LT`.`source_id`) and (`LT`.`name_table` = _latin1'groups') and (`LT`.`field_name` = _latin1'name')))) order by `ACG`.`area_id`;


-- 
-- Struttura della tabella `view_categories`
-- 

DROP VIEW IF EXISTS `view_categories`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_categories` AS select `view_tree_areas_groups`.`area_id` AS `area_id`,`view_tree_areas_groups`.`area_lang` AS `area_lang`,`view_tree_areas_groups`.`id` AS `id`,`view_tree_areas_groups`.`status` AS `status`,`view_tree_areas_groups`.`type_id` AS `type_id`,`view_tree_areas_groups`.`type` AS `type`,`view_tree_areas_groups`.`prior` AS `prior`,`view_tree_areas_groups`.`name` AS `name`,`view_tree_areas_groups`.`lang_id` AS `lang_id`,`view_tree_areas_groups`.`lang` AS `lang`,`view_tree_areas_groups`.`nameLang` AS `nameLang` from `view_tree_areas_groups` where (`view_tree_areas_groups`.`type` = _latin1'categoria') order by `view_tree_areas_groups`.`area_id`,`view_tree_areas_groups`.`id`;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_contents_galleries`
-- 

DROP VIEW IF EXISTS `view_contents_galleries`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_contents_galleries` AS select distinct `contents`.`id` AS `id`,`contents_contents`.`inline` AS `inline`,`galleries`.`id` AS `gallerie_id` from ((`contents` join `contents_contents` on((`contents`.`id` = `contents_contents`.`first_id`))) left join `contents` `galleries` on(((`contents_contents`.`second_id` = `galleries`.`id`) and (`contents_contents`.`relation_type` = _latin1'brother')))) where ((`contents`.`content_type_id` <> (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'galleria'))) and (`galleries`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'galleria'))));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_docs_docs`
-- 

DROP VIEW IF EXISTS `view_docs_docs`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_docs_docs` AS select distinct `contents`.`id` AS `id`,`contents_contents`.`prior` AS `prior`,`docs`.`id` AS `child_id` from ((`contents` join `contents_contents` on((`contents`.`id` = `contents_contents`.`first_id`))) left join `contents` `docs` on(((`contents_contents`.`second_id` = `docs`.`id`) and (`contents_contents`.`relation_type` = _latin1'parent')))) where ((`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'doc'))) and (`docs`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'doc')))) order by `contents`.`id`,`contents_contents`.`prior`;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_events_contents`
-- 

DROP VIEW IF EXISTS `view_events_contents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_events_contents` AS select distinct `contents`.`id` AS `id`,`contents_contents`.`prior` AS `prior`,`second_content`.`id` AS `second_id`,`second_content`.`content_type_id` AS `second_type_id`,`content_types`.`name` AS `second_type_name` from (((`contents` join `contents_contents` on((`contents`.`id` = `contents_contents`.`first_id`))) left join `contents` `second_content` on(((`contents_contents`.`second_id` = `second_content`.`id`) and (`contents_contents`.`relation_type` = _latin1'brother')))) left join `content_types` on((`second_content`.`content_type_id` = `content_types`.`id`))) where ((`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'evento'))) and `second_content`.`content_type_id` in (select `content_types`.`id` AS `id` from `content_types` where ((`content_types`.`name` = _latin1'biblio') or (`content_types`.`name` = _latin1'doc') or (`content_types`.`name` = _latin1'librieria') or (`content_types`.`name` = _latin1'libro'))));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_short_authors`
-- 

DROP VIEW IF EXISTS `view_short_authors`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_authors` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,`author_features`.`nome` AS `nome`,`author_features`.`cognome` AS `cognome`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from (`contents` join `author_features` on((`contents`.`id` = `author_features`.`content_id`))) where (`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'autore')));

-- --------------------------------------------------------
-- 
-- Struttura della tabella `view_long_authors`
-- 

DROP VIEW IF EXISTS `view_long_authors`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_authors` AS select `v`.`ID` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`nome` AS `nome`,`v`.`cognome` AS `cognome`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_authors` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_short_bibliographies`
-- 

DROP VIEW IF EXISTS `view_short_bibliographies`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_bibliographies` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from `contents` where (`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'biblio')));

-- 
-- Struttura della tabella `view_short_documents`
-- 

DROP VIEW IF EXISTS `view_short_documents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_documents` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from `contents` where (`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'doc')));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_short_events`
-- 

DROP VIEW IF EXISTS `view_short_events`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_events` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from `contents` where (`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'evento')));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_short_galleries`
-- 

DROP VIEW IF EXISTS `view_short_galleries`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_galleries` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from `contents` where (`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'galleria')));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_short_libraries`
-- 

DROP VIEW IF EXISTS `view_short_libraries`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_libraries` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from `contents` where (`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'libreria')));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_short_news`
-- 

DROP VIEW IF EXISTS `view_short_news`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_news` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from `contents` where (`contents`.`content_type_id` = (select `content_types`.`id` AS `id` from `content_types` where (`content_types`.`name` = _latin1'news')));
-- --------------------------------------------------------
-- 
-- Struttura della tabella `view_long_bibliographies`
-- 

DROP VIEW IF EXISTS `view_long_bibliographies`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_bibliographies` AS select `v`.`ID` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_bibliographies` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------
-- 
-- Struttura della tabella `view_short_contents`
-- 

DROP VIEW IF EXISTS `view_short_contents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_short_contents` AS select `contents`.`id` AS `ID`,`contents`.`status` AS `status`,`contents`.`titolo` AS `titolo`,`contents`.`lang` AS `lang`,`contents`.`inizio` AS `inizio`,`contents`.`fine` AS `fine`,`contents`.`data` AS `data`,`contents`.`content_type_id` AS `content_type_id`,`content_types`.`name` AS `content_type`,(select if((((now() >= `contents`.`inizio`) or isnull(`contents`.`inizio`)) and ((now() <= `contents`.`fine`) or isnull(`contents`.`fine`))),1,0) AS `IF(((NOW() >= contents.inizio OR contents.inizio IS NULL) AND (NOW() <= contents.fine OR contents.fine IS NULL)),1,0)`) AS `valida` from (`contents` join `content_types` on((`contents`.`content_type_id` = `content_types`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_long_contents`
-- 

DROP VIEW IF EXISTS `view_long_contents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_contents` AS select `v`.`ID` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`content_type_id` AS `content_type_id`,`v`.`content_type` AS `content_type`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_contents` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_long_documents`
-- 

DROP VIEW IF EXISTS `view_long_documents`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_documents` AS select `v`.`ID` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_documents` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_long_events`
-- 

DROP VIEW IF EXISTS `view_long_events`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_events` AS select `v`.`ID` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_events` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_long_galleries`
-- 

DROP VIEW IF EXISTS `view_long_galleries`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_galleries` AS select `v`.`ID` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_galleries` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_long_libraries`
-- 

DROP VIEW IF EXISTS `view_long_libraries`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_libraries` AS select `v`.`ID` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_libraries` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_long_news`
-- 

DROP VIEW IF EXISTS `view_long_news`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_long_news` AS select `v`.`ID` AS `ID`,`v`.`status` AS `status`,`v`.`titolo` AS `titolo`,`v`.`lang` AS `lang`,`v`.`inizio` AS `inizio`,`v`.`fine` AS `fine`,`v`.`data` AS `data`,`v`.`valida` AS `valida`,`fileStatico`.`id` AS `filestatico_id`,`fileStatico`.`status` AS `filestatico_status`,`fileStatico`.`filePath` AS `filestatico_filePath`,`firstImage`.`id` AS `firstImage_id`,`firstImage`.`status` AS `firstImage_status`,`firstImage`.`filePath` AS `firstImage_filePath`,`audioVideo`.`id` AS `audioVideo_id`,`audioVideo`.`status` AS `audioVideo_status`,`audioVideo`.`filePath` AS `audioVideo_filePath` from ((((((`view_short_news` `v` left join `contents_multimedia_objects` `CFS` on(((`v`.`ID` = `CFS`.`content_id`) and (`CFS`.`tipo` = _latin1'fileStatico')))) left join `multimedia_objects` `fileStatico` on((`CFS`.`object_id` = `fileStatico`.`id`))) left join `contents_multimedia_objects` `CFS2` on(((`v`.`ID` = `CFS2`.`content_id`) and (`CFS2`.`tipo` = _latin1'firstImage')))) left join `multimedia_objects` `firstImage` on((`CFS2`.`object_id` = `firstImage`.`id`))) left join `contents_multimedia_objects` `CFS3` on(((`v`.`ID` = `CFS3`.`content_id`) and (`CFS3`.`tipo` = _latin1'audioVideo')))) left join `multimedia_objects` `audioVideo` on((`CFS3`.`object_id` = `audioVideo`.`id`)));

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_sections`
-- 

DROP VIEW IF EXISTS `view_sections`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_sections` AS select `view_tree_areas_groups`.`area_id` AS `area_id`,`view_tree_areas_groups`.`area_lang` AS `area_lang`,`view_tree_areas_groups`.`id` AS `id`,`view_tree_areas_groups`.`status` AS `status`,`view_tree_areas_groups`.`type_id` AS `type_id`,`view_tree_areas_groups`.`type` AS `type`,`view_tree_areas_groups`.`prior` AS `prior`,`view_tree_areas_groups`.`name` AS `name`,`view_tree_areas_groups`.`lang_id` AS `lang_id`,`view_tree_areas_groups`.`lang` AS `lang`,`view_tree_areas_groups`.`nameLang` AS `nameLang` from `view_tree_areas_groups` where (`view_tree_areas_groups`.`type` = _latin1'sezione') order by `view_tree_areas_groups`.`area_id`,`view_tree_areas_groups`.`id`;

-- --------------------------------------------------------
-- 
-- Struttura della tabella `view_subjects`
-- 

DROP VIEW IF EXISTS `view_subjects`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_subjects` AS select `view_tree_areas_groups`.`area_id` AS `area_id`,`view_tree_areas_groups`.`area_lang` AS `area_lang`,`view_tree_areas_groups`.`id` AS `id`,`view_tree_areas_groups`.`status` AS `status`,`view_tree_areas_groups`.`type_id` AS `type_id`,`view_tree_areas_groups`.`type` AS `type`,`view_tree_areas_groups`.`prior` AS `prior`,`view_tree_areas_groups`.`name` AS `name`,`view_tree_areas_groups`.`lang_id` AS `lang_id`,`view_tree_areas_groups`.`lang` AS `lang`,`view_tree_areas_groups`.`nameLang` AS `nameLang` from `view_tree_areas_groups` where (`view_tree_areas_groups`.`type` = _latin1'soggetto') order by `view_tree_areas_groups`.`area_id`,`view_tree_areas_groups`.`id`;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `view_tipologies`
-- 

DROP VIEW IF EXISTS `view_tipologies`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_tipologies` AS select `view_tree_areas_groups`.`area_id` AS `area_id`,`view_tree_areas_groups`.`area_lang` AS `area_lang`,`view_tree_areas_groups`.`id` AS `id`,`view_tree_areas_groups`.`status` AS `status`,`view_tree_areas_groups`.`type_id` AS `type_id`,`view_tree_areas_groups`.`type` AS `type`,`view_tree_areas_groups`.`prior` AS `prior`,`view_tree_areas_groups`.`name` AS `name`,`view_tree_areas_groups`.`lang_id` AS `lang_id`,`view_tree_areas_groups`.`lang` AS `lang`,`view_tree_areas_groups`.`nameLang` AS `nameLang` from `view_tree_areas_groups` where (`view_tree_areas_groups`.`type` = _latin1'tipologia') order by `view_tree_areas_groups`.`area_id`,`view_tree_areas_groups`.`id`;

-- --------------------------------------------------------

