<?php 
/* BeditaApp schema generated on: 2014-01-13 12:41:29 : 1389613289*/
class BeditaAppSchema extends CakeSchema {
	var $name = 'BeditaApp';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $aliases = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'nickname_alias' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'comment' => 'alternative nickname', 'charset' => 'utf8'),
		'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3, 'collate' => 'utf8_general_ci', 'comment' => 'alias preferred language, can be NULL', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'nickname_alias' => array('column' => 'nickname_alias', 'unique' => 1), 'object_id' => array('column' => 'object_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $annotations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'author' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'annotation author', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'annotation author email', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'annotation url, can be NULL', 'charset' => 'utf8'),
		'thread_path' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'path to thread, can be NULL', 'charset' => 'utf8'),
		'rating' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'object rating, can be NULL'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'author_idx' => array('column' => 'author', 'unique' => 0), 'objects_idx' => array('column' => 'object_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $applications = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'application_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'name of application, for example flash', 'charset' => 'utf8'),
		'application_label' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'label for application, for example Adobe Flash, can be NULL', 'charset' => 'utf8'),
		'application_version' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_general_ci', 'comment' => 'version of application, can be NULL', 'charset' => 'utf8'),
		'application_type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'type of application, for example application/x-shockwave-flash', 'charset' => 'utf8'),
		'text_dir' => array('type' => 'string', 'null' => true, 'default' => 'ltr', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'text orientation (ltr:left to right;rtl: right to left)', 'charset' => 'utf8'),
		'text_lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'text language, can be NULL', 'charset' => 'utf8'),
		'width' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5, 'comment' => 'application window width in pixels'),
		'height' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5, 'comment' => 'application window height in pixels'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $areas = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'public_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'public name for publication, can be NULL', 'charset' => 'utf8'),
		'public_url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'public url for publication, can be NULL', 'charset' => 'utf8'),
		'staging_url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'staging/test url for publication, can be NULL', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'publication email, can be NULL', 'charset' => 'utf8'),
		'stats_code' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'statistics code, for example google stats code. can be NULL', 'charset' => 'utf8'),
		'stats_provider' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'statistics provider, for example google. can be NULL', 'charset' => 'utf8'),
		'stats_provider_url' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'statistics provider url', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $banned_ips = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'ip_address' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 15, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'comment' => 'creation time'),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'comment' => 'last modified time'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'ban', 'length' => 10, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'ip status (ban, accept)', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ip_unique' => array('column' => 'ip_address', 'unique' => 1), 'status_idx' => array('column' => 'status', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $cards = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'collate' => 'utf8_general_ci', 'comment' => 'person name, can be NULL', 'charset' => 'utf8'),
		'surname' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'collate' => 'utf8_general_ci', 'comment' => 'person surname, can be NULL', 'charset' => 'utf8'),
		'person_title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => 'person title, for example Sir, Madame, Prof, Doct, ecc., can be NULL', 'charset' => 'utf8'),
		'gender' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => 'gender, for example male, female, can be NULL', 'charset' => 'utf8'),
		'birthdate' => array('type' => 'date', 'null' => true, 'default' => NULL, 'comment' => 'date of birth, can be NULL'),
		'deathdate' => array('type' => 'date', 'null' => true, 'default' => NULL, 'comment' => 'date of death, can be NULL'),
		'company' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'is a company, default: false'),
		'company_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'name of company, can be NULL', 'charset' => 'utf8'),
		'company_kind' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'collate' => 'utf8_general_ci', 'comment' => 'type of company, can be NULL', 'charset' => 'utf8'),
		'street_address' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'address street, can be NULL', 'charset' => 'utf8'),
		'city' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'city, can be NULL', 'charset' => 'utf8'),
		'zipcode' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => 'zipcode, can be NULL', 'charset' => 'utf8'),
		'country' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'country, can be NULL', 'charset' => 'utf8'),
		'state_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'state, can be NULL', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'first email, can be NULL', 'charset' => 'utf8'),
		'email2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'second email, can be NULL', 'charset' => 'utf8'),
		'phone' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => 'first phone number, can be NULL', 'charset' => 'utf8'),
		'phone2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => 'second phone number, can be NULL', 'charset' => 'utf8'),
		'fax' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => 'fax number, can be NULL', 'charset' => 'utf8'),
		'website' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => 'website url, can be NULL', 'charset' => 'utf8'),
		'privacy_level' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'level of privacy (0-9), default 0'),
		'newsletter_email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'email for newsletter subscription, can be NULL', 'charset' => 'utf8'),
		'mail_status' => array('type' => 'string', 'null' => false, 'default' => 'valid', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'status of email address (valid/blocked)', 'charset' => 'utf8'),
		'mail_bounce' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'comment' => 'mail bounce response, default 0'),
		'mail_last_bounce_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'date of last email check, can be NULL'),
		'mail_html' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => 'html confirmation email on subscription, default:1 (true)'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'area_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'label' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'label for category', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'category name', 'charset' => 'utf8'),
		'object_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'order priority'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'parent_path' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'path to parent, can be NULL', 'charset' => 'utf8'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'on', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'status of category (on/off)', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name_type' => array('column' => array('name', 'object_type_id'), 'unique' => 1), 'object_type_id' => array('column' => 'object_type_id', 'unique' => 0), 'index_label' => array('column' => 'label', 'unique' => 0), 'index_name' => array('column' => 'name', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'start_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'end_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'subject' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'abstract' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'body' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'duration' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'in seconds'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $date_items = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'start_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'start time, can be NULL'),
		'end_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => 'end time, can be NULL'),
		'params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'calendar params: e.g. days of week', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id' => array('column' => 'object_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $event_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'userid' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'event user', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'key' => 'index', 'comment' => 'event time'),
		'msg' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'log content', 'charset' => 'utf8'),
		'log_level' => array('type' => 'string', 'null' => false, 'default' => 'info', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'log level (debug, info, warn, err)', 'charset' => 'utf8'),
		'context' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => 'event context', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'userid_idx' => array('column' => 'userid', 'unique' => 0), 'date_idx' => array('column' => 'created', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $geo_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'latitude' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '9,6', 'comment' => 'latitude, can be NULL'),
		'longitude' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '9,6', 'comment' => 'longitude, can be NULL'),
		'address' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'address, can be NULL', 'charset' => 'utf8'),
		'title' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'geotag name/title', 'charset' => 'utf8'),
		'gmaps_lookat' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'google maps code, can be NULL', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id' => array('column' => 'object_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'comment' => 'group name', 'charset' => 'utf8'),
		'backend_auth' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'group authorized to backend (default: false)'),
		'immutable' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'group data immutable (default:false)'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $groups_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'groups_users_FKIndex1' => array('column' => 'user_id', 'unique' => 0), 'groups_users_FKIndex2' => array('column' => 'group_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $hash_jobs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'service_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'type of hash operations', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'serialized specific params for hash operation', 'charset' => 'utf8'),
		'hash' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'expired' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'comment' => 'hash expired datetime'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'pending', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'job status, can be pending/expired/closed/failed', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'hash' => array('column' => 'hash', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $history = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'object_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'title, can be NULL', 'charset' => 'utf8'),
		'area_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index', 'comment' => 'NULL in backend history'),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id' => array('column' => 'object_id', 'unique' => 0), 'user_id' => array('column' => 'user_id', 'unique' => 0), 'area_id' => array('column' => 'area_id', 'unique' => 0), 'url' => array('column' => 'url', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $images = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'width' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5, 'comment' => 'image width, can be NULL'),
		'height' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5, 'comment' => 'image height, can be NULL'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $lang_texts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'lang' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 3, 'collate' => 'utf8_general_ci', 'comment' => 'language of translation, for example ita, eng, por', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'field/attribute name', 'charset' => 'utf8'),
		'text' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'translation', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'lang_texts_FKIndex1' => array('column' => 'object_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $links = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'target' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => '(_self, _blank, parent, top, popup)', 'charset' => 'utf8'),
		'http_code' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'http_response_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'source_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'collate' => 'utf8_general_ci', 'comment' => 'can be rss, wikipedia, archive.org, localresource....', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'idx_url' => array('column' => 'url', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $mail_group_cards = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'mail_group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'card_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'pending', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'describe subscription status (pending, confirmed)', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'mail_group_card' => array('column' => array('card_id', 'mail_group_id'), 'unique' => 1), 'card_id_index' => array('column' => 'card_id', 'unique' => 0), 'mail_group_id_index' => array('column' => 'mail_group_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $mail_group_messages = array(
		'mail_group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'mail_message_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => array('mail_group_id', 'mail_message_id'), 'unique' => 1), 'mail_group_id_index' => array('column' => 'mail_group_id', 'unique' => 0), 'mail_message_id_index' => array('column' => 'mail_message_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $mail_groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'area_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'group_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '???'),
		'security' => array('type' => 'string', 'null' => false, 'default' => 'all', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'secure level (all, none)', 'charset' => 'utf8'),
		'confirmation_in_message' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'confirmation_out_message' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'group_name' => array('column' => 'group_name', 'unique' => 1), 'area_id' => array('column' => 'area_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $mail_jobs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'mail_message_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'card_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'unsent', 'length' => 10, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'job status (unsent, pending, sent, failed)', 'charset' => 'utf8'),
		'sending_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => '???'),
		'mail_body' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'recipient' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'email recipient, used if card_is and mail_message_id are null', 'charset' => 'utf8'),
		'mail_params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'serialized array with: reply-to, sender, subject, signature...', 'charset' => 'utf8'),
		'smtp_err' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'SMTP error message on sending failure', 'charset' => 'utf8'),
		'process_info' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index', 'comment' => 'pid of process delegates to send this mail job'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'card_id_index' => array('column' => 'card_id', 'unique' => 0), 'mail_message_id_index' => array('column' => 'mail_message_id', 'unique' => 0), 'process_info_index' => array('column' => 'process_info', 'unique' => 0), 'status_index' => array('column' => 'status', 'unique' => 0), 'recipient_index' => array('column' => 'recipient', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $mail_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'msg' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'log_level' => array('type' => 'string', 'null' => false, 'default' => 'info', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => '(info, warn, err)', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'key' => 'index', 'comment' => '???'),
		'recipient' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'subject' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'mail_params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'on failure, serialized array with: reply-to, sender, subject, signature...', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'recipient' => array('column' => 'recipient', 'unique' => 0), 'created' => array('column' => 'created', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $mail_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'mail_status' => array('type' => 'string', 'null' => false, 'default' => 'unsent', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'sending status (unsent, pending, injob, sent)', 'charset' => 'utf8'),
		'start_sending' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'end_sending' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'sender_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'newsletter sender name', 'charset' => 'utf8'),
		'sender' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'newsletter sender email', 'charset' => 'utf8'),
		'reply_to' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'bounce_to' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => '???'),
		'signature' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'privacy_disclaimer' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'stylesheet' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $modules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'label' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'on', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => '(on, off)', 'charset' => 'utf8'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'module_type' => array('type' => 'string', 'null' => false, 'default' => 'core', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => '(core, plugin)', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $object_categories = array(
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'category_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => array('object_id', 'category_id'), 'unique' => 1), 'objects_has_categories_FKIndex1' => array('column' => 'object_id', 'unique' => 0), 'objects_has_categories_FKIndex2' => array('column' => 'category_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $object_editors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'last_access' => array('type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id_index' => array('column' => 'object_id', 'unique' => 0), 'user_id_index' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $object_properties = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'property_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'property_value' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'property_id_index' => array('column' => 'property_id', 'unique' => 0), 'object_id' => array('column' => 'object_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $object_relations = array(
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'switch' => array('type' => 'string', 'null' => false, 'default' => 'attach', 'length' => 63, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'relation properties values', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => array('object_id', 'id', 'switch'), 'unique' => 1), 'related_objects_FKIndex1' => array('column' => 'id', 'unique' => 0), 'related_objects_FKIndex2' => array('column' => 'object_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $object_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'module_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $object_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'switch' => array('type' => 'string', 'null' => false, 'default' => 'card', 'length' => 63, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id_user_id_switch' => array('column' => array('object_id', 'user_id', 'switch'), 'unique' => 1), 'object_id_FKIndex1' => array('column' => 'object_id', 'unique' => 0), 'user_id_FKIndex2' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $objects = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => true, 'default' => 'draft', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => '(on, off, draft)', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'title' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'nickname' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'valid' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'comment' => '???'),
		'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'ip_created' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 15, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_created' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'user_modified' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'rights' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'license' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'creator' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'publisher' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'note' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'fixed' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '???'),
		'comments' => array('type' => 'string', 'null' => true, 'default' => 'off', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'define if an object is commentable (on, off, moderated)', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'nickname_idx' => array('column' => 'nickname', 'unique' => 1), 'objects_FKIndex1' => array('column' => 'object_type_id', 'unique' => 0), 'user_created' => array('column' => 'user_created', 'unique' => 0), 'user_modified' => array('column' => 'user_modified', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $permission_modules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'module_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'ugid' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'switch' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'permission type (user,group)', 'charset' => 'utf8'),
		'flag' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => '???'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'permission_modules_FKIndex1' => array('column' => 'module_id', 'unique' => 0), 'permission_modules_FKIndex3' => array('column' => 'ugid', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $permissions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'ugid' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'switch' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'permission type (user,group)', 'charset' => 'utf8'),
		'flag' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => '???'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'permissions_obj_inkdex' => array('column' => 'object_id', 'unique' => 0), 'permissions_ugid_switch' => array('column' => array('ugid', 'switch'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $products = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'abstract' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'body' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'serial_number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'weight' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'width' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'height' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'product_depth' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'volume' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'length_unit' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'weight_unit' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'volume_unit' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'color' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'production_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'production_place' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $properties = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'object_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'property_type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => '(number, date, text, options)', 'charset' => 'utf8'),
		'multiple_choice' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'comment' => '???'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name_type' => array('column' => array('name', 'object_type_id'), 'unique' => 1), 'name_index' => array('column' => 'name', 'unique' => 0), 'type_index' => array('column' => 'object_type_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $property_options = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'property_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'property_option' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'property_id' => array('column' => 'property_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $search_texts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'lang' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 3, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'content' => array('type' => 'text', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'relevance' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 4, 'comment' => 'importance (1-10) range'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id' => array('column' => array('object_id', 'lang'), 'unique' => 0), 'content' => array('column' => 'content', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $sections = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'syndicate' => array('type' => 'string', 'null' => true, 'default' => 'on', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => '(on, off)', 'charset' => 'utf8'),
		'priority_order' => array('type' => 'string', 'null' => true, 'default' => 'asc', 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'order of objects inserted in section (asc, desc)', 'charset' => 'utf8'),
		'last_modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'map_priority' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '2,1', 'comment' => '???'),
		'map_changefreq' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $streams = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'uri' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'mime_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 60, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'file_size' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => '???'),
		'hash_file' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'original_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => 'original name for uploaded file', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'hash_file_index' => array('column' => 'hash_file', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $trees = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'area_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'object_path' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'parent_path' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => '???'),
		'menu' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10),
		'indexes' => array('object_path' => array('column' => 'object_path', 'unique' => 1), 'id_idx' => array('column' => 'id', 'unique' => 0), 'parent_idx' => array('column' => 'parent_id', 'unique' => 0), 'area_idx' => array('column' => 'area_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $user_properties = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'property_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'property_value' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'property_id_index' => array('column' => 'property_id', 'unique' => 0), 'user_id' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'userid' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'realname' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'passwd' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'valid' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'last_login' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'last_login_err' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'num_login_err' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'user_level' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '???'),
		'auth_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'auth_params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'time_zone' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 9, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'comments' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'notify new comments option (never, mine, all)', 'charset' => 'utf8'),
		'notes' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10, 'collate' => 'utf8_general_ci', 'comment' => 'notify new notes option (never, mine, all)', 'charset' => 'utf8'),
		'notify_changes' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'reports' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'comment' => '???'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'userid' => array('column' => 'userid', 'unique' => 1), 'email' => array('column' => 'email', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $versions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'revision' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'comment' => '???'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'diff' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id_revision' => array('column' => array('object_id', 'revision'), 'unique' => 1), 'objects_index' => array('column' => 'object_id', 'unique' => 0), 'user_index' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $videos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'provider' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'video_uid' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'thumbnail' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
}
?>