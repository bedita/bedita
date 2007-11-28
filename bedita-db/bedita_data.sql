-- phpMyAdmin SQL Dump
-- version 2.8.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 10 Mag, 2007 at 04:02 PM
-- Versione MySQL: 5.0.37
-- Versione PHP: 5.2.0
--
-- Database: 'bedita2'
--

--
-- Dump dei dati per la tabella 'content_types'
--

INSERT INTO object_types (id, name) VALUES
(1, 'area'),
(2, 'newsletter'),
(3, 'section'),

(4, 'questionnaire'),
(5, 'faq'),
(29, 'gallery'),
(6, 'cartigli'),

(7, 'scroll'),
(8, 'timeline'),
(9, 'community'),

(10, 'file'),
(11, 'audiovideo'),
(12, 'image'),

(13, 'comment'),
(14, 'faqquestion'),
(15, 'question'),

(16, 'answer'),
(17, 'user'),
(18, 'shortnews'),

(19, 'bibliography'),
(20, 'book'),
(21, 'event'),

(22, 'document'),
(23, 'documentptrobject'),
(24, 'documentptrextern'),

(25, 'documentptrfile'),
(26, 'documentptrservice'),
(27, 'documentrule'),

(28, 'author'),
(30, 'biblioitem')
;

INSERT INTO `question_types` (`id`, `label`) VALUES
(1, 'scelta multipla'),
(2, 'scelta singola'),
(3, 'testo libero'),
(4, 'checkOpen'),
(5, 'grado'),
(6, 'testo semplice')
;

-- ---------------------------
-- Dati primo utente e gruppi
-- ---------------------------
INSERT INTO `users` ( id, `userid` , `realname` , `passwd` ) VALUES (1, 'bedita', 'BEdita', MD5( 'bedita' ));

INSERT INTO `groups` ( `name` ) VALUES ('administrator');
INSERT INTO `groups` ( `name` ) VALUES ('guest');

INSERT INTO `groups_users` ( `user_id` , `group_id` ) VALUES ((SELECT MAX(id) FROM users), (SELECT id FROM groups WHERE name = 'administrator'));



-- ---------------------------
-- Dati moduli
-- ---------------------------
INSERT INTO `modules` (`label`, `color`, `path`, `status`) VALUES
('admin', '#000000', 'admin', 'on'),
('areas', '#ff9933', 'areas', 'on'),

('documents', '#ff9900', 'documents', 'on'),
('galleries', '#123456', 'galleries', 'on') ,
('multimedia', '#ff3456', 'multimedia', 'on') ;


INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES (
(SELECT id FROM modules WHERE label = 'admin'),
(SELECT id FROM groups WHERE name = 'administrator'),
'group', '15'
) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES (
(SELECT id FROM modules WHERE label = 'areas'),
(SELECT id FROM groups WHERE name = 'administrator'),
'group', '15'
) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES (
(SELECT id FROM modules WHERE label = 'documents'),
(SELECT id FROM groups WHERE name = 'administrator'),
'group', '15'
) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES (
(SELECT id FROM modules WHERE label = 'galleries'),
(SELECT id FROM groups WHERE name = 'administrator'),
'group', '15'
) ;

