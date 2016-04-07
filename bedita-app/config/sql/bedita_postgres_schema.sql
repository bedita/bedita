DROP TABLE IF EXISTS aliases CASCADE;
DROP TABLE IF EXISTS annotations CASCADE;
DROP TABLE IF EXISTS applications CASCADE;
DROP TABLE IF EXISTS areas CASCADE;
DROP TABLE IF EXISTS banned_ips;
DROP TABLE IF EXISTS cake_sessions;
DROP TABLE IF EXISTS cards CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS contents CASCADE;
DROP TABLE IF EXISTS date_items CASCADE;
DROP TABLE IF EXISTS event_logs CASCADE;
DROP TABLE IF EXISTS geo_tags CASCADE;
DROP TABLE IF EXISTS groups CASCADE;
DROP TABLE IF EXISTS groups_users CASCADE;
DROP TABLE IF EXISTS hash_jobs CASCADE;
DROP TABLE IF EXISTS history CASCADE;
DROP TABLE IF EXISTS images CASCADE;
DROP TABLE IF EXISTS lang_texts CASCADE;
DROP TABLE IF EXISTS links CASCADE;
DROP TABLE IF EXISTS mail_groups CASCADE;
DROP TABLE IF EXISTS mail_group_cards CASCADE;
DROP TABLE IF EXISTS mail_group_messages CASCADE;
DROP TABLE IF EXISTS mail_jobs CASCADE;
DROP TABLE IF EXISTS mail_logs CASCADE;
DROP TABLE IF EXISTS mail_messages CASCADE;
DROP TABLE IF EXISTS modules CASCADE;
DROP TABLE IF EXISTS objects CASCADE;
DROP TABLE IF EXISTS object_categories CASCADE;
DROP TABLE IF EXISTS object_editors CASCADE;
DROP TABLE IF EXISTS object_properties CASCADE;
DROP TABLE IF EXISTS object_relations CASCADE;
DROP TABLE IF EXISTS object_types CASCADE;
DROP TABLE IF EXISTS object_users CASCADE;
DROP TABLE IF EXISTS permissions CASCADE;
DROP TABLE IF EXISTS permission_modules CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS properties CASCADE;
DROP TABLE IF EXISTS property_options CASCADE;
DROP TABLE IF EXISTS search_texts CASCADE;
DROP TABLE IF EXISTS sections CASCADE;
DROP TABLE IF EXISTS section_types CASCADE;
DROP TABLE IF EXISTS streams CASCADE;
DROP TABLE IF EXISTS trees CASCADE;
DROP TABLE IF EXISTS user_properties CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS versions CASCADE;
DROP TABLE IF EXISTS videos CASCADE;


CREATE TABLE aliases (
    id serial,
    object_id integer NOT NULL,
    nickname_alias character varying(255) NOT NULL,
    lang character(3)
);


COMMENT ON COLUMN aliases.nickname_alias IS 'alternative nickname';
COMMENT ON COLUMN aliases.lang IS 'alias preferred language, can be NULL';


CREATE TABLE annotations (
    id integer NOT NULL,
    object_id integer NOT NULL,
    author character varying(255),
    email character varying(255),
    url character varying(255),
    thread_path text,
    rating integer
);

COMMENT ON COLUMN annotations.author IS 'annotation author';
COMMENT ON COLUMN annotations.email IS 'annotation author email';
COMMENT ON COLUMN annotations.url IS 'annotation url, can be NULL';
COMMENT ON COLUMN annotations.thread_path IS 'path to thread, can be NULL';
COMMENT ON COLUMN annotations.rating IS 'object rating, can be NULL';

CREATE TABLE applications (
    id integer NOT NULL,
    application_name character varying(255) NOT NULL,
    application_label character varying(255),
    application_version character varying(50),
    application_type character varying(255) NOT NULL,
    text_dir character varying(10) DEFAULT 'ltr'::character varying,
    text_lang character varying(255),
    width integer,
    height integer
);


COMMENT ON COLUMN applications.application_name IS 'name of application, for example flash';
COMMENT ON COLUMN applications.application_label IS 'label for application, for example Adobe Flash, can be NULL';
COMMENT ON COLUMN applications.application_version IS 'version of application, can be NULL';
COMMENT ON COLUMN applications.application_type IS 'type of application, for example application/x-shockwave-flash';
COMMENT ON COLUMN applications.text_dir IS 'text orientation (ltr:left to right;rtl: right to left)';
COMMENT ON COLUMN applications.text_lang IS 'text language, can be NULL';
COMMENT ON COLUMN applications.width IS 'application window width in pixels';
COMMENT ON COLUMN applications.height IS 'application window height in pixels';

CREATE TABLE areas (
    id integer NOT NULL,
    public_name character varying(255),
    public_url character varying(255),
    staging_url character varying(255),
    email character varying(255),
    stats_code text,
    stats_provider character varying(255),
    stats_provider_url text
);

COMMENT ON COLUMN areas.public_name IS 'public name for publication, can be NULL';
COMMENT ON COLUMN areas.public_url IS 'public url for publication, can be NULL';
COMMENT ON COLUMN areas.staging_url IS 'staging/test url for publication, can be NULL';
COMMENT ON COLUMN areas.email IS 'publication email, can be NULL';
COMMENT ON COLUMN areas.stats_code IS 'statistics code, for example google stats code. can be NULL';
COMMENT ON COLUMN areas.stats_provider IS 'statistics provider, for example google. can be NULL';
COMMENT ON COLUMN areas.stats_provider_url IS 'statistics provider url';


CREATE TABLE banned_ips (
    id serial,
    ip_address character varying(15) NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    status character varying(10) DEFAULT 'ban'::character varying NOT NULL
);


COMMENT ON COLUMN banned_ips.created IS 'creation time';
COMMENT ON COLUMN banned_ips.modified IS 'last modified time';
COMMENT ON COLUMN banned_ips.status IS 'ip status (ban, accept)';

