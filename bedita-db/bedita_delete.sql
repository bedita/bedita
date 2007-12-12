--
-- Database: `bedita2`
-- NOTA: per ora solo modello oggetti, tree, versioning, 
--       utenti e permessi

-- --------------------------------------------------------

-- 
-- Struttura tabelle utenti e login
-- 

SET FOREIGN_KEY_CHECKS=0;

DROP VIEW IF EXISTS `view_files`;
DROP VIEW IF EXISTS `view_multimedias`;
DROP VIEW IF EXISTS `view_audio`;
DROP VIEW IF EXISTS `view_video`;
DROP VIEW IF EXISTS `view_images`;
DROP VIEW IF EXISTS `view_communities`;
DROP VIEW IF EXISTS `view_timelines`;
DROP VIEW IF EXISTS `view_scrolls`;
DROP VIEW IF EXISTS `view_faqs`;
DROP VIEW IF EXISTS `view_questionnaires`;
DROP VIEW IF EXISTS `view_sections`;
DROP VIEW IF EXISTS `view_attachments`;
DROP VIEW IF EXISTS `view_permissions`;
DROP VIEW IF EXISTS `view_trees` ;

DROP TABLE IF EXISTS `links`;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `event_date_items`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `object_users`;
DROP TABLE IF EXISTS `bibliographies`;
DROP TABLE IF EXISTS `base_documents`;
DROP TABLE IF EXISTS `content_bases_objects`;
DROP TABLE IF EXISTS `contents_typed_object_categories`;
DROP TABLE IF EXISTS `contents`;
DROP TABLE IF EXISTS `authors`;
DROP TABLE IF EXISTS `images`;
DROP TABLE IF EXISTS `files`;
DROP TABLE IF EXISTS `typed_object_categories`;
DROP TABLE IF EXISTS `answers`;
DROP TABLE IF EXISTS `faq_questions`;
DROP TABLE IF EXISTS `audio_videos`;
DROP TABLE IF EXISTS `audio`;
DROP TABLE IF EXISTS `video`;
DROP TABLE IF EXISTS `areas`;
DROP TABLE IF EXISTS `streams`;
DROP TABLE IF EXISTS `short_news`;
DROP TABLE IF EXISTS `newsletters`;
DROP TABLE IF EXISTS `lang_texts`;
DROP TABLE IF EXISTS `indexs`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `object_users`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `versions`;
DROP TABLE IF EXISTS `trees`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `biblio_items`;
DROP TABLE IF EXISTS `content_bases`;
DROP TABLE IF EXISTS `custom_properties`;
DROP TABLE IF EXISTS `collections`;
DROP TABLE IF EXISTS `objects`;
DROP TABLE IF EXISTS `question_types`;
DROP TABLE IF EXISTS `object_types`;
DROP TABLE IF EXISTS `groups_users`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `groups`;
DROP TABLE IF EXISTS `modules`;
DROP TABLE IF EXISTS `permission_modules`;

