<?php 
/* SVN FILE: $Id$ */
/* BeditaApp schema generated on: 2010-08-10 12:08:58 : 1281435298*/
class BeditaAppSchema extends CakeSchema {
	var $name = 'BeditaApp';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $aliases = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'nickname_alias' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'unique'),
		'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'nickname_alias' => array('column' => 'nickname_alias', 'unique' => 1), 'object_id' => array('column' => 'object_id', 'unique' => 0))
	);
	var $annotations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'author' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'thread_path' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'rating' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'author_idx' => array('column' => 'author', 'unique' => 0), 'objects_idx' => array('column' => 'object_id', 'unique' => 0))
	);
	var $applications = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'application_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'application_label' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'application_version' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'values' => NULL),
		'application_type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'text_dir' => array('type' => 'string', 'null' => true, 'default' => 'ltr', 'length' => 10, 'values' => NULL),
		'text_lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'width' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5, 'values' => NULL),
		'height' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $areas = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'public_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'public_url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'staging_url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'stats_code' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'stats_provider' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'stats_provider_url' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $authors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 60, 'values' => NULL),
		'surname' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 60, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $banned_ips = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'ip_address' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 15, 'values' => NULL, 'key' => 'unique'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'values' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'values' => NULL),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'ban', 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ip_unique' => array('column' => 'ip_address', 'unique' => 1), 'status_idx' => array('column' => 'status', 'unique' => 0))
	);
	var $cake_sessions = array(
		'id' => array('type' => 'string', 'null' => false, 'values' => NULL, 'key' => 'primary'),
		'data' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'expires' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $cards = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'values' => NULL),
		'surname' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'values' => NULL),
		'person_title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'gender' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'birthdate' => array('type' => 'date', 'null' => true, 'default' => NULL, 'values' => NULL),
		'deathdate' => array('type' => 'date', 'null' => true, 'default' => NULL, 'values' => NULL),
		'company' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'values' => NULL),
		'company_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'company_kind' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'values' => NULL),
		'street_address' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'city' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'zipcode' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'country' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'state_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'email2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'phone' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'phone2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'fax' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'website' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'privacy_level' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'values' => NULL),
		'newsletter_email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'mail_status' => array('type' => 'string', 'null' => false, 'default' => 'valid', 'length' => 10, 'values' => NULL),
		'mail_bounce' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'values' => NULL),
		'mail_last_bounce_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'mail_html' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'area_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'label' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'object_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'parent_path' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'on', 'length' => 10, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name_type' => array('column' => array('name', 'object_type_id'), 'unique' => 1), 'object_type_id' => array('column' => 'object_type_id', 'unique' => 0), 'index_label' => array('column' => 'label', 'unique' => 0), 'index_name' => array('column' => 'name', 'unique' => 0))
	);
	var $contents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'start_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'end_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'subject' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'abstract' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'body' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'duration' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $date_items = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'start_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'end_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id' => array('column' => 'object_id', 'unique' => 0))
	);
	var $event_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'userid' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'values' => NULL, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'msg' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'values' => NULL),
		'log_level' => array('type' => 'string', 'null' => false, 'default' => 'info', 'length' => 10, 'values' => NULL),
		'context' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'userid_idx' => array('column' => 'userid', 'unique' => 0), 'date_idx' => array('column' => 'created', 'unique' => 0))
	);
	var $geo_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'latitude' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '9,6', 'values' => NULL),
		'longitude' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '9,6', 'values' => NULL),
		'address' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'gmaps_lookat' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id' => array('column' => 'object_id', 'unique' => 0))
	);
	var $groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'values' => NULL, 'key' => 'unique'),
		'backend_auth' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'values' => NULL),
		'immutable' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'values' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1))
	);
	var $groups_users = array(
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => array('user_id', 'group_id'), 'unique' => 1), 'groups_users_FKIndex1' => array('column' => 'user_id', 'unique' => 0), 'groups_users_FKIndex2' => array('column' => 'group_id', 'unique' => 0))
	);
	var $hash_jobs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'primary'),
		'service_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'hash' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'unique'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'values' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'values' => NULL),
		'expired' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'values' => NULL),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'pending', 'length' => 10, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'hash' => array('column' => 'hash', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 0))
	);
	var $history = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'object_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'area_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id' => array('column' => 'object_id', 'unique' => 0), 'user_id' => array('column' => 'user_id', 'unique' => 0), 'area_id' => array('column' => 'area_id', 'unique' => 0), 'url' => array('column' => 'url', 'unique' => 0))
	);
	var $images = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'width' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5, 'values' => NULL),
		'height' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $lang_texts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'lang' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 3, 'values' => NULL),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'text' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'lang_texts_FKIndex1' => array('column' => 'object_id', 'unique' => 0))
	);
	var $links = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'target' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'http_code' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'http_response_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'source_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'idx_url' => array('column' => 'url', 'unique' => 0))
	);
	var $mail_group_cards = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'mail_group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'card_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'pending', 'length' => 10, 'values' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'mail_group_card' => array('column' => array('card_id', 'mail_group_id'), 'unique' => 1), 'card_id_index' => array('column' => 'card_id', 'unique' => 0), 'mail_group_id_index' => array('column' => 'mail_group_id', 'unique' => 0))
	);
	var $mail_group_messages = array(
		'mail_group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'mail_message_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => array('mail_group_id', 'mail_message_id'), 'unique' => 1), 'mail_group_id_index' => array('column' => 'mail_group_id', 'unique' => 0), 'mail_message_id_index' => array('column' => 'mail_message_id', 'unique' => 0))
	);
	var $mail_groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'area_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'group_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'unique'),
		'visible' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'values' => NULL),
		'security' => array('type' => 'string', 'null' => false, 'default' => 'all', 'length' => 10, 'values' => NULL),
		'confirmation_in_message' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'confirmation_out_message' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'group_name' => array('column' => 'group_name', 'unique' => 1), 'area_id' => array('column' => 'area_id', 'unique' => 0))
	);
	var $mail_jobs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'mail_message_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'card_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'unsent', 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'sending_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'mail_body' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'recipient' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'mail_params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'smtp_err' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'process_info' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'card_id_index' => array('column' => 'card_id', 'unique' => 0), 'mail_message_id_index' => array('column' => 'mail_message_id', 'unique' => 0), 'process_info_index' => array('column' => 'process_info', 'unique' => 0), 'status_index' => array('column' => 'status', 'unique' => 0), 'recipient_index' => array('column' => 'recipient', 'unique' => 0))
	);
	var $mail_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'msg' => array('type' => 'text', 'null' => false, 'default' => NULL, 'values' => NULL),
		'log_level' => array('type' => 'string', 'null' => false, 'default' => 'info', 'length' => 10, 'values' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'recipient' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'subject' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'mail_params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'recipient' => array('column' => 'recipient', 'unique' => 0), 'created' => array('column' => 'created', 'unique' => 0))
	);
	var $mail_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'mail_status' => array('type' => 'enum', 'null' => false, 'default' => 'unsent', 'length' => 7, 'values' => '\'unsent\',\'pending\',\'injob\',\'sent\''),
		'start_sending' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'end_sending' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'sender_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'sender' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'reply_to' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'bounce_to' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'signature' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'privacy_disclaimer' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'stylesheet' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $modules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'values' => NULL, 'key' => 'unique'),
		'label' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'status' => array('type' => 'string', 'null' => false, 'default' => 'on', 'length' => 10, 'values' => NULL),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'values' => NULL),
		'module_type' => array('type' => 'string', 'null' => false, 'default' => 'core', 'length' => 10, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1))
	);
	var $object_categories = array(
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'category_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => array('object_id', 'category_id'), 'unique' => 1), 'objects_has_categories_FKIndex1' => array('column' => 'object_id', 'unique' => 0), 'objects_has_categories_FKIndex2' => array('column' => 'category_id', 'unique' => 0))
	);
	var $object_editors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'last_access' => array('type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id_index' => array('column' => 'object_id', 'unique' => 0), 'user_id_index' => array('column' => 'user_id', 'unique' => 0))
	);
	var $object_properties = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'property_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'property_value' => array('type' => 'text', 'null' => false, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'property_id_index' => array('column' => 'property_id', 'unique' => 0), 'object_id' => array('column' => 'object_id', 'unique' => 0))
	);
	var $object_relations = array(
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'switch' => array('type' => 'string', 'null' => false, 'default' => 'attach', 'length' => 63, 'values' => NULL, 'key' => 'primary'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => array('object_id', 'id', 'switch'), 'unique' => 1), 'related_objects_FKIndex1' => array('column' => 'id', 'unique' => 0), 'related_objects_FKIndex2' => array('column' => 'object_id', 'unique' => 0))
	);
	var $object_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL, 'key' => 'unique'),
		'module_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1))
	);
	var $object_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'switch' => array('type' => 'string', 'null' => false, 'default' => 'card', 'length' => 63, 'values' => NULL),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'values' => NULL),
		'params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id_user_id_switch' => array('column' => array('object_id', 'user_id', 'switch'), 'unique' => 1), 'object_id_FKIndex1' => array('column' => 'object_id', 'unique' => 0), 'user_id_FKIndex2' => array('column' => 'user_id', 'unique' => 0))
	);
	var $objects = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'status' => array('type' => 'string', 'null' => true, 'default' => 'draft', 'length' => 10, 'values' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'nickname' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'valid' => array('type' => 'boolean', 'null' => true, 'default' => '1', 'values' => NULL),
		'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3, 'values' => NULL),
		'ip_created' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 15, 'values' => NULL),
		'user_created' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'user_modified' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'rights' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'license' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'creator' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'publisher' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'note' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'fixed' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'values' => NULL),
		'comments' => array('type' => 'string', 'null' => true, 'default' => 'off', 'length' => 10, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'objects_FKIndex1' => array('column' => 'object_type_id', 'unique' => 0), 'user_created' => array('column' => 'user_created', 'unique' => 0), 'user_modified' => array('column' => 'user_modified', 'unique' => 0))
	);
	var $permission_modules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'module_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'ugid' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'switch' => array('type' => 'set', 'null' => true, 'default' => NULL, 'length' => 5, 'values' => '\'user\',\'group\''),
		'flag' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'permission_modules_FKIndex1' => array('column' => 'module_id', 'unique' => 0), 'permission_modules_FKIndex3' => array('column' => 'ugid', 'unique' => 0))
	);
	var $permissions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'ugid' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'switch' => array('type' => 'set', 'null' => false, 'default' => NULL, 'length' => 5, 'values' => '\'user\',\'group\''),
		'flag' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'permissions_obj_inkdex' => array('column' => 'object_id', 'unique' => 0), 'permissions_ugid_switch' => array('column' => array('ugid', 'switch'), 'unique' => 0))
	);
	var $products = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'abstract' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'body' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'serial_number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'weight' => array('type' => 'float', 'null' => true, 'default' => NULL, 'values' => NULL),
		'width' => array('type' => 'float', 'null' => true, 'default' => NULL, 'values' => NULL),
		'height' => array('type' => 'float', 'null' => true, 'default' => NULL, 'values' => NULL),
		'product_depth' => array('type' => 'float', 'null' => true, 'default' => NULL, 'values' => NULL),
		'volume' => array('type' => 'float', 'null' => true, 'default' => NULL, 'values' => NULL),
		'length_unit' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40, 'values' => NULL),
		'weight_unit' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40, 'values' => NULL),
		'volume_unit' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40, 'values' => NULL),
		'color' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'production_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'production_place' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $properties = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'object_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'property_type' => array('type' => 'set', 'null' => false, 'default' => NULL, 'length' => 7, 'values' => '\'number\',\'date\',\'text\',\'options\''),
		'multiple_choice' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name_type' => array('column' => array('name', 'object_type_id'), 'unique' => 1), 'name_index' => array('column' => 'name', 'unique' => 0), 'type_index' => array('column' => 'object_type_id', 'unique' => 0))
	);
	var $property_options = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'property_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'property_option' => array('type' => 'text', 'null' => false, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'property_id' => array('column' => 'property_id', 'unique' => 0))
	);
	var $search_texts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'lang' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 3, 'values' => NULL),
		'content' => array('type' => 'text', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'relevance' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 4, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id' => array('column' => array('object_id', 'lang'), 'unique' => 0), 'content' => array('column' => 'content', 'unique' => 0))
	);
	var $sections = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'syndicate' => array('type' => 'string', 'null' => true, 'default' => 'on', 'length' => 10, 'values' => NULL),
		'priority_order' => array('type' => 'string', 'null' => true, 'default' => 'asc', 'length' => 10, 'values' => NULL),
		'last_modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'map_priority' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '2,1', 'values' => NULL),
		'map_changefreq' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 128, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
	var $streams = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'uri' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'mime_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 60, 'values' => NULL),
		'file_size' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'hash_file' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'hash_file_index' => array('column' => 'hash_file', 'unique' => 0))
	);
	var $trees = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'area_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'object_path' => array('type' => 'string', 'null' => false, 'default' => NULL, 'values' => NULL, 'key' => 'primary'),
		'parent_path' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'menu' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'indexes' => array('object_path' => array('column' => 'object_path', 'unique' => 1), 'id_idx' => array('column' => 'id', 'unique' => 0), 'parent_idx' => array('column' => 'parent_id', 'unique' => 0), 'area_idx' => array('column' => 'area_id', 'unique' => 0))
	);
	var $user_properties = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'property_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'property_value' => array('type' => 'text', 'null' => false, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'property_id_index' => array('column' => 'property_id', 'unique' => 0), 'user_id' => array('column' => 'user_id', 'unique' => 0))
	);
	var $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'userid' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'values' => NULL, 'key' => 'unique'),
		'realname' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'passwd' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL, 'key' => 'unique'),
		'valid' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'values' => NULL),
		'last_login' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'last_login_err' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'num_login_err' => array('type' => 'integer', 'null' => false, 'default' => '0', 'values' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'values' => NULL),
		'user_level' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'values' => NULL),
		'auth_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'auth_params' => array('type' => 'text', 'null' => true, 'default' => NULL, 'values' => NULL),
		'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3, 'values' => NULL),
		'time_zone' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 9, 'values' => NULL),
		'comments' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'notes' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'notify_changes' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'values' => NULL),
		'reports' => array('type' => 'boolean', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'userid' => array('column' => 'userid', 'unique' => 1), 'email' => array('column' => 'email', 'unique' => 1))
	);
	var $versions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'object_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'revision' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'values' => NULL),
		'diff' => array('type' => 'text', 'null' => false, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'object_id_revision' => array('column' => array('object_id', 'revision'), 'unique' => 1), 'objects_index' => array('column' => 'object_id', 'unique' => 0), 'user_index' => array('column' => 'user_id', 'unique' => 0))
	);
	var $videos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'values' => NULL, 'key' => 'primary'),
		'provider' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'video_uid' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'thumbnail' => array('type' => 'string', 'null' => true, 'default' => NULL, 'values' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
?>