CREATE TABLE cake_sessions (
    id character varying(255) NOT NULL,
    data text,
    expires integer
);


CREATE TABLE cards (
    id integer NOT NULL,
    name character varying(64),
    surname character varying(64),
    person_title character varying(32),
    gender character varying(32),
    birthdate date,
    deathdate date,
    company boolean DEFAULT false NOT NULL,
    company_name character varying(128),
    company_kind character varying(64),
    street_address character varying(255),
    city character varying(255),
    zipcode character varying(32),
    country character varying(128),
    state_name character varying(128),
    email character varying(128),
    email2 character varying(128),
    phone character varying(32),
    phone2 character varying(32),
    fax character varying(32),
    website character varying(128),
    privacy_level boolean DEFAULT false NOT NULL,
    newsletter_email character varying(255),
    mail_status character varying(10) DEFAULT 'valid'::character varying NOT NULL,
    mail_bounce integer DEFAULT 0 NOT NULL,
    mail_last_bounce_date timestamp without time zone,
    mail_html boolean DEFAULT true NOT NULL
);


COMMENT ON COLUMN cards.name IS 'person name, can be NULL';
COMMENT ON COLUMN cards.surname IS 'person surname, can be NULL';
COMMENT ON COLUMN cards.person_title IS 'person title, for example Sir, Madame, Prof, Doct, ecc., can be NULL';
COMMENT ON COLUMN cards.gender IS 'gender, for example male, female, can be NULL';
COMMENT ON COLUMN cards.birthdate IS 'date of birth, can be NULL';
COMMENT ON COLUMN cards.deathdate IS 'date of death, can be NULL';
COMMENT ON COLUMN cards.company IS 'is a company, default: false';
COMMENT ON COLUMN cards.company_name IS 'name of company, can be NULL';
COMMENT ON COLUMN cards.company_kind IS 'type of company, can be NULL';
COMMENT ON COLUMN cards.street_address IS 'address street, can be NULL';
COMMENT ON COLUMN cards.city IS 'city, can be NULL';
COMMENT ON COLUMN cards.zipcode IS 'zipcode, can be NULL';
COMMENT ON COLUMN cards.country IS 'country, can be NULL';
COMMENT ON COLUMN cards.state_name IS 'state, can be NULL';
COMMENT ON COLUMN cards.email IS 'first email, can be NULL';
COMMENT ON COLUMN cards.email2 IS 'second email, can be NULL';
COMMENT ON COLUMN cards.phone IS 'first phone number, can be NULL';
COMMENT ON COLUMN cards.phone2 IS 'second phone number, can be NULL';
COMMENT ON COLUMN cards.fax IS 'fax number, can be NULL';
COMMENT ON COLUMN cards.website IS 'website url, can be NULL';
COMMENT ON COLUMN cards.privacy_level IS 'level of privacy (0-9), default 0';
COMMENT ON COLUMN cards.newsletter_email IS 'email for newsletter subscription, can be NULL';
COMMENT ON COLUMN cards.mail_status IS 'status of email address (valid/blocked)';
COMMENT ON COLUMN cards.mail_bounce IS 'mail bounce response, default 0';
COMMENT ON COLUMN cards.mail_last_bounce_date IS 'date of last email check, can be NULL';
COMMENT ON COLUMN cards.mail_html IS 'html confirmation email on subscription, default:1 (true)';


CREATE TABLE categories (
    id serial,
    area_id integer,
    label character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    object_type_id integer,
    priority integer,
    parent_id integer,
    parent_path text,
    status character varying(10) DEFAULT 'on'::character varying NOT NULL
);


COMMENT ON COLUMN categories.label IS 'label for category';
COMMENT ON COLUMN categories.name IS 'category name';
COMMENT ON COLUMN categories.priority IS 'order priority';
COMMENT ON COLUMN categories.parent_path IS 'path to parent, can be NULL';
COMMENT ON COLUMN categories.status IS 'status of category (on/off)';

CREATE TABLE contents (
    id integer NOT NULL,
    start_date timestamp without time zone,
    end_date timestamp without time zone,
    subject character varying(255),
    abstract text,
    body text,
    duration integer
);


COMMENT ON COLUMN contents.duration IS 'in seconds';


CREATE TABLE date_items (
    id serial,
    object_id integer NOT NULL,
    start_date timestamp without time zone,
    end_date timestamp without time zone,
    params text
);


COMMENT ON COLUMN date_items.start_date IS 'start time, can be NULL';
COMMENT ON COLUMN date_items.end_date IS 'end time, can be NULL';
COMMENT ON COLUMN date_items.params IS 'calendar params: e.g. days of week';


CREATE TABLE event_logs (
    id serial,
    userid character varying(200) NOT NULL,
    created timestamp without time zone NOT NULL,
    msg text NOT NULL,
    log_level character varying(10) DEFAULT 'info'::character varying NOT NULL,
    context character varying(32)
);


COMMENT ON COLUMN event_logs.userid IS 'event user';
COMMENT ON COLUMN event_logs.created IS 'event time';
COMMENT ON COLUMN event_logs.msg IS 'log content';
COMMENT ON COLUMN event_logs.log_level IS 'log level (debug, info, warn, err)';
COMMENT ON COLUMN event_logs.context IS 'event context';


CREATE TABLE geo_tags (
    id serial,
    object_id integer NOT NULL,
    latitude double precision,
    longitude double precision,
    address text,
    title text,
    gmaps_lookat text
);


COMMENT ON COLUMN geo_tags.latitude IS 'latitude, can be NULL';
COMMENT ON COLUMN geo_tags.longitude IS 'longitude, can be NULL';
COMMENT ON COLUMN geo_tags.address IS 'address, can be NULL';
COMMENT ON COLUMN geo_tags.gmaps_lookat IS 'google maps code, can be NULL';


