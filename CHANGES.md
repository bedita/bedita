# ChangeLog

## Version 4.0.0.alpha2 - Cactus

### API changes

* [#1318](https://github.com/bedita/bedita/pull/1318)  Link to available objects to add related objects
* [#1313](https://github.com/bedita/bedita/issues/1313) Model API `/relations` endpoint
* [#1330](https://github.com/bedita/bedita/pull/1330) GET & PATCH `/auth/user` - modify logged user own profile data
* [#1335](https://github.com/bedita/bedita/pull/1335) `Fields` query string
* [#1293](https://github.com/bedita/bedita/pull/1293) Admin API `/admin` endpoint
* [#1324](https://github.com/bedita/bedita/pull/1324)  /admin endpoint only accessible as `administrator`
* [#1310](https://github.com/bedita/bedita/pull/1310) Add common filters to `/trash` endoint


### Core changes

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


### Integration changes

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
