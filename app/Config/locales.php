<?php
/*
 * Resources
 *
 * http://it.php.net/manual/en/function.setlocale.php
 * http://www.loc.gov/standards/iso639-2/php/code_list.php
 * http://docs.moodle.org/dev/Table_of_locales - Windows locale's strings
 *
 */

$config["locales"] = array(
	'eng' => array(
		'en_US.UTF8','en_US.UTF-8','en_US',
		'en_GB.UTF8','en_GB.UTF-8','en_GB',
	),
	'fra' => array(
		'fr_FR.UTF8','fr_FR.UTF-8','fr_FR',
		'fr_CH.UTF8','fr_CH.UTF-8','fr_CH',
		'fr_BE.UTF8','fr_BE.UTF-8','fr_BE',
		'French_France.1252',
	),
	'ger' => array(
		'de_DE.UTF8','de_DE.UTF-8','de_DE',
		'German_Germany.1252',
	),
	'ita' => array(
		'it_IT.UTF8','it_IT.UTF-8','it_IT',
		'Italian_Italy.1252',
	),
	'spa' => array(
		'es_ES.UTF8','es_ES.UTF-8','es_ES',
		'Spanish_Spain.1252',
	),
);