CREATE TABLE groups (
    id serial,
    name character varying(32) NOT NULL,
    backend_auth boolean DEFAULT false NOT NULL,
    immutable boolean DEFAULT false NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


COMMENT ON COLUMN groups.name IS 'group name';
COMMENT ON COLUMN groups.backend_auth IS 'group authorized to backend (default: false)';
COMMENT ON COLUMN groups.immutable IS 'group data immutable (default:false)';


CREATE TABLE groups_users (
    user_id integer NOT NULL,
    group_id integer NOT NULL
);


CREATE TABLE hash_jobs (
    id serial,
    service_type character varying(255),
    user_id integer NOT NULL,
    params text,
    hash character varying(255) NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    result text,
    expired timestamp without time zone NOT NULL,
    status character varying(20) DEFAULT 'pending'::character varying NOT NULL
);


COMMENT ON COLUMN hash_jobs.service_type IS 'type of hash operations';
COMMENT ON COLUMN hash_jobs.params IS 'serialized specific params for hash operation';
COMMENT ON COLUMN hash_jobs.expired IS 'hash expired datetime';
COMMENT ON COLUMN hash_jobs.result IS '(JSON) job result data';
COMMENT ON COLUMN hash_jobs.status IS 'job status, can be pending/in progress/expired/closed/failed';


CREATE TABLE history (
    id serial,
    user_id integer,
    object_id integer,
    title character varying(255),
    area_id integer,
    url character varying(255) NOT NULL,
    created timestamp without time zone
);


COMMENT ON COLUMN history.title IS 'title, can be NULL';
COMMENT ON COLUMN history.area_id IS 'NULL in backend history';
COMMENT ON COLUMN history.url IS '???';

CREATE TABLE images (
    id integer NOT NULL,
    width integer,
    height integer
);

COMMENT ON COLUMN images.width IS 'image width, can be NULL';
COMMENT ON COLUMN images.height IS 'image height, can be NULL';

CREATE TABLE lang_texts (
    id serial,
    object_id integer NOT NULL,
    lang character(3) NOT NULL,
    name character varying(255),
    text text
);


COMMENT ON COLUMN lang_texts.lang IS 'language of translation, for example ita, eng, por';
COMMENT ON COLUMN lang_texts.name IS 'field/attribute name';
COMMENT ON COLUMN lang_texts.text IS 'translation';

CREATE TABLE links (
    id integer NOT NULL,
    url character varying(255),
    target character varying(10),
    http_code text,
    http_response_date timestamp without time zone,
    source_type character varying(64)
);

COMMENT ON COLUMN links.url IS '???';
COMMENT ON COLUMN links.target IS '(_self, _blank, parent, top, popup)';
COMMENT ON COLUMN links.http_code IS '???';
COMMENT ON COLUMN links.http_response_date IS '???';
COMMENT ON COLUMN links.source_type IS 'can be rss, wikipedia, archive.org, localresource....';

CREATE TABLE mail_group_cards (
    id serial,
    mail_group_id integer NOT NULL,
    card_id integer NOT NULL,
    status character varying(10) DEFAULT 'pending'::character varying NOT NULL,
    created timestamp without time zone
);


COMMENT ON COLUMN mail_group_cards.status IS 'describe subscription status (pending, confirmed)';
COMMENT ON COLUMN mail_group_cards.created IS '???';


CREATE TABLE mail_group_messages (
    mail_group_id integer NOT NULL,
    mail_message_id integer NOT NULL
);



CREATE TABLE mail_groups (
    id serial,
    area_id integer NOT NULL,
    group_name character varying(255) NOT NULL,
    visible boolean DEFAULT true NOT NULL,
    security character varying(10) DEFAULT 'all'::character varying NOT NULL,
    confirmation_in_message text,
    confirmation_out_message text
);


COMMENT ON COLUMN mail_groups.group_name IS '???';
COMMENT ON COLUMN mail_groups.visible IS '???';
COMMENT ON COLUMN mail_groups.security IS 'secure level (all, none)';
COMMENT ON COLUMN mail_groups.confirmation_in_message IS '???';
COMMENT ON COLUMN mail_groups.confirmation_out_message IS '???';

CREATE TABLE mail_jobs (
    id serial,
    mail_message_id integer,
    card_id integer,
    status character varying(10) DEFAULT 'unsent'::character varying NOT NULL,
    sending_date timestamp without time zone,
    created timestamp without time zone,
    modified timestamp without time zone,
    priority integer,
    mail_body text,
    recipient text,
    mail_params text,
    smtp_err text,
    process_info integer
);


COMMENT ON COLUMN mail_jobs.status IS 'job status (unsent, pending, sent, failed)';
COMMENT ON COLUMN mail_jobs.sending_date IS '???';
COMMENT ON COLUMN mail_jobs.created IS '???';
COMMENT ON COLUMN mail_jobs.modified IS '???';
COMMENT ON COLUMN mail_jobs.priority IS '???';
COMMENT ON COLUMN mail_jobs.mail_body IS '???';
COMMENT ON COLUMN mail_jobs.recipient IS 'used if card_is and mail_message_id are null, one or more comma separeted addresses';
COMMENT ON COLUMN mail_jobs.mail_params IS 'serialized array with: reply-to, sender, subject, signature...';
COMMENT ON COLUMN mail_jobs.smtp_err IS 'SMTP error message on sending failure';
COMMENT ON COLUMN mail_jobs.process_info IS 'pid of process delegates to send this mail job';

CREATE TABLE mail_logs (
    id serial,
    msg text NOT NULL,
    log_level character varying(10) DEFAULT 'info'::character varying NOT NULL,
    created timestamp without time zone NOT NULL,
    recipient character varying(255),
    subject character varying(255),
    mail_params text
);


COMMENT ON COLUMN mail_logs.msg IS '???';
COMMENT ON COLUMN mail_logs.log_level IS '(info, warn, err)';
COMMENT ON COLUMN mail_logs.created IS '???';
COMMENT ON COLUMN mail_logs.recipient IS '???';
COMMENT ON COLUMN mail_logs.subject IS '???';
COMMENT ON COLUMN mail_logs.mail_params IS 'on failure, serialized array with: reply-to, sender, subject, signature...';


CREATE TABLE mail_messages (
    id integer NOT NULL,
    mail_status character varying(10) DEFAULT 'unsent'::character varying NOT NULL,
    start_sending timestamp without time zone,
    end_sending timestamp without time zone,
	sender_name character varying(255) NOT NULL,
    sender character varying(255) NOT NULL,
    reply_to character varying(255) NOT NULL,
    bounce_to character varying(255) NOT NULL,
    priority integer,
    signature character varying(255) NOT NULL,
    privacy_disclaimer text,
    stylesheet character varying(255)
);



COMMENT ON COLUMN mail_messages.mail_status IS '???';



COMMENT ON COLUMN mail_messages.start_sending IS '???';



COMMENT ON COLUMN mail_messages.end_sending IS '???';



COMMENT ON COLUMN mail_messages.sender IS '???';



COMMENT ON COLUMN mail_messages.reply_to IS '???';



COMMENT ON COLUMN mail_messages.bounce_to IS '???';



COMMENT ON COLUMN mail_messages.priority IS '???';



COMMENT ON COLUMN mail_messages.signature IS '???';



COMMENT ON COLUMN mail_messages.privacy_disclaimer IS '???';



COMMENT ON COLUMN mail_messages.stylesheet IS '???';




CREATE TABLE modules (
    id serial,
    name character varying(32) NOT NULL,
    label character varying(32),
    url character varying(255) NOT NULL,
    status character varying(10) DEFAULT 'on'::character varying NOT NULL,
    priority integer,
    module_type character varying(10) DEFAULT 'core'::character varying NOT NULL
);



COMMENT ON COLUMN modules.name IS '???';



COMMENT ON COLUMN modules.label IS '???';



COMMENT ON COLUMN modules.url IS '???';



COMMENT ON COLUMN modules.status IS '(on, off)';



COMMENT ON COLUMN modules.priority IS '???';



COMMENT ON COLUMN modules.module_type IS '(core, plugin)';



CREATE TABLE object_categories (
    object_id integer NOT NULL,
    category_id integer NOT NULL
);



CREATE TABLE object_editors (
    id serial,
    object_id integer NOT NULL,
    user_id integer NOT NULL,
    last_access timestamp without time zone DEFAULT '1971-01-01 00:00:00'::timestamp without time zone NOT NULL
);



CREATE TABLE object_properties (
    id serial,
    property_id integer NOT NULL,
    object_id integer NOT NULL,
    property_value text NOT NULL
);



CREATE TABLE object_relations (
    object_id integer NOT NULL,
    id integer NOT NULL,
    switch character varying(63) DEFAULT 'attach'::character varying NOT NULL,
    priority integer,
    params text
);



COMMENT ON COLUMN object_relations.switch IS '???';
COMMENT ON COLUMN object_relations.priority IS '???';
COMMENT ON COLUMN object_relations.params IS 'relation properties values';


CREATE TABLE object_types (
    id integer NOT NULL,
    name character varying(255),
    module_name character varying(32)
);



CREATE TABLE object_users (
    id serial,
    object_id integer NOT NULL,
    user_id integer NOT NULL,
    switch character varying(63) DEFAULT 'card'::character varying NOT NULL,
    priority integer,
    params text
);



COMMENT ON COLUMN object_users.switch IS '???';
COMMENT ON COLUMN object_users.priority IS '???';
COMMENT ON COLUMN object_users.params IS '???';


CREATE TABLE objects (
    id serial,
    object_type_id integer NOT NULL,
    status character varying(10) DEFAULT 'draft'::character varying,
    created timestamp without time zone,
    modified timestamp without time zone,
    title character varying(255),
    nickname character varying(255),
    description text,
    valid boolean DEFAULT true,
    lang character(3),
    ip_created character varying(15),
    user_created integer NOT NULL default 1,
    user_modified integer NOT NULL default 1,
    rights character varying(255),
    license character varying(255),
    creator character varying(255),
    publisher character varying(255),
    note text,
    fixed boolean DEFAULT false,
    comments character varying(10) DEFAULT 'off'::character varying
);



COMMENT ON COLUMN objects.status IS '(on, off, draft)';
COMMENT ON COLUMN objects.title IS '???';
COMMENT ON COLUMN objects.nickname IS '???';
COMMENT ON COLUMN objects.description IS '???';
COMMENT ON COLUMN objects.valid IS '???';
COMMENT ON COLUMN objects.lang IS '???';
COMMENT ON COLUMN objects.rights IS '???';



COMMENT ON COLUMN objects.license IS '???';



COMMENT ON COLUMN objects.creator IS '???';



COMMENT ON COLUMN objects.publisher IS '???';



COMMENT ON COLUMN objects.note IS '???';



COMMENT ON COLUMN objects.fixed IS '???';



COMMENT ON COLUMN objects.comments IS 'define if an object is commentable (on, off, moderated)';



CREATE TABLE permission_modules (
    id serial,
    module_id integer NOT NULL,
    ugid integer NOT NULL,
    switch character varying(10),
    flag integer
);



COMMENT ON COLUMN permission_modules.switch IS 'permission type (user,group)';



COMMENT ON COLUMN permission_modules.flag IS '???';



CREATE TABLE permissions (
    id serial,
    object_id integer NOT NULL,
    ugid integer NOT NULL,
    switch character varying(10) NOT NULL,
    flag integer
);



COMMENT ON COLUMN permissions.switch IS 'permission type (user,group)';



COMMENT ON COLUMN permissions.flag IS '???';



CREATE TABLE products (
    id integer NOT NULL,
    abstract text,
    body text,
    serial_number character varying(128),
    weight double precision,
    width double precision,
    height double precision,
    product_depth double precision,
    volume double precision,
    length_unit character varying(40),
    weight_unit character varying(40),
    volume_unit character varying(40),
    color character varying(128),
    production_date timestamp without time zone,
    production_place character varying(255)
);



COMMENT ON COLUMN products.abstract IS '???';



COMMENT ON COLUMN products.body IS '???';



COMMENT ON COLUMN products.serial_number IS '???';



COMMENT ON COLUMN products.weight IS '???';



COMMENT ON COLUMN products.width IS '???';



COMMENT ON COLUMN products.height IS '???';



COMMENT ON COLUMN products.product_depth IS '???';



COMMENT ON COLUMN products.volume IS '???';



COMMENT ON COLUMN products.length_unit IS '???';



COMMENT ON COLUMN products.weight_unit IS '???';



COMMENT ON COLUMN products.volume_unit IS '???';



COMMENT ON COLUMN products.color IS '???';



COMMENT ON COLUMN products.production_date IS '???';



COMMENT ON COLUMN products.production_place IS '???';



CREATE TABLE properties (
    id serial,
    name character varying(255) NOT NULL,
    object_type_id integer,
    property_type character varying(10) NOT NULL,
    multiple_choice boolean DEFAULT false
);



COMMENT ON COLUMN properties.property_type IS '(number, date, text, options)';



COMMENT ON COLUMN properties.multiple_choice IS '???';



CREATE TABLE property_options (
    id serial,
    property_id integer NOT NULL,
    property_option text NOT NULL
);



COMMENT ON COLUMN property_options.property_option IS '???';



CREATE TABLE search_texts (
    id serial,
    object_id integer NOT NULL,
    lang character varying(3) NOT NULL,
    content text NOT NULL,
    relevance smallint DEFAULT (1)::smallint NOT NULL
);



COMMENT ON COLUMN search_texts.lang IS '???';



COMMENT ON COLUMN search_texts.content IS '???';



COMMENT ON COLUMN search_texts.relevance IS 'importance (1-10) range';



CREATE TABLE section_types (
    id serial,
    section_id integer NOT NULL,
    object_type_id integer NOT NULL,
    restricted smallint,
    predefined smallint
);



COMMENT ON COLUMN section_types.restricted IS '???';



COMMENT ON COLUMN section_types.predefined IS '???';



CREATE TABLE sections (
    id integer NOT NULL,
    syndicate character varying(10) DEFAULT 'on'::character varying,
    priority_order character varying(10) DEFAULT 'asc'::character varying,
    last_modified timestamp without time zone,
    map_priority double precision,
    map_changefreq character varying(128)
);



COMMENT ON COLUMN sections.syndicate IS '(on, off)';



COMMENT ON COLUMN sections.priority_order IS 'order of objects inserted in section (asc, desc)';



COMMENT ON COLUMN sections.map_priority IS '???';



COMMENT ON COLUMN sections.map_changefreq IS '???';



CREATE TABLE streams (
    id integer NOT NULL,
    uri character varying(255) NOT NULL,
    name character varying(255),
    mime_type character varying(60),
    file_size integer,
    hash_file character varying(255),
    original_name character varying(255) NULL
);



COMMENT ON COLUMN streams.uri IS '???';
COMMENT ON COLUMN streams.name IS '???';
COMMENT ON COLUMN streams.mime_type IS '???';
COMMENT ON COLUMN streams.file_size IS '???';
COMMENT ON COLUMN streams.hash_file IS '???';
COMMENT ON COLUMN streams.original_name IS 'original name for uploaded file';



CREATE TABLE trees (
    id integer NOT NULL,
    area_id integer,
    parent_id integer,
    object_path character varying(255) NOT NULL,
    parent_path character varying(255),
    priority integer,
    menu integer default 0 NOT NULL
);



COMMENT ON COLUMN trees.object_path IS '???';



COMMENT ON COLUMN trees.parent_path IS '???';



COMMENT ON COLUMN trees.priority IS '???';



COMMENT ON COLUMN trees.menu IS '???';



CREATE TABLE user_properties (
    id serial,
    property_id integer NOT NULL,
    user_id integer NOT NULL,
    property_value text NOT NULL
);



CREATE TABLE users (
    id serial,
    userid character varying(200) NOT NULL,
    realname character varying(255),
    passwd character varying(255),
    email character varying(255),
    valid boolean DEFAULT true NOT NULL,
    last_login timestamp without time zone,
    last_login_err timestamp without time zone,
    num_login_err integer DEFAULT 0 NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone,
    user_level boolean DEFAULT false NOT NULL,
    auth_type character varying(255),
    auth_params text,
    lang character(3),
    time_zone character(9),
    comments character varying(10),
    notes character varying(10),
    notify_changes boolean,
    reports boolean
);



COMMENT ON COLUMN users.user_level IS '???';



COMMENT ON COLUMN users.auth_type IS '???';



COMMENT ON COLUMN users.auth_params IS '???';



COMMENT ON COLUMN users.lang IS '???';



COMMENT ON COLUMN users.time_zone IS '???';



COMMENT ON COLUMN users.comments IS 'notify new comments option (never, mine, all)';



COMMENT ON COLUMN users.notes IS 'notify new notes option (never, mine, all)';



COMMENT ON COLUMN users.notify_changes IS '???';



COMMENT ON COLUMN users.reports IS '???';



CREATE TABLE versions (
    id serial,
    object_id integer NOT NULL,
    revision integer NOT NULL,
    user_id integer NOT NULL,
    created timestamp without time zone NOT NULL,
    diff text NOT NULL
);


COMMENT ON COLUMN versions.revision IS '???';



CREATE TABLE videos (
    id integer NOT NULL,
    provider character varying(255),
    video_uid character varying(255),
    thumbnail character varying(255)
);


COMMENT ON COLUMN videos.provider IS '???';



COMMENT ON COLUMN videos.video_uid IS '???';



COMMENT ON COLUMN videos.thumbnail IS '???';



ALTER TABLE ONLY aliases
    ADD CONSTRAINT aliases_pkey PRIMARY KEY (id);


ALTER TABLE ONLY annotations
    ADD CONSTRAINT annotations_pkey PRIMARY KEY (id);



ALTER TABLE ONLY applications
    ADD CONSTRAINT applications_pkey PRIMARY KEY (id);

ALTER TABLE ONLY areas
    ADD CONSTRAINT areas_pkey PRIMARY KEY (id);

ALTER TABLE ONLY banned_ips
    ADD CONSTRAINT banned_ips_pkey PRIMARY KEY (id);

ALTER TABLE ONLY cake_sessions
    ADD CONSTRAINT cake_sessions_pkey PRIMARY KEY (id);

ALTER TABLE ONLY cards
    ADD CONSTRAINT cards_pkey PRIMARY KEY (id);

ALTER TABLE ONLY categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);

