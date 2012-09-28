<?php
// BEdita DB schema documentation
$doc = array();

// table: aliases
$doc['tables']['aliases'] = 'object unique name aliases (used mainly frontend URLs)';
$doc['aliases'] = array (
  'nickname_alias' => 'alternative nickname/unique name',
  'lang' => 'alias preferred language',
);

// table: annotations
$doc['tables']['annotations'] = 'object annotations, comments, notes';
$doc['annotations'] = array (
  'author' => 'author, if not a logged user',
  'email' => 'email, if not a logged user',
  'url' => 'author url',
  'thread_path' => 'path to thread, in the form /12/23/34',
  'rating' => 'numeric rating',
);

// table: applications
$doc['tables']['applications'] = 'applications, for example flash, java applet, etc.';
$doc['applications'] = array (
  'application_name' => 'name, for example flash',
  'application_label' => 'label for application, for example Adobe Flash',
  'application_version' => 'app version',
  'application_type' => 'mime type of application, for example application/x-shockwave-flash',
  'text_dir' => 'text orientation (ltr: left to right - rtl: right to left)',
  'text_lang' => 'text language',
  'width' => 'application window width in pixels',
  'height' => 'application window height in pixels',
);

// table: areas
$doc['tables']['areas'] = 'publications (web sites, etc.)';
$doc['areas'] = array (
  'public_name' => 'public name for publication',
  'public_url' => 'public url for publication',
  'staging_url' => 'staging/test url for publication',
  'email' => 'owner/support email',
  'stats_code' => 'statistics code for html, for example google stats code',
  'stats_provider' => 'statistics provider, for example google',
  'stats_provider_url' => 'statistics provider url',
);

// table: authors
$doc['tables']['authors'] = 'authors';
$doc['authors'] = array (
  'name' => 'author name',
  'surname' => 'author surname',
);

// table: banned_ips
$doc['tables']['banned_ips'] = 'banned ips (mainly for comments)';
$doc['banned_ips'] = array (
  'created' => 'creation time',
  'modified' => 'last modified time',
  'status' => 'ip status (ban, accept)',
);

// table: cards
$doc['tables']['cards'] = 'persons/companies cards, addressbook data, etc.';
$doc['cards'] = array (
  'name' => 'person name, can be NULL',
  'surname' => 'person surname, can be NULL',
  'person_title' => 'person title, for example Sir, Madame, Prof, Doct, ecc., can be NULL',
  'gender' => 'gender, for example male, female, can be NULL',
  'birthdate' => 'date of birth, can be NULL',
  'deathdate' => 'date of death, can be NULL',
  'company' => 'is a company, default: false',
  'company_name' => 'name of company, can be NULL',
  'company_kind' => 'type of company, can be NULL',
  'street_address' => 'address street, can be NULL',
  'city' => 'city, can be NULL',
  'zipcode' => 'zipcode, can be NULL',
  'country' => 'country, can be NULL',
  'state_name' => 'state, can be NULL',
  'email' => 'first email, can be NULL',
  'email2' => 'second email, can be NULL',
  'phone' => 'first phone number, can be NULL',
  'phone2' => 'second phone number, can be NULL',
  'fax' => 'fax number, can be NULL',
  'website' => 'website url, can be NULL',
  'privacy_level' => 'level of privacy (0-9), default 0',
  'newsletter_email' => 'email for newsletter subscription, can be NULL',
  'mail_status' => 'status of email address (valid/blocked)',
  'mail_bounce' => 'mail bounce response, default 0',
  'mail_last_bounce_date' => 'date of last email check, can be NULL',
  'mail_html' => 'html confirmation email on subscription, default:1 (true)',
);

// table: categories
$doc['tables']['categories'] = 'general categories';
$doc['categories'] = array (
  'label' => 'label for category',
  'name' => 'category name',
  'priority' => 'order priority',
  'parent_path' => 'path to parent, can be NULL',
  'status' => 'status of category (on/off)',
);

// table: contents
$doc['tables']['contents'] = 'general contents data';
$doc['contents'] = array (
  'duration' => 'in seconds',
);

// table: date_items
$doc['tables']['date_items'] = 'dates associated to objects';
$doc['date_items'] = array (
  'start_date' => 'start time, can be NULL',
  'end_date' => 'end time, can be NULL',
);

