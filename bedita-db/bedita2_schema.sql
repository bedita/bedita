-- phpMyAdmin SQL Dump
-- version 2.8.2.4
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generato il: 10 Mag, 2007 at 04:00 PM
-- Versione MySQL: 5.0.37
-- Versione PHP: 5.2.0
-- 
-- Database: `bedita2`
-- 

-- --------------------------------------------------------

-- 
-- Struttura della tabella `areas`
-- 

DROP TABLE IF EXISTS `areas`;
CREATE TABLE `areas` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content_id` int(10) NOT NULL,
  `area_type` set('site','newsletter') default NULL,
  `servername` varchar(255) default NULL,
  `area_status` enum('on','off','private','hidden') default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=latin1 COMMENT='area information';

-- --------------------------------------------------------

-- 
-- Struttura della tabella `authors`
-- 

DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content_id` int(10) unsigned NOT NULL,
  `name` varchar(32) default NULL,
  `surname` varchar(32) default NULL,
  `poetics` text,
  `biography` text,
  PRIMARY KEY  (`id`),
  KEY `library_characteristics_FKIndex1` (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=203 DEFAULT CHARSET=latin1 COMMENT='specific autho related contents';

-- --------------------------------------------------------

-- 
-- Struttura della tabella `biblios`
-- 

DROP TABLE IF EXISTS `biblios`;
CREATE TABLE `biblios` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `multimedia_object_id` int(10) unsigned default NULL,
  `book_id` int(10) unsigned default NULL,
  `content_id` int(10) unsigned NOT NULL,
  `search_code` varchar(255) default NULL,
  `switch` set('txt','book') default NULL,
  PRIMARY KEY  (`id`),
  KEY `biblio_contents_FKIndex2` (`book_id`),
  KEY `biblio_contents_FKIndex3` (`multimedia_object_id`),
  KEY `biblio_contents_FKIndex4` (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `comments`
-- 

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content_id` int(10) unsigned NOT NULL,
  `ip` varchar(16) default NULL,
  `name` varchar(100) default NULL,
  `email` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `comments_FKIndex1` (`content_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `content_trees`
-- 

DROP TABLE IF EXISTS `content_trees`;
CREATE TABLE `content_trees` (
  `content_id` int(11) NOT NULL,
  `path` mediumtext NOT NULL,
  `parent_path` mediumtext NOT NULL,
  `parent_id` int(11) default NULL,
  `priority` int(11) default NULL,
  PRIMARY KEY  (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='content tree structure, no data information';

-- --------------------------------------------------------

-- 
-- Struttura della tabella `content_types`
-- 

DROP TABLE IF EXISTS `content_types`;
CREATE TABLE `content_types` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) default NULL,
  `container` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=latin1 COMMENT='type information, contents and containers, read-only';

-- --------------------------------------------------------

-- 
-- Struttura della tabella `contents`
-- 

DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` int(10) unsigned NOT NULL,
  `content_type_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned default NULL,
  `status` set('on','off','draft') default NULL,
  `created` date NOT NULL,
  `modified` date default NULL,
  `start` date default NULL,
  `end` date default NULL,
  `lang` char(7) default NULL,
  `title` varchar(255) default NULL,
  `subtitle` varchar(255) default NULL,
  `short_text` text,
  `long_text` text,
  `user_name` varchar(255) default NULL,
  `allow_comments` set('Y','N') default 'Y',
  PRIMARY KEY  (`id`),
  KEY `contents_FKIndex1` (`user_id`),
  KEY `contents_FKIndex2` (`content_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='content base information';

-- --------------------------------------------------------

-- 
-- Struttura della tabella `contents_multimedia_objects`
-- 

DROP TABLE IF EXISTS `contents_multimedia_objects`;
CREATE TABLE `contents_multimedia_objects` (
  `object_id` int(10) unsigned NOT NULL,
  `mm_type` enum('firstImage','audioVideo','inline','attachment','fileStatico') NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`object_id`,`mm_type`,`content_id`),
  KEY `contents_objects_FKIndex2` (`object_id`),
  KEY `contents_objects_FKIndex21` (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `lang_texts`
-- 

DROP TABLE IF EXISTS `lang_texts`;
CREATE TABLE `lang_texts` (
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
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `links`
-- 

DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
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
) ENGINE=MyISAM AUTO_INCREMENT=333 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `modules`
-- 

DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
  `id` int(10) NOT NULL auto_increment,
  `label` varchar(32) default NULL,
  `color` varchar(7) default NULL,
  `path` varchar(16) default NULL,
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `multimedia_objects`
-- 

DROP TABLE IF EXISTS `multimedia_objects`;
CREATE TABLE `multimedia_objects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `description` text,
  `filePath` varchar(255) default NULL,
  `fileName` varchar(255) default NULL,
  `fileType` varchar(255) default NULL,
  `fileSize` varchar(255) default NULL,
  `status` enum('on','off') default NULL,
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5902 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Struttura della tabella `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) NOT NULL auto_increment,
  `username` varchar(32) NOT NULL default '',
  `passw` varchar(32) NOT NULL,
  `email` varchar(35) default NULL,
  `name` varchar(30) default NULL,
  `surname` varchar(30) default NULL,
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