ALTER TABLE ONLY contents
    ADD CONSTRAINT contents_pkey PRIMARY KEY (id);

ALTER TABLE ONLY date_items
    ADD CONSTRAINT date_items_pkey PRIMARY KEY (id);

ALTER TABLE ONLY users
    ADD CONSTRAINT email UNIQUE (email);

ALTER TABLE ONLY event_logs
    ADD CONSTRAINT event_logs_pkey PRIMARY KEY (id);

ALTER TABLE ONLY geo_tags
    ADD CONSTRAINT geo_tags_pkey PRIMARY KEY (id);



ALTER TABLE ONLY mail_groups
    ADD CONSTRAINT group_name UNIQUE (group_name);



ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_pkey PRIMARY KEY (id);



ALTER TABLE ONLY groups_users
    ADD CONSTRAINT groups_users_pkey PRIMARY KEY (group_id, user_id);



ALTER TABLE ONLY hash_jobs
    ADD CONSTRAINT hash UNIQUE (hash);



ALTER TABLE ONLY hash_jobs
    ADD CONSTRAINT hash_jobs_pkey PRIMARY KEY (id);



ALTER TABLE ONLY history
    ADD CONSTRAINT history_pkey PRIMARY KEY (id);



ALTER TABLE ONLY images
    ADD CONSTRAINT images_pkey PRIMARY KEY (id);