// table: event_logs
$doc['tables']['event_logs'] = 'backend main events log';
$doc['event_logs'] = array (
  'userid' => 'event user',
  'created' => 'event time',
  'msg' => 'log content',
  'log_level' => 'log level (debug, info, warn, err)',
  'context' => 'event context',
);

// table: geo_tags
$doc['tables']['geo_tags'] = 'geotagging informations';
$doc['geo_tags'] = array (
  'latitude' => 'latitude, can be NULL',
  'longitude' => 'longitude, can be NULL',
  'address' => 'address, can be NULL',
  'gmaps_lookat' => 'google maps code, can be NULL',
);

// table: groups
$doc['tables']['groups'] = 'generic groups';
$doc['groups'] = array (
  'name' => 'group name',
  'backend_auth' => 'group authorized to backend (default: false)',
  'immutable' => 'group data immutable (default:false)',
);

// table: groups_users
$doc['tables']['groups_users'] = 'join table for groups/users';
$doc['groups_users'] = array (
);

// table: hash_jobs
$doc['tables']['hash_jobs'] = 'contains hash operations, for example subscribe/unsubscribe';
$doc['hash_jobs'] = array (
  'service_type' => 'type of hash operations',
  'params' => 'serialized specific params for hash operation',
  'expired' => 'hash expired datetime',
  'status' => 'job status, can be pending/expired/closed/failed',
);

// table: history
$doc['tables']['history'] = 'history of users navigation, can be in backend/frontend';
$doc['history'] = array (
  'title' => 'title, can be NULL',
  'area_id' => 'NULL in backend history',
  'url' => '???',
);

// table: images
$doc['tables']['images'] = 'image data';
$doc['images'] = array (
  'width' => 'image width, can be NULL',
  'height' => 'image height, can be NULL',
);

// table: lang_texts
$doc['tables']['lang_texts'] = 'translations of object fields/attributes';
$doc['lang_texts'] = array (
  'lang' => 'language of translation, for example ita, eng, por',
  'name' => 'field/attribute name',
  'text' => 'translation',
);

// table: links
$doc['tables']['links'] = '???';
$doc['links'] = array (
  'url' => '???',
  'target' => '(_self, _blank, parent, top, popup)',
  'http_code' => '???',
  'http_response_date' => '???',
  'source_type' => 'can be rss, wikipedia, archive.org, localresource....',
);

// table: mail_group_cards
$doc['tables']['mail_group_cards'] = '???';
$doc['mail_group_cards'] = array (
  'status' => 'describe subscription status (pending, confirmed)',
  'created' => '???',
);

// table: mail_group_messages
$doc['tables']['mail_group_messages'] = '???';
$doc['mail_group_messages'] = array (
);

// table: mail_groups
$doc['tables']['mail_groups'] = '???';
$doc['mail_groups'] = array (
  'group_name' => '???',
  'visible' => '???',
  'security' => 'secure level (all, none)',
  'confirmation_in_message' => '???',
  'confirmation_out_message' => '???',
);

// table: mail_jobs
$doc['tables']['mail_jobs'] = '???';
$doc['mail_jobs'] = array (
  'status' => 'job status (unsent, pending, sent, failed)',
  'sending_date' => '???',
  'created' => '???',
  'modified' => '???',
  'priority' => '???',
  'mail_body' => '???',
  'recipient' => 'email recipient, used if card_is and mail_message_id are null',
  'mail_params' => 'serialized array with: reply-to, sender, subject, signature...',
  'smtp_err' => 'SMTP error message on sending failure',
  'process_info' => 'pid of process delegates to send this mail job',
);

// table: mail_logs
$doc['tables']['mail_logs'] = '???';
$doc['mail_logs'] = array (
  'msg' => '???',
  'log_level' => '(info, warn, err)',
  'created' => '???',
  'recipient' => '???',
  'subject' => '???',
  'mail_params' => 'on failure, serialized array with: reply-to, sender, subject, signature...',
);

// table: mail_messages
$doc['tables']['mail_messages'] = '???';
$doc['mail_messages'] = array (
  'mail_status' => '???',
  'start_sending' => '???',
  'end_sending' => '???',
  'sender_name' => 'newsletter sender name',
  'sender' => 'newsletter sender email',
  'reply_to' => '???',
  'bounce_to' => '???',
  'priority' => '???',
  'signature' => '???',
  'privacy_disclaimer' => '???',
  'stylesheet' => '???',
);