INSERT INTO `permission_modules` ( `module_id` , `ugid` , `switch` , `flag` )
VALUES (
(SELECT id FROM modules WHERE label = 'multimedia'),
(SELECT id FROM groups WHERE name = 'administrator'),
'group', '15'
) ;
-- ---------------------------
-- Dati di esempio
-- ---------------------------
-- Area: www.test.clq
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES  (2, 1, 'on', NOW(), NOW(), 'Test site', "TestSite", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `collections` (`id`, `create_rules`, `access_rules`) VALUES (2, NULL, NULL);
INSERT INTO `areas` (`id`) VALUES (2);
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (2, NULL, '/2', '/', 1);

INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
2, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

INSERT INTO `custom_properties` ( `id` , `object_id` , `name` , `type` , `integer` )
VALUES (
NULL , '2', 'prova', 'integer', '10'
);

-- 2 sezioni
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES (3, 3, 'on', NOW(), NOW(), 'Sezione Home Page 1', "SezioneHomePage1", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `collections` (`id`, `create_rules`, `access_rules`) VALUES (3, NULL, NULL);
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (3, 2, '/2/3', '/2', 1);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
3, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES (4, 3, 'on', NOW(), NOW(), 'Sezione Home Page 2', "SezioneHomePage2", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `collections` (`id`, `create_rules`, `access_rules`) VALUES (4, NULL, NULL);
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (4, 2, '/2/4', '/2', 2);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
4, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

-- suo omologo in inglese
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES (6, 22, 'on', NOW(), NOW(), 'Primo Documento di Test', "PrimoDocumentoDiTest", 1, 'en', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (6, '2007-05-22 10:59:28', '2008-05-22 10:59:34', 'sottotitolo', 'Questo è il testo breve', 'txt');
INSERT INTO `contents` ( `id` , `audio_video_id` , `image_id` , `longDesc` ) VALUES (6, NULL , NULL , 'testo lungo');
INSERT INTO `base_documents` ( `id` , `desc_author` , `flagComments` , `credits` ) VALUES (6, 'descrizione autore', '1', 'credits');
INSERT INTO `documents` ( `id` , `ptrURL` , `ptrObj` , `ptrFile` , `ptrRule` , `switch` ) VALUES (6, NULL , NULL , NULL , NULL , 'doc');
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (6, 3, '/2/3/6', '/2/3', 1);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
6, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

-- aggiunta di una immagine
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES (7, 12, 'on', NOW(), NOW(), 'Immagine di test', "ImmagineDiTest", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (7, '2007-05-22 10:59:28', '2008-05-22 10:59:34', NULL, NULL, 'txt');
INSERT INTO `streams` ( `id` , `path` , `name` , `type` , `size` ) VALUES ('7', '/test/test.jpg', 'test.jpg', 'image/jpeg', '34564');
INSERT INTO `images` ( `id` ) VALUES ('7');
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
7, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

-- un oggetto di tipo document (22) inserito in home page
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES (5, 22, 'on', NOW(), NOW(), 'Primo Documento di Test', "PrimoDocumentoDiTest", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (5, '2007-05-22 10:59:28', '2008-05-22 10:59:34', 'sottotitolo', 'Questo è il testo breve', 'txt');
INSERT INTO `contents` ( `id` , `audio_video_id` , `image_id` , `longDesc` ) VALUES (5, NULL , 7 , 'testo lungo');
INSERT INTO `base_documents` ( `id` , `desc_author` , `flagComments` , `credits` ) VALUES (5, 'descrizione autore', '1', 'credits');
INSERT INTO `documents` ( `id` , `ptrURL` , `ptrObj` , `ptrFile` , `ptrRule` , `switch` ) VALUES (5, NULL , NULL , NULL , NULL , 'doc');
INSERT INTO `custom_properties` ( `object_id` , `name` , `type` , `integer` , `bool` , `float` , `string` , `stream` ) VALUES ('5', 'test', 'integer', '10', NULL , NULL , NULL , NULL);
INSERT INTO `custom_properties` ( `object_id` , `name` , `type` , `integer` , `bool` , `float` , `string` , `stream` )VALUES ('5', 'testBool', 'bool', NULL , '1', NULL , NULL , NULL);
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (5, 3, '/2/3/5', '/2/3', 2);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
5, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

-- fa le associazioni
INSERT INTO `content_bases_objects` ( `object_id` , `id` , `switch` ) VALUES (6, 5, 'LANGS') ;
INSERT INTO `content_bases_objects` ( `object_id` , `id` , `switch` ) VALUES (7, 5, 'IMGS');

-- Inserisce un oggetto di tipo evento con un calendario
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`) VALUES (8, 21, 'on', NOW(), NOW(), 'Primo Evento di Test', "PrimoEventoDiTest", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (8, '2007-05-22 10:59:28', '2008-05-22 10:59:34', 'sottotitolo', 'Questo è il testo breve', 'txt');
INSERT INTO `contents` ( `id` , `audio_video_id` , `image_id` , `longDesc` ) VALUES (8, NULL , 7 , 'testo lungo');
INSERT INTO `base_documents` ( `id` , `desc_author` , `flagComments` , `credits` ) VALUES (8, 'descrizione autore', '1', 'credits');
INSERT INTO `events` ( `id` ) VALUES (8);
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (8, 3, '/2/3/8', '/2/3', 3);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
8, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

INSERT INTO `event_date_items` ( `id` , `event_id`, `start` , `end`) VALUES (1, 8, '2007-06-22 00:00:00', '2008-06-24 00:00:00');
INSERT INTO `event_date_items` ( `id` , `event_id`, `start` , `end`) VALUES (2, 8, '2007-07-22 00:00:00', '2008-07-24 00:00:00');

-- Inserisce una short_news
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`) VALUES (9, 18, 'on', NOW(), NOW(), 'Prima Notizia di Test', "PrimaNotiziaDiTest", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (9, '2007-05-22 10:59:28', '2008-05-22 10:59:34', 'sottotitolo', 'Questo è il testo breve', 'txt');
INSERT INTO `short_news` ( `id`) VALUES (9) ;
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (9, 2, '/2/9', '/2', 3);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
9, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

INSERT INTO `links` ( `id`, `object_id`, `switch`, `url`) VALUES (1, 9, 'url', 'htt://www.qwerg.com') ;
INSERT INTO `links` ( `id`, `object_id`, `switch`, `url`) VALUES (2, 9, 'url', 'htt://www.chialab.it') ;


-- aggiunta di una immagine x un author
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`) VALUES (10, 12, 'on', NOW(), NOW(), 'Immagine Albanese', "ImmagineAlbanese", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (10, '2007-05-22 10:59:28', '2008-05-22 10:59:34', NULL, NULL, 'txt');
INSERT INTO `streams` ( `id` , `path` , `name` , `type` , `size` ) VALUES (10, '/test/albanese.jpg', 'test.jpg', 'image/jpeg', '34564');
INSERT INTO `images` ( `id` ) VALUES (10);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
10, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

-- Inserisce un author
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`) VALUES (11, 28, 'on', NOW(), NOW(), 'Antonio Albanese', "AntonioAlbanese", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (11, '2007-05-22 10:59:28', '2008-05-22 10:59:34', 'sottotitolo', 'Questo è il testo breve DI Albanese', 'txt');
INSERT INTO `authors` ( `id` , `image_id` , `nome` , `cognome` , `search_string` ) VALUES ('11', '10', 'Antonio', 'Albanese', 'test search string') ;
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (11, 2, '/2/11', '/2', 4);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
11, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

-- omologo del 5  in francese
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES (12, 22, 'on', NOW(), NOW(), 'Primo Documento di Test', "PrimoDocumentoDiTest", 1, 'fr', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (12, '2007-05-22 10:59:28', '2008-05-22 10:59:34', 'sottotitolo', 'Questo è il testo breve', 'txt');
INSERT INTO `contents` ( `id` , `audio_video_id` , `image_id` , `longDesc` ) VALUES (12, NULL , NULL , 'testo lungo');
INSERT INTO `base_documents` ( `id` , `desc_author` , `flagComments` , `credits` ) VALUES (12, 'descrizione autore', '1', 'credits');
INSERT INTO `documents` ( `id` , `ptrURL` , `ptrObj` , `ptrFile` , `ptrRule` , `switch` ) VALUES (12, NULL , NULL , NULL , NULL , 'doc');
INSERT INTO `trees` ( `id` , `parent_id` , `path`, `pathParent`, `priority` ) VALUES (12, 3, '/2/3/12', '/2/12', 1);
INSERT INTO `permissions` ( `object_id` , `ugid` , `switch` , `flag` )
VALUES (
12, (SELECT id FROM groups WHERE name = 'administrator'), 'group', '15'
) ;

INSERT INTO `content_bases_objects` ( `object_id` , `id` , `switch` ) VALUES (12, 5, 'LANGS') ;

-- inserisce delle categorie x documenti
INSERT INTO `typed_object_categories` (id, `area_id` , `label` , `typed` , `priority` ) VALUES (1, '2', 'Categoria Doc # 1', 22 , '1') ;
INSERT INTO `typed_object_categories` (id, `area_id` , `label` , `typed` , `priority` ) VALUES (2, '2', 'Categoria Doc # 2', 22 , '2') ;
INSERT INTO `typed_object_categories` (id, `area_id` , `label` , `typed` , `priority` ) VALUES (3, '2', 'Categoria Doc # 3', 22 , '3') ;

-- inserisce il documento 5 nelle categorie
INSERT INTO `contents_typed_object_categories` ( `content_id` , `typed_object_category_id`) VALUES (5, 2) ;
INSERT INTO `contents_typed_object_categories` ( `content_id` , `typed_object_category_id`) VALUES (5, 1) ;

-- Inserisce commenti al 5
INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES (13, 13, 'on', NOW(), NOW(), 'Commento 1', "Commento1", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (13, '2007-05-22 10:59:28', '2008-05-22 10:59:34', '', 'Questo è il commento 1', 'txt');
INSERT INTO `comments` ( `id` , `author` , `email` ) VALUES (13, 'giangi', 'example@example.com')  ;
INSERT INTO `content_bases_objects` ( `object_id` , `id` , `switch` ) VALUES (13, 5, 'COMMENTS') ;

INSERT INTO `objects` (`id`, `object_type_id`, `status`, `created`, `modified`, `title`, `nickname`, `current`, `lang`, `IP_created`, `user_created`, `user_modified`)  VALUES (14, 13, 'on', NOW(), NOW(), 'Commento 1', "Commento1", 1, 'it', '192.168.0.1', 1, 1);
INSERT INTO `content_bases` ( `id` , `start` , `end` , `subtitle` , `shortDesc` , `formato` ) VALUES (14, '2007-05-22 10:59:28', '2008-05-22 10:59:34', '', 'Questo è il commento 1', 'txt');
INSERT INTO `comments` ( `id` , `author` , `email` ) VALUES (14, 'giangi', 'example@example.com')  ;
INSERT INTO `content_bases_objects` ( `object_id` , `id` , `switch` ) VALUES (14, 5, 'COMMENTS') ;

INSERT INTO `links` ( `id`, `object_id`, `switch`, `url`) VALUES (3, 5, 'url', 'htt://www.qwerg.com') ;
INSERT INTO `links` ( `id`, `object_id`, `switch`, `url`) VALUES (4, 5, 'url', 'htt://www.chialab.it') ;


-- -----------------------------
-- Dati diversi utenti per test
-- -----------------------------
INSERT INTO `users` ( id, `userid` , `realname` , `passwd` ) VALUES (2, 'giangi', 'Giangi', MD5( 'giangi' ));
INSERT INTO `users` ( id, `userid` , `realname` , `passwd` ) VALUES (3, 'alberto', 'Alberto', MD5( 'alberto' ));
INSERT INTO `users` ( id, `userid` , `realname` , `passwd` ) VALUES (4, 'torto', 'Paolo Rossi', MD5( 'torto' ));

INSERT INTO `groups` ( `name` ) VALUES ('editor');
INSERT INTO `groups` ( `name` ) VALUES ('reader');
INSERT INTO `groups` ( `name` ) VALUES ('frontend');

INSERT INTO `groups_users` ( `user_id` , `group_id` ) VALUES ((SELECT MAX(id) FROM users WHERE userid = 'giangi'), (SELECT id FROM groups WHERE name = 'administrator'));
INSERT INTO `groups_users` ( `user_id` , `group_id` ) VALUES ((SELECT MAX(id) FROM users WHERE userid = 'giangi'), (SELECT id FROM groups WHERE name = 'reader'));
INSERT INTO `groups_users` ( `user_id` , `group_id` ) VALUES ((SELECT MAX(id) FROM users WHERE userid = 'giangi'), (SELECT id FROM groups WHERE name = 'editor'));
INSERT INTO `groups_users` ( `user_id` , `group_id` ) VALUES ((SELECT MAX(id) FROM users WHERE userid = 'alberto'),(SELECT id FROM groups WHERE name = 'editor'));
INSERT INTO `groups_users` ( `user_id` , `group_id` ) VALUES ((SELECT MAX(id) FROM users WHERE userid = 'torto'),  (SELECT id FROM groups WHERE name = 'frontend'));