ALTER TABLE ONLY banned_ips
    ADD CONSTRAINT ip_unique UNIQUE (ip_address);



ALTER TABLE ONLY lang_texts
    ADD CONSTRAINT lang_texts_pkey PRIMARY KEY (id);



ALTER TABLE ONLY links
    ADD CONSTRAINT links_pkey PRIMARY KEY (id);



ALTER TABLE ONLY mail_group_cards
    ADD CONSTRAINT mail_group_card UNIQUE (card_id, mail_group_id);



ALTER TABLE ONLY mail_group_cards
    ADD CONSTRAINT mail_group_cards_pkey PRIMARY KEY (id);



ALTER TABLE ONLY mail_group_messages
    ADD CONSTRAINT mail_group_messages_pkey PRIMARY KEY (mail_group_id, mail_message_id);



ALTER TABLE ONLY mail_groups
    ADD CONSTRAINT mail_groups_pkey PRIMARY KEY (id);



ALTER TABLE ONLY mail_jobs
    ADD CONSTRAINT mail_jobs_pkey PRIMARY KEY (id);



ALTER TABLE ONLY mail_logs
    ADD CONSTRAINT mail_logs_pkey PRIMARY KEY (id);



ALTER TABLE ONLY mail_messages
    ADD CONSTRAINT mail_messages_pkey PRIMARY KEY (id);