// table: modules
$doc['tables']['modules'] = '???';
$doc['modules'] = array (
  'name' => '???',
  'label' => '???',
  'url' => '???',
  'status' => '(on, off)',
  'priority' => '???',
  'module_type' => '(core, plugin)',
);

// table: object_categories
$doc['tables']['object_categories'] = '???';
$doc['object_categories'] = array (
);

// table: object_editors
$doc['tables']['object_editors'] = '???';
$doc['object_editors'] = array (
);

// table: object_properties
$doc['tables']['object_properties'] = '???';
$doc['object_properties'] = array (
);

// table: object_relations
$doc['tables']['object_relations'] = '???';
$doc['object_relations'] = array (
  'switch' => '???',
  'priority' => '???',
);

// table: object_types
$doc['tables']['object_types'] = '???';
$doc['object_types'] = array (
);

// table: object_users
$doc['tables']['object_users'] = '???';
$doc['object_users'] = array (
  'switch' => '???',
  'priority' => '???',
  'params' => '???',
);

// table: objects
$doc['tables']['objects'] = '???';
$doc['objects'] = array (
  'status' => '(on, off, draft)',
  'title' => '???',
  'nickname' => '???',
  'description' => '???',
  'valid' => '???',
  'lang' => '???',
  'rights' => '???',
  'license' => '???',
  'creator' => '???',
  'publisher' => '???',
  'note' => '???',
  'fixed' => '???',
  'comments' => 'define if an object is commentable (on, off, moderated)',
);

// table: permission_modules
$doc['tables']['permission_modules'] = '???';
$doc['permission_modules'] = array (
  'switch' => '???',
  'flag' => '???',
);

// table: permissions
$doc['tables']['permissions'] = '???';
$doc['permissions'] = array (
  'switch' => '???',
  'flag' => '???',
);

// table: products
$doc['tables']['products'] = '???';
$doc['products'] = array (
  'abstract' => '???',
  'body' => '???',
  'serial_number' => '???',
  'weight' => '???',
  'width' => '???',
  'height' => '???',
  'product_depth' => '???',
  'volume' => '???',
  'length_unit' => '???',
  'weight_unit' => '???',
  'volume_unit' => '???',
  'color' => '???',
  'production_date' => '???',
  'production_place' => '???',
);

// table: properties
$doc['tables']['properties'] = '???';
$doc['properties'] = array (
  'property_type' => '???',
  'multiple_choice' => '???',
);

// table: property_options
$doc['tables']['property_options'] = '???';
$doc['property_options'] = array (
  'property_option' => '???',
);

// table: search_texts
$doc['tables']['search_texts'] = 'searchable text, index table';
$doc['search_texts'] = array (
  'lang' => 'text language',
  'content' => 'actual text to index',
  'relevance' => 'importance (1-10) range',
);

// table: sections
$doc['tables']['sections'] = '???';
$doc['sections'] = array (
  'syndicate' => '(on, off)',
  'priority_order' => 'order of objects inserted in section (asc, desc)',
  'map_priority' => '???',
  'map_changefreq' => '???',
);

// table: streams
$doc['tables']['streams'] = '???';
$doc['streams'] = array (
  'uri' => '???',
  'name' => '???',
  'mime_type' => '???',
  'file_size' => '???',
  'hash_file' => '???',
);

// table: trees
$doc['tables']['trees'] = '???';
$doc['trees'] = array (
  'object_path' => '???',
  'parent_path' => '???',
  'priority' => '???',
  'menu' => '???',
);

// table: user_properties
$doc['tables']['user_properties'] = 'user custom properties values';
$doc['user_properties'] = array (
);

// table: users
$doc['tables']['users'] = '???';
$doc['users'] = array (
  'user_level' => '???',
  'auth_type' => '???',
  'auth_params' => '???',
  'lang' => '???',
  'time_zone' => '???',
  'comments' => 'notify new comments option (never, mine, all)',
  'notes' => 'notify new notes option (never, mine, all)',
  'notify_changes' => '???',
  'reports' => '???',
);

// table: versions
$doc['tables']['versions'] = 'object versioning data';
$doc['versions'] = array (
  'revision' => 'revision number, from 1 to N-1',
  'diff' => 'serialized incremental difference',
);

// table: videos
$doc['tables']['videos'] = 'video contents';
$doc['videos'] = array (
  'provider' => 'provider name: youtube, vimeo,...',
  'video_uid' => 'video identifier for provider',
  'thumbnail' => 'thumbnail image url',
);

?>