# ChangeLog

## Version 4.0.0.beta - Cactus

### API changes (4.0.0.beta)

* [#1348](https://github.com/bedita/bedita/issues/1348) `/model` base path for modeling endpoints
* [#1352](https://github.com/bedita/bedita/issues/1352) Refactor `model/` and `admin/` endpoints to use routing prefixes
* [#1393](https://github.com/bedita/bedita/issues/1393) JSON API `sparse fieldsets` with `fields` query parameter
* [#1390](https://github.com/bedita/bedita/pull/1390) Use `application/schema+json` content type
* [#1384](https://github.com/bedita/bedita/issues/1384) `/model/schema` endpoint - JSON SCHEMA
* [#1396](https://github.com/bedita/bedita/pull/1396) Enable `/admin/config` creation.
* [#1382](https://github.com/bedita/bedita/pull/1382) Accept `false` and `true` as boolen values in filters
* [#1392](https://github.com/bedita/bedita/pull/1392) Make `/relations` and `/properties` *searchable*
* [#1381](https://github.com/bedita/bedita/pull/1381) Search `/roles`, `/object_types`, `/applications`...
* [#1399](https://github.com/bedita/bedita/pull/1399) `object_type` endpoint info in `/home`

### Core changes (4.0.0.beta)

* [#1354](https://github.com/bedita/bedita/pull/1354) Makefile: release, publish and repo split
* [#1355](https://github.com/bedita/bedita/pull/1355) Multi project urls on same instance using BEDITA_BASE_URL
* [#1351](https://github.com/bedita/bedita/pull/1351) CTI: pass _inherited option in beforeSave chain
* [#908](https://github.com/bedita/bedita/issues/908) Inheritance in object types design
* [#1315](https://github.com/bedita/bedita/issues/1315) Basic analytics middleware
* [#1361](https://github.com/bedita/bedita/pull/1361) Add migrations for `videos`, `audio` and `files`
* [#1358](https://github.com/bedita/bedita/pull/1358) Object types naming rules
* [#1356](https://github.com/bedita/bedita/pull/1356) Object types inheritance
* [#1357](https://github.com/bedita/bedita/pull/1357) Pass pagination options to JSON API paginator instance
* [#1397](https://github.com/bedita/bedita/pull/1397) Schema for static properties
* [#1379](https://github.com/bedita/bedita/issues/1379) Default property types
* [#1371](https://github.com/bedita/bedita/pull/1371) Refactor BEdita shell + solve problematic behaviour with SQLite
* [#1366](https://github.com/bedita/bedita/issues/1366) Object types operations rules
* [#1345](https://github.com/bedita/bedita/issues/1345) Inheritance in custom properties
* [#1374](https://github.com/bedita/bedita/pull/1374) Object types removal controls
* [#1178](https://github.com/bedita/bedita/issues/1178) Shell test BeditaShellTest: nasty behaviour with sqlite, in following shell test
* [#1369](https://github.com/bedita/bedita/issues/1369) Set object type parent on creation
* [#1375](https://github.com/bedita/bedita/pull/1375) Fixture and tests review of `media`
* [#1367](https://github.com/bedita/bedita/pull/1367) Migration on core media types inheritance
* [#1365](https://github.com/bedita/bedita/pull/1365) Set default `application` and remove/disable controls
* [#1353](https://github.com/bedita/bedita/issues/1353) Properties list of an object type
* [#1401](https://github.com/bedita/bedita/pull/1401) Empty date inputs marshalled as `null`
* [#1391](https://github.com/bedita/bedita/issues/1391) Refactor shell test cases to use `ConsoleIntegrationTestCase`

### Integration changes (4.0.0.beta)

* [#1364](https://github.com/bedita/bedita/pull/1364) Composer: code sniffer proper setup and launch
* [#1368](https://github.com/bedita/bedita/pull/1368) Simple bash deploy script
* [#1346](https://github.com/bedita/bedita/pull/1346) Update app to cakephp 3.5
* [#1360](https://github.com/bedita/bedita/pull/1360) Scrutinizer Auto-Fixes
* [#1395](https://github.com/bedita/bedita/pull/1395) Postman updates
* [#1378](https://github.com/bedita/bedita/pull/1378) Insert `replace` in composer

## Version 4.0.0.alpha2 - Cactus

### API changes (4.0.0.alpha2)

* [#1318](https://github.com/bedita/bedita/pull/1318)  Link to available objects to add related objects
* [#1313](https://github.com/bedita/bedita/issues/1313) Model API `/relations` endpoint
* [#1330](https://github.com/bedita/bedita/pull/1330) GET & PATCH `/auth/user` - modify logged user own profile data
* [#1335](https://github.com/bedita/bedita/pull/1335) `Fields` query string
* [#1293](https://github.com/bedita/bedita/pull/1293) Admin API `/admin` endpoint
* [#1324](https://github.com/bedita/bedita/pull/1324)  /admin endpoint only accessible as `administrator`
* [#1310](https://github.com/bedita/bedita/pull/1310) Add common filters to `/trash` endoint

### Core changes (4.0.0.alpha2)

* [#1339](https://github.com/bedita/bedita/pull/1339) Relation parameters JSON SCHEMA validation
* [#1334](https://github.com/bedita/bedita/pull/1334) Introduce `custom properties` JSON field
* [#1343](https://github.com/bedita/bedita/pull/1343) Write operations on `custom properties`
* [#1261](https://github.com/bedita/bedita/pull/1261) Move HTML rendering to middleware layer
* [#1329](https://github.com/bedita/bedita/pull/1329) Admin role and user not removable (403 Forbidden)
* [#1320](https://github.com/bedita/bedita/pull/1320) Fix unable to save date ranges in some CTI cases
* [#1333](https://github.com/bedita/bedita/pull/1333) Hidden object type attributes
* [#1321](https://github.com/bedita/bedita/pull/1321) Fix error saving special chars in text fields
* [#1301](https://github.com/bedita/bedita/pull/1301) Include relations in object types cache
* [#1319](https://github.com/bedita/bedita/pull/1319) Dump empty array as sequence in spec shell
* [#1305](https://github.com/bedita/bedita/pull/1305) Allow base64-encoded uploads

### Integration changes (4.0.0.alpha2)

* [#1309](https://github.com/bedita/bedita/pull/1309) Official Docker image 🐳
* [#1338](https://github.com/bedita/bedita/pull/1338) Add Docker Compose template
* [#1342](https://github.com/bedita/bedita/pull/1342) Docker settings: CORS, API KEY and admin user
* [#1302](https://github.com/bedita/bedita/pull/1302) Update CakePHP to version 3.5
* [#1311](https://github.com/bedita/bedita/pull/1311) Update migrations plugin
* [#1337](https://github.com/bedita/bedita/pull/1337) Restore temporarily disabled build
* [#1290](https://github.com/bedita/bedita/pull/1290) Update CI builds
* [#1331](https://github.com/bedita/bedita/pull/1331) Force debug `true` in test bootstrap
* [#1323](https://github.com/bedita/bedita/pull/1323) Travis coverage fix and HHVM removal

## Version 4.0.0.alpha - Cactus

First public release - changelog will be updated from next release onwards