ALTER TABLE ONLY modules
    ADD CONSTRAINT modules_name_key UNIQUE (name);



ALTER TABLE ONLY modules
    ADD CONSTRAINT modules_pkey PRIMARY KEY (id);



ALTER TABLE ONLY groups
    ADD CONSTRAINT name UNIQUE (name);



ALTER TABLE ONLY categories
    ADD CONSTRAINT name_type UNIQUE (name, object_type_id);



ALTER TABLE ONLY aliases
    ADD CONSTRAINT nickname_alias UNIQUE (nickname_alias);



ALTER TABLE ONLY object_categories
    ADD CONSTRAINT object_categories_pkey PRIMARY KEY (category_id, object_id);



ALTER TABLE ONLY object_editors
    ADD CONSTRAINT object_editors_pkey PRIMARY KEY (id);



ALTER TABLE ONLY versions
    ADD CONSTRAINT object_id_revision UNIQUE (object_id, revision);



ALTER TABLE ONLY object_users
    ADD CONSTRAINT object_id_user_id_switch UNIQUE (object_id, user_id, switch);



ALTER TABLE ONLY trees
    ADD CONSTRAINT object_path UNIQUE (object_path);



ALTER TABLE ONLY object_properties
    ADD CONSTRAINT object_properties_pkey PRIMARY KEY (id);



ALTER TABLE ONLY object_relations
    ADD CONSTRAINT object_relations_pkey PRIMARY KEY (id, object_id, switch);



ALTER TABLE ONLY object_types
    ADD CONSTRAINT object_types_name_key UNIQUE (name);



ALTER TABLE ONLY object_types
    ADD CONSTRAINT object_types_pkey PRIMARY KEY (id);



ALTER TABLE ONLY object_users
    ADD CONSTRAINT object_users_pkey PRIMARY KEY (id);



ALTER TABLE ONLY objects
    ADD CONSTRAINT objects_pkey PRIMARY KEY (id);



ALTER TABLE ONLY permission_modules
    ADD CONSTRAINT permission_modules_pkey PRIMARY KEY (id);



ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissions_obj_ug_sw_fl UNIQUE (object_id, ugid, switch, flag);


ALTER TABLE ONLY products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);



