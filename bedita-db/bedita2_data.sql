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

INSERT INTO content_types (id, name, container) VALUES (101, 'event', 0),
(102, 'biblio', 0),
(103, 'doc', 0),
(104, 'gallery', 0),
(105, 'news', 0),
(106, 'author', 0),
(107, 'book', 0),
(108, 'library', 0),
(5, 'categoria', 1),
(2, 'tipologia', 1),
(3, 'sezione', 1),
(4, 'soggetto', 1),
(9, 'catLibreria', 1),
(6, 'catFaq', 1),
(7, 'catTimeline', 1),
(8, 'catCartiglio', 1),
(1, 'area', 1),
(109, 'comment', 0);

-- 
-- Dump dei dati per la tabella 'modules'
-- 

INSERT INTO modules (id, label, color, path, status) VALUES (2, 'eventi', '#3399CC', 'events', 'on'),
(3, 'bibliografie', '#cc9900', 'bibliographies', 'on'),
(4, 'documenti', '#ff9900', 'documents', 'on'),
(5, 'news', '#CC00FF', 'news', 'on'),
(6, 'questionari', '#999966', 'questionnaries', 'off'),
(7, 'gallerie', '#ffcc33', 'galleries', 'on'),
(8, 'newsletter', '#99cc33', 'newsletters', 'off'),
(9, 'iscrizioni', '#33cc99', 'inscribes', 'off'),
(10, 'reference', '#cc3333', 'references', 'off'),
(11, 'prenotazioni pc', '#999999', 'bookings', 'off'),
(12, 'cronologia', '#3366cc', 'timelines', 'off'),
(13, 'cartigli', '#993300', 'cartigli', 'off'),
(14, 'autori', '#99cccc', 'authors', 'on'),
(15, 'utilitÃƒÂ ', '#FFFFFF', 'utilities', 'off'),
(17, 'superadmin', '#000000', 'users', 'on'),
(16, 'ragazzi', '#339900', 'ragazzi', 'off'),
(1, 'aree', '#ff9933', 'areas', 'on');

-- 
-- Dump dei dati per la tabella 'users'
-- 

INSERT INTO users (id, username, passw, email, name, surname, status) VALUES (5, 'armando', 'b499dc5fb65cd81f5dec94df0ef67d99', '', '', 'bibliotecari', 'on'),
(2, 'administrator', 'b499dc5fb65cd81f5dec94df0ef67d99', '', '', 'tutors', 'on'),
(3, 'redazione', 'b499dc5fb65cd81f5dec94df0ef67d99', 'redazioneweb@comune.bologna.it', '', 'maestre', 'on'),
(7, 'cippo', 'b499dc5fb65cd81f5dec94df0ef67d99', '', '', 'sala borsa ragazzi', 'on'),
(6, 'qwerg', 'b499dc5fb65cd81f5dec94df0ef67d99', 'example@example.com', '', 'qwerg|chialab', 'on'),
(8, 'extBiblioReference', 'b499dc5fb65cd81f5dec94df0ef67d99', '', '', 'reference esterno', 'on'),
(9, 'guardiola', 'b499dc5fb65cd81f5dec94df0ef67d99', '', '', 'paleotti', 'on'),
(10, 'hamelin', 'b499dc5fb65cd81f5dec94df0ef67d99', '', '', 'xanadu', 'on'),
(15, 'aa', 'b499dc5fb65cd81f5dec94df0ef67d99', '', '', '', 'on'),
(21, 'username', 'b499dc5fb65cd81f5dec94df0ef67d99', 'example@example.com', 'nome', 'cognome', 'off');