ALTER TABLE ONLY properties
    ADD CONSTRAINT properties_name_key UNIQUE (name, object_type_id);



ALTER TABLE ONLY properties
    ADD CONSTRAINT properties_pkey PRIMARY KEY (id);



ALTER TABLE ONLY property_options
    ADD CONSTRAINT property_options_pkey PRIMARY KEY (id);



ALTER TABLE ONLY search_texts
    ADD CONSTRAINT search_texts_pkey PRIMARY KEY (id);



ALTER TABLE ONLY section_types
    ADD CONSTRAINT section_types_pkey PRIMARY KEY (id);



ALTER TABLE ONLY sections
    ADD CONSTRAINT sections_pkey PRIMARY KEY (id);



ALTER TABLE ONLY streams
    ADD CONSTRAINT streams_pkey PRIMARY KEY (id);



ALTER TABLE ONLY user_properties
    ADD CONSTRAINT user_properties_pkey PRIMARY KEY (id);



ALTER TABLE ONLY users
    ADD CONSTRAINT userid UNIQUE (userid);



ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);



ALTER TABLE ONLY versions
    ADD CONSTRAINT versions_pkey PRIMARY KEY (id);



ALTER TABLE ONLY videos
    ADD CONSTRAINT videos_pkey PRIMARY KEY (id);



CREATE INDEX area_id ON history USING btree (area_id);



CREATE INDEX area_id1 ON mail_groups USING btree (area_id);



CREATE INDEX area_idx ON trees USING btree (area_id);



CREATE INDEX author_idx ON annotations USING btree (author);



CREATE INDEX card_id_index ON mail_group_cards USING btree (card_id);



CREATE INDEX card_id_index1 ON mail_jobs USING btree (card_id);



CREATE INDEX content ON search_texts USING gin(to_tsvector('english', content));



CREATE INDEX created ON mail_logs USING btree (created);



CREATE INDEX date_idx ON event_logs USING btree (created);



CREATE INDEX "groups_users_FKIndex1" ON groups_users USING btree (user_id);



CREATE INDEX "groups_users_FKIndex2" ON groups_users USING btree (group_id);



CREATE INDEX hash_file_index ON streams USING btree (hash_file);



CREATE INDEX id_idx ON trees USING btree (id);



CREATE INDEX idx_url ON links USING btree (url);



CREATE INDEX index_label ON categories USING btree (label);



CREATE INDEX index_name ON categories USING btree (name);



CREATE INDEX "lang_texts_FKIndex1" ON lang_texts USING btree (object_id);



CREATE INDEX mail_group_id_index ON mail_group_cards USING btree (mail_group_id);



CREATE INDEX mail_group_id_index1 ON mail_group_messages USING btree (mail_group_id);



CREATE INDEX mail_message_id_index ON mail_group_messages USING btree (mail_message_id);



CREATE INDEX mail_message_id_index1 ON mail_jobs USING btree (mail_message_id);



CREATE INDEX name_index ON properties USING btree (name);



CREATE INDEX object_id ON aliases USING btree (object_id);



CREATE INDEX object_id1 ON date_items USING btree (object_id);



CREATE INDEX object_id2 ON geo_tags USING btree (object_id);



CREATE INDEX object_id3 ON history USING btree (object_id);



CREATE INDEX object_id4 ON object_properties USING btree (object_id);



CREATE INDEX object_id5 ON search_texts USING btree (object_id, lang);



CREATE INDEX "object_id_FKIndex1" ON object_users USING btree (object_id);



CREATE INDEX object_id_index ON object_editors USING btree (object_id);



CREATE INDEX object_type_id ON categories USING btree (object_type_id);



CREATE INDEX "objects_FKIndex1" ON objects USING btree (object_type_id);



CREATE INDEX "objects_has_categories_FKIndex1" ON object_categories USING btree (object_id);



CREATE INDEX "objects_has_categories_FKIndex2" ON object_categories USING btree (category_id);



CREATE INDEX objects_idx ON annotations USING btree (object_id);



CREATE INDEX objects_index ON versions USING btree (object_id);



CREATE INDEX parent_idx ON trees USING btree (parent_id);



CREATE INDEX "permission_modules_FKIndex1" ON permission_modules USING btree (module_id);



CREATE INDEX "permission_modules_FKIndex3" ON permission_modules USING btree (ugid);



CREATE INDEX permissions_obj_inkdex ON permissions USING btree (object_id);



CREATE INDEX permissions_ugid_switch ON permissions USING btree (ugid, switch);



CREATE INDEX process_info_index ON mail_jobs USING btree (process_info);



CREATE INDEX property_id ON property_options USING btree (property_id);



CREATE INDEX property_id_index ON object_properties USING btree (property_id);



CREATE INDEX property_id_index1 ON user_properties USING btree (property_id);



CREATE INDEX recipient ON mail_logs USING btree (recipient);


CREATE INDEX recipient_index ON mail_jobs USING btree (recipient);


CREATE INDEX "related_objects_FKIndex1" ON object_relations USING btree (id);



CREATE INDEX "related_objects_FKIndex2" ON object_relations USING btree (object_id);



CREATE INDEX section_id ON section_types USING btree (section_id);



CREATE INDEX status_idx ON banned_ips USING btree (status);


CREATE INDEX status_idx1 ON mail_jobs USING btree (status);


CREATE INDEX type_index ON properties USING btree (object_type_id);



CREATE INDEX url ON history USING btree (url);



CREATE INDEX user_created ON objects USING btree (user_created);



CREATE INDEX user_id ON hash_jobs USING btree (user_id);



CREATE INDEX user_id1 ON history USING btree (user_id);



CREATE INDEX user_id2 ON user_properties USING btree (user_id);



CREATE INDEX "user_id_FKIndex2" ON object_users USING btree (user_id);



CREATE INDEX user_id_index ON object_editors USING btree (user_id);



CREATE INDEX user_index ON versions USING btree (user_id);



CREATE INDEX user_modified ON objects USING btree (user_modified);



CREATE INDEX userid_idx ON event_logs USING btree (userid);



ALTER TABLE ONLY aliases
    ADD CONSTRAINT aliases_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY annotations
    ADD CONSTRAINT annotations_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY applications
    ADD CONSTRAINT applications_ibfk_1 FOREIGN KEY (id) REFERENCES streams(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY areas
    ADD CONSTRAINT areas_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY cards
    ADD CONSTRAINT cards_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY contents
    ADD CONSTRAINT contents_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY date_items
    ADD CONSTRAINT date_items_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY geo_tags
    ADD CONSTRAINT geo_tags_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY groups_users
    ADD CONSTRAINT groups_users_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY groups_users
    ADD CONSTRAINT groups_users_ibfk_2 FOREIGN KEY (group_id) REFERENCES groups(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY history
    ADD CONSTRAINT history_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY history
    ADD CONSTRAINT history_ibfk_2 FOREIGN KEY (user_id) REFERENCES users(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY history
    ADD CONSTRAINT history_ibfk_3 FOREIGN KEY (area_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY images
    ADD CONSTRAINT images_ibfk_1 FOREIGN KEY (id) REFERENCES streams(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY lang_texts
    ADD CONSTRAINT lang_texts_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY links
    ADD CONSTRAINT links_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY mail_group_cards
    ADD CONSTRAINT mail_group_cards_ibfk_1 FOREIGN KEY (card_id) REFERENCES cards(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY mail_group_cards
    ADD CONSTRAINT mail_group_cards_ibfk_2 FOREIGN KEY (mail_group_id) REFERENCES mail_groups(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY mail_group_messages
    ADD CONSTRAINT mail_group_messages_ibfk_1 FOREIGN KEY (mail_message_id) REFERENCES mail_messages(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY mail_group_messages
    ADD CONSTRAINT mail_group_messages_ibfk_2 FOREIGN KEY (mail_group_id) REFERENCES mail_groups(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY mail_jobs
    ADD CONSTRAINT mail_jobs_ibfk_1 FOREIGN KEY (mail_message_id) REFERENCES mail_messages(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY mail_jobs
    ADD CONSTRAINT mail_jobs_ibfk_2 FOREIGN KEY (card_id) REFERENCES cards(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY mail_messages
    ADD CONSTRAINT mail_messages_ibfk_1 FOREIGN KEY (id) REFERENCES contents(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_categories
    ADD CONSTRAINT object_categories_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_categories
    ADD CONSTRAINT object_categories_ibfk_2 FOREIGN KEY (category_id) REFERENCES categories(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_editors
    ADD CONSTRAINT object_editors_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_editors
    ADD CONSTRAINT object_editors_ibfk_2 FOREIGN KEY (user_id) REFERENCES users(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_properties
    ADD CONSTRAINT object_properties_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_properties
    ADD CONSTRAINT object_properties_ibfk_2 FOREIGN KEY (property_id) REFERENCES properties(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_relations
    ADD CONSTRAINT object_relations_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_relations
    ADD CONSTRAINT object_relations_ibfk_2 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_users
    ADD CONSTRAINT object_users_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY object_users
    ADD CONSTRAINT object_users_ibfk_2 FOREIGN KEY (user_id) REFERENCES users(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY objects
    ADD CONSTRAINT objects_ibfk_1 FOREIGN KEY (user_created) REFERENCES users(id) MATCH FULL;



ALTER TABLE ONLY objects
    ADD CONSTRAINT objects_ibfk_2 FOREIGN KEY (user_modified) REFERENCES users(id) MATCH FULL;



ALTER TABLE ONLY permission_modules
    ADD CONSTRAINT permission_modules_ibfk_1 FOREIGN KEY (module_id) REFERENCES modules(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY permissions
    ADD CONSTRAINT permissions_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY products
    ADD CONSTRAINT products_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY properties
    ADD CONSTRAINT properties_ibfk_1 FOREIGN KEY (object_type_id) REFERENCES object_types(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY property_options
    ADD CONSTRAINT property_options_ibfk_1 FOREIGN KEY (property_id) REFERENCES properties(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY section_types
    ADD CONSTRAINT section_types_ibfk_1 FOREIGN KEY (section_id) REFERENCES sections(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY sections
    ADD CONSTRAINT sections_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY streams
    ADD CONSTRAINT streams_ibfk_1 FOREIGN KEY (id) REFERENCES contents(id) MATCH FULL ON DELETE CASCADE;



ALTER TABLE ONLY trees
    ADD CONSTRAINT trees_ibfk_1 FOREIGN KEY (id) REFERENCES objects(id) MATCH FULL;



ALTER TABLE ONLY trees
    ADD CONSTRAINT trees_ibfk_2 FOREIGN KEY (parent_id) REFERENCES objects(id) MATCH FULL;



ALTER TABLE ONLY user_properties
    ADD CONSTRAINT user_properties_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) MATCH FULL ON DELETE CASCADE;


ALTER TABLE ONLY user_properties
    ADD CONSTRAINT user_properties_ibfk_2 FOREIGN KEY (property_id) REFERENCES properties(id) MATCH FULL ON DELETE CASCADE;


ALTER TABLE ONLY versions
    ADD CONSTRAINT versions_ibfk_1 FOREIGN KEY (object_id) REFERENCES objects(id) MATCH FULL ON DELETE CASCADE;


ALTER TABLE ONLY versions
    ADD CONSTRAINT versions_ibfk_2 FOREIGN KEY (user_id) REFERENCES users(id) MATCH FULL;


ALTER TABLE ONLY videos
    ADD CONSTRAINT videos_ibfk_1 FOREIGN KEY (id) REFERENCES streams(id) MATCH FULL ON DELETE CASCADE;
