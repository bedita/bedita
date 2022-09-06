# ChangeLog

## Version 5.0.2 - Salix

### Integration changes (5.0.2)

* [#1933](https://github.com/bedita/bedita/pull/1933) Use PHP 8.1 in docker image

## Version 5.0.1 - Salix

### Integration changes (5.0.1)

* [#1932](https://github.com/bedita/bedita/pull/1932) Fix some docker issues on 5-salix

## Version 5.0.0 - Salix

### API changes (5.0.0)

* [#1929](https://github.com/bedita/bedita/pull/1929) Avoid to execute the entire middleware queue for OPTIONS requests
* [#1925](https://github.com/bedita/bedita/pull/1925) `/config` endpoint refactor
* [#1924](https://github.com/bedita/bedita/pull/1924) Introduce Authentication and Authorization plugins
* [#1917](https://github.com/bedita/bedita/pull/1917) Fix POST call on relation types

### Core changes (5.0.0)

* [#1927](https://github.com/bedita/bedita/pull/1927) Increase `links.url` maximum length (v5)
* [#1921](https://github.com/bedita/bedita/pull/1921) command to refresh streams metadata in
* [#1919](https://github.com/bedita/bedita/pull/1919) Allow `null` parent name in object types
* [#1923](https://github.com/bedita/bedita/pull/1923) Introducing StatusBehavior

### Integration changes (5.0.0)

* [#1928](https://github.com/bedita/bedita/pull/1928) Removed minor integration deprecations
* [#1920](https://github.com/bedita/bedita/pull/1920) Introduce API and Core Plugin classes
* [#1918](https://github.com/bedita/bedita/pull/1918) Upgrade to CakePHP 4.4
* [#1915](https://github.com/bedita/bedita/pull/1915) Update exported project model format

## Version 5.0.0-beta - Salix

### API changes (5.0.0-beta)

* [#1910](https://github.com/bedita/bedita/pull/1910) Refactor: use standard Cake4 configuration layout, new BaseApplication class + other minor changes
* [#1909](https://github.com/bedita/bedita/pull/1909) API endpoint to admin endpoint permissions

### Core changes (5.0.0-beta)

* [#1904](https://github.com/bedita/bedita/pull/1904) Use new MiddlewareInterface
* [#1906](https://github.com/bedita/bedita/pull/1906) Avoid duplicate results with `?filter[ancestors]`

### Integration changes (5.0.0-beta)

* [#1914](https://github.com/bedita/bedita/pull/1914) Migrate to PHPUnit 9.5
* [#1913](https://github.com/bedita/bedita/pull/1913) Update composer dependencies

## Version 5.0.0.alpha - Salix

First public release - changelog will be updated from next release onwards

## Version 4.7.0 - Cactus

### API changes (4.7.0)

* [#1877](https://github.com/bedita/bedita/pull/1877) Add return type to initialize() methods
* [#1865](https://github.com/bedita/bedita/pull/1865) New JSON base controller
* [#1862](https://github.com/bedita/bedita/pull/1862) Use ActionTrait in LoginController
* [#1861](https://github.com/bedita/bedita/pull/1861) Skip auth token checks on OPTIONS request
* [#1859](https://github.com/bedita/bedita/pull/1859) Fix token decode error
* [#1609](https://github.com/bedita/bedita/pull/1609) Require protocol when validating URLs

### Core changes (4.7.0)

* [#1893](https://github.com/bedita/bedita/pull/1893) Ensure left and right fields are update when removing a tree entity
* [#1887](https://github.com/bedita/bedita/pull/1887) Add `user_preferences` field to users table
* [#1886](https://github.com/bedita/bedita/pull/1886) Introduce `InvalidDataException`
* [#1880](https://github.com/bedita/bedita/pull/1880) Fix thumb file extension and check allowed extensions
* [#1878](https://github.com/bedita/bedita/pull/1878) Split tags into their own `tags` table
* [#1882](https://github.com/bedita/bedita/pull/1882) Fix migration `CreateTagsTable`
* [#1874](https://github.com/bedita/bedita/pull/1874) Dispatch `ObjectType.getSchema` event when building schema for an object type
* [#1888](https://github.com/bedita/bedita/pull/1888) Tree integrity check
* [#1850](https://github.com/bedita/bedita/pull/1850) Create writable `bedita_core` folder for cache
* [#1873](https://github.com/bedita/bedita/pull/1873) Introduce `LockedResourceException`
* [#1864](https://github.com/bedita/bedita/pull/1864) Related objects utility methods
* [#1863](https://github.com/bedita/bedita/pull/1863) Mark join entity as new when it is a new association for source entity

### Integration changes (4.7.0)

* [#1890](https://github.com/bedita/bedita/pull/1890) Treat phpdbg SAPI as cli
* [#1889](https://github.com/bedita/bedita/pull/1889) Update phpstan/phpstan to version 1.5
* [#1872](https://github.com/bedita/bedita/pull/1872) Cake 4 Core compatibility changes
* [#1871](https://github.com/bedita/bedita/pull/1871) Cake 4 API compatibility changes
* [#1853](https://github.com/bedita/bedita/pull/1853) Update `bedita/dev-tools` to 1.5.*

## Version 4.6.1 - Cactus

### API changes (4.6.1)

* [#1849](https://github.com/bedita/bedita/pull/1849) Fix renew token expiry

## Version 4.6.0 - Cactus

### API changes (4.6.0)

* [#1844](https://github.com/bedita/bedita/pull/1844) Improve external OAuth2 providers check
* [#1836](https://github.com/bedita/bedita/pull/1836) New `/applications` endpoint
* [#1830](https://github.com/bedita/bedita/pull/1830) Add `private` URLs to Streams
* [#1829](https://github.com/bedita/bedita/pull/1829) Implement proper OAuth2 flow on `/auth`
* [#1827](https://github.com/bedita/bedita/pull/1827) get /model/schema/:type, all relations

### Core changes (4.6.0)

* [#1841](https://github.com/bedita/bedita/pull/1841) Add `pseudonym` to Profiles
* [#1840](https://github.com/bedita/bedita/pull/1840) `password_modified` read-only property
* [#1839](https://github.com/bedita/bedita/pull/1839) `Trees.menu` is default off
* [#1834](https://github.com/bedita/bedita/pull/1834) Lighter async mail payload
* [#1826](https://github.com/bedita/bedita/pull/1826) Fix `filter[]` error on related objects
* [#1825](https://github.com/bedita/bedita/pull/1825) Fix `UpdateRelatedObjectsActions::prepareData()` on multiple relatedEntities
* [#1823](https://github.com/bedita/bedita/pull/1823) Fix unique uname generation use cases
* [#1822](https://github.com/bedita/bedita/pull/1822) Add file metadata to Streams
* [#1821](https://github.com/bedita/bedita/pull/1821) New `fix_history` command

### Integration changes (4.6.0)

* [#1837](https://github.com/bedita/bedita/pull/1837) Docker: update PHP and composer versions
* [#1831](https://github.com/bedita/bedita/pull/1831) Use MySQL 8 as default for dump file & CI
* [#1845](https://github.com/bedita/bedita/pull/1845) Bulk coding style fix

## Version 4.5.0 - Cactus

### API changes (4.5.0)

* [#1817](https://github.com/bedita/bedita/pull/1817) New `/translations` endpoint
* [#1808](https://github.com/bedita/bedita/pull/1808) Filter for custom properties
* [#1802](https://github.com/bedita/bedita/pull/1802) Lock/unlock objects and block/unblock users actions
* [#1798](https://github.com/bedita/bedita/pull/1798) Project model endpoint & command
* [#1753](https://github.com/bedita/bedita/pull/1753) New `history_editor` finder & filter

### Core changes (4.5.0)

* [#1818](https://github.com/bedita/bedita/pull/1818) More resources tables searchable
* [#1809](https://github.com/bedita/bedita/pull/1809) Add `date-time` to nullable formats
* [#1807](https://github.com/bedita/bedita/pull/1807) Avoid `uname` change when absent
* [#1804](https://github.com/bedita/bedita/pull/1804) Format and validate JSON Schema properties
* [#1800](https://github.com/bedita/bedita/pull/1800) Locked constraint logic
* [#1796](https://github.com/bedita/bedita/pull/1796) Filter `publish_start` and `publish_end` dates

### Integration changes (4.5.0)

* [#1815](https://github.com/bedita/bedita/pull/1815) Fix PHPstan issues, PHPstan added to `require-dev`
* [#1812](https://github.com/bedita/bedita/pull/1812) Upgrade to CakePHP 3.10
* [#1805](https://github.com/bedita/bedita/pull/1805) Properties update utility
* [#1803](https://github.com/bedita/bedita/pull/1803) Use phpcs XML conf
* [#1801](https://github.com/bedita/bedita/pull/1801) Enable `locked` save via `ObjectsHandler`
* [#1799](https://github.com/bedita/bedita/pull/1799) Fix geometric tests
* [#1797](https://github.com/bedita/bedita/pull/1797) Release Makefile cleanup

## Version 4.4.0 - Cactus

### API changes (4.4.0)

* [#1787](https://github.com/bedita/bedita/pull/1787) Add `/model/tags` route
* [#1774](https://github.com/bedita/bedita/pull/1774) Allow `uname` in relationship management
* [#1773](https://github.com/bedita/bedita/pull/1773) Fix `?include` check
* [#1772](https://github.com/bedita/bedita/pull/1772) Fix `/home` authorization check on object types endpoints
* [#1767](https://github.com/bedita/bedita/pull/1767) Fix `status` handling on status level check
* [#1759](https://github.com/bedita/bedita/pull/1759) Introduce min/max sort on DateRanges
* [#1747](https://github.com/bedita/bedita/pull/1747) Dispatch JsonApi.beforeFormatData and JsonApi.afterFormatData events
* [#1746](https://github.com/bedita/bedita/pull/1746) Include relations count number via `?count` query string
* [#1710](https://github.com/bedita/bedita/pull/1710) Add associations and relations metadata to JSON Schema

### Core changes (4.4.0)

* [#1795](https://github.com/bedita/bedita/pull/1795) Ensure custom properties results formatter is prepended
* [#1790](https://github.com/bedita/bedita/pull/1790) Add endpoints permissions cache
* [#1751](https://github.com/bedita/bedita/pull/1751) Activate routes cache
* [#1788](https://github.com/bedita/bedita/pull/1788) Enale tags creation in  CategoriesBehavior
* [#1784](https://github.com/bedita/bedita/pull/1784) Reload ObjectType entity to load related type's relations
* [#1783](https://github.com/bedita/bedita/pull/1783) Add index on `created` and `modified` columns of `async_jobs`
* [#1782](https://github.com/bedita/bedita/pull/1782) ResourceBase class for Resources utilities
* [#1780](https://github.com/bedita/bedita/pull/1780) External Auth Signup: add `verified`, remove reference on `anonymize`
* [#1778](https://github.com/bedita/bedita/pull/1778) Expose available custom properties getting related object
* [#1777](https://github.com/bedita/bedita/pull/1777) Enable `categories` in migrations
* [#1769](https://github.com/bedita/bedita/pull/1769) Display only _"available"_ children and parents
* [#1768](https://github.com/bedita/bedita/pull/1768) Restore original inherited table alias after cascading to parent finder
* [#1766](https://github.com/bedita/bedita/pull/1766) Allow empty `label/inverse_label` in relations
* [#1765](https://github.com/bedita/bedita/pull/1765) Cache database config and applications
* [#1762](https://github.com/bedita/bedita/pull/1762) Dispatch events when adding, removing, or replacing associations
* [#1761](https://github.com/bedita/bedita/pull/1761) Introduce layered cache
* [#1757](https://github.com/bedita/bedita/pull/1757) Add virtual property `object_type_name` to Endpoint Entity
* [#1754](https://github.com/bedita/bedita/pull/1754) Avoid to use real properties as virtual properties
* [#1745](https://github.com/bedita/bedita/pull/1745) Fix search behavior, allow `_` and `-` in search

### Integration changes (4.4.0)

* [#1791](https://github.com/bedita/bedita/pull/1791) Update to composer 2
* [#1786](https://github.com/bedita/bedita/pull/1786) Drop PHP 7.1
* [#1771](https://github.com/bedita/bedita/pull/1771) Better isolation within test cases
* [#1760](https://github.com/bedita/bedita/pull/1760) Remove useless inline docblock, fix phpstan weirdness
* [#1758](https://github.com/bedita/bedita/pull/1758) Fix publish components action
* [#1756](https://github.com/bedita/bedita/pull/1756) Remove travis CI
* [#1755](https://github.com/bedita/bedita/pull/1755) Move to GitHub Actions
* [#1733](https://github.com/bedita/bedita/pull/1733) Upgrade to CakePHP 3.9

## Version 4.3.0 - Cactus

### API changes (4.3.0)

* [#1735](https://github.com/bedita/bedita/pull/1735) Add `external_auth` info in users meta
* [#1742](https://github.com/bedita/bedita/pull/1742) Fix signup when user has no roles but conf require roles

### Core changes (4.3.0)

* [#1731](https://github.com/bedita/bedita/pull/1731) Handle JSON-SCHEMA defaults in relation `params` setting

### Integration changes (4.3.0)

* [#1738](https://github.com/bedita/bedita/pull/1738) Update composer to use plugin-installer ^1.3
* [#1734](https://github.com/bedita/bedita/pull/1734) Allow to use specific composer version in CI

## Version 4.2.1 - Cactus

### API changes (4.2.1)

* [#1727](https://github.com/bedita/bedita/pull/1727) Save relation metadata in `PATCH /folders/{id}/parent`

### Core changes (4.2.1)

* [#1728](https://github.com/bedita/bedita/pull/1728) Add endpoints to handled resources in migrations
* [#1729](https://github.com/bedita/bedita/pull/1729) Add endpoint_permissions to resource utility
* [#1729](https://github.com/bedita/bedita/pull/1729) Perform non atomic changes in `Resources` - fix official docker image

## Version 4.2.0 - Cactus

### API changes (4.2.0)

* [#1551](https://github.com/bedita/bedita/pull/1551) Create media object with stream in one request
* [#1690](https://github.com/bedita/bedita/pull/1690) Add `canonical` meta property on `children` relation
* [#1524](https://github.com/bedita/bedita/pull/1524) PATCH /folders/:id/relationships/children fails with meta.relation.position present
* [#1695](https://github.com/bedita/bedita/pull/1695) Categories type finder + filter
* [#1679](https://github.com/bedita/bedita/pull/1679) Avoid `included` repetitions
* [#1651](https://github.com/bedita/bedita/pull/1651) Fix pagination `maxLimit` configuration
* [#1691](https://github.com/bedita/bedita/pull/1691) Handle `canonical` meta #1690
* [#1671](https://github.com/bedita/bedita/pull/1671) Categories object type name
* [#1668](https://github.com/bedita/bedita/pull/1668) User roles filter/finder
* [#1663](https://github.com/bedita/bedita/pull/1663) Block anonymous apps as default
* [#1652](https://github.com/bedita/bedita/pull/1652) Populate `meta.media_url` always for media objects
* [#1702](https://github.com/bedita/bedita/pull/1702) DateRanges order via `sort` query string
* [#1697](https://github.com/bedita/bedita/pull/1697) New `from_date` and `to_date` filters on `DateRanges`

### Core changes (4.2.0)

* [#1602](https://github.com/bedita/bedita/pull/1602) CustomProperties behavior recursion problem
* [#1550](https://github.com/bedita/bedita/pull/1550) Entity `isDirty()` not working on custom properties
* [#1654](https://github.com/bedita/bedita/pull/1654) Default priority on relations: max + 1
* [#1687](https://github.com/bedita/bedita/pull/1687) `Publications` core object type
* [#1672](https://github.com/bedita/bedita/pull/1672) Fix entity virtual props recursion
* [#1669](https://github.com/bedita/bedita/pull/1669) Change `title` set rules on profiles/users
* [#1662](https://github.com/bedita/bedita/pull/1662) Allow non assoc array configuration in `Plugins`
* [#1692](https://github.com/bedita/bedita/pull/1692) Column streams.uri increase limit
* [#1684](https://github.com/bedita/bedita/pull/1684) `Links` core object type
* [#1681](https://github.com/bedita/bedita/pull/1681) Limit `uname` max length
* [#1670](https://github.com/bedita/bedita/pull/1670) Custom Signup action
* [#1667](https://github.com/bedita/bedita/pull/1667) Virtual methods to get/set parent folder by uname
* [#1716](https://github.com/bedita/bedita/pull/1716) Override static property schema definition
* [#1709](https://github.com/bedita/bedita/pull/1709) Increase `extra` to 16MB on MySQL
* [#1707](https://github.com/bedita/bedita/pull/1707) Add `trees.menu`
* [#1706](https://github.com/bedita/bedita/pull/1706) Enable multi application config
* [#1704](https://github.com/bedita/bedita/pull/1704) Set `DateRanges.params` column type as JSON
* [#1703](https://github.com/bedita/bedita/pull/1703) Sanitize `uname`
* [#1711](https://github.com/bedita/bedita/pull/1711) Ensure entity not marked as dirty promoting empty custom prop
* [#1699](https://github.com/bedita/bedita/pull/1699) fix: use `available` finder on `relatedTo` assoc
* [#1722](https://github.com/bedita/bedita/pull/1722) fix: rewind stream only if seekable

### Integration changes (4.2.0)

* [#1705](https://github.com/bedita/bedita/pull/1705) Add `config` and `auth_providers` to Resources utility
* [#1680](https://github.com/bedita/bedita/pull/1680) Refactor tests on `default://` and `thumbnails://` filesystems
* [#1666](https://github.com/bedita/bedita/pull/1666) Improve objects handler
* [#1660](https://github.com/bedita/bedita/pull/1660) New Resources utility
* [#1721](https://github.com/bedita/bedita/pull/1721) YAML column properties migrations
* [#1720](https://github.com/bedita/bedita/pull/1720) fix: yml migrations rollback (restore field)
* [#1718](https://github.com/bedita/bedita/pull/1718) fix: clear registry before table reload w options
* [#1717](https://github.com/bedita/bedita/pull/1717) Resources migrations via YAML
* [#1550](https://github.com/bedita/bedita/pull/1550) test: add test for isDirty custom prop
* [#1694](https://github.com/bedita/bedita/pull/1694) Feat add `Relations::update()`

## Version 4.1.0 - Cactus

### API changes (4.1.0)

* [#1638](https://github.com/bedita/bedita/issues/1638) Categories and Tags API
* [#1636](https://github.com/bedita/bedita/pull/1636) Object & resources `/history` endpoint
* [#1649](https://github.com/bedita/bedita/pull/1649) User opt-out, remove user data request #1578
* [#1632](https://github.com/bedita/bedita/pull/1632) Add external **provider thumbs** to `/media/thumbs` response
* [#1644](https://github.com/bedita/bedita/pull/1644) Fix `/` to `/home` redirect via explicit path

### Core changes (4.1.0)

* [#1624](https://github.com/bedita/bedita/issues/1624) Object and resource history data model
* [#1639](https://github.com/bedita/bedita/pull/1639) Categories and Tags data model
* [#1641](https://github.com/bedita/bedita/pull/1641) Refactor object table hierarchy and behaviors
* [#1640](https://github.com/bedita/bedita/pull/1640) Introduce **core** property types, `core_type` flag
* [#1634](https://github.com/bedita/bedita/pull/1634) Fix timezone save problem on datetime properties, `UTC` as default
* [#1645](https://github.com/bedita/bedita/pull/1645) Use `TableRegistry::getTableLocator()`, fix deprecations
* Introduce `TreeBehavior::nonAtomicRecover()`, handle recover actions inside external transactions
* Fix deprecated code

### Integration changes (4.1.0)

* [#1643](https://github.com/bedita/bedita/pull/1643) Add MySQL 8 and Maria DB 10 to Travis CI
* [#1646](https://github.com/bedita/bedita/pull/1646) PHP 7.4 Travis task added, PHP 7.1 removed
* [#1635](https://github.com/bedita/bedita/pull/1635) Update code sniffer rules to PSR-12 viacakephp/codesniffer 3.2.*
* [#1631](https://github.com/bedita/bedita/pull/1631) Avoid migrations transaction error on SQlite

## Version 4.0.0 - Cactus

### API changes (4.0.0)

* [#1614](https://github.com/bedita/bedita/pull/1614) Block auth operations on blocked/deleted user
* [#1604](https://github.com/bedita/bedita/pull/1604) Logged user cannot trash himself
* [#1594](https://github.com/bedita/bedita/pull/1594) Fix Uploading XML or JSON files
* [#1590](https://github.com/bedita/bedita/pull/1590) Add `meta.media_url` property in media objects
* [#1582](https://github.com/bedita/bedita/issues/1582) Fix Relation with empty params can't be saved
* [#1580](https://github.com/bedita/bedita/pull/1580) Signup new configuration options
* [#1628](https://github.com/bedita/bedita/pull/1628) Custom error handler to avoid HTML on `trigger_error`
* Add `locked` filter on objects

### Core changes (4.0.0)

* [#1625](https://github.com/bedita/bedita/pull/1625) Relations and Properties utilities
* [#1613](https://github.com/bedita/bedita/pull/1613) Allow `+02` and `+0200` as TZ format in input date
* [#1612](https://github.com/bedita/bedita/pull/1612) Fix `DefaultValues` config on core types
* [#1589](https://github.com/bedita/bedita/pull/1589) Fix `DefaultValues` behavior
* [#1607](https://github.com/bedita/bedita/pull/1607) Avoid bad side effects on DataCleanup (see #1601)
* [#1595](https://github.com/bedita/bedita/pull/1595) New `streams removeOrphans` shell
* [#1581](https://github.com/bedita/bedita/pull/1581) Fix created_by/modified_by foreign key check
* [#1579](https://github.com/bedita/bedita/pull/1579) Permanent user removal and anonymization #1556
* [#1592](https://github.com/bedita/bedita/pull/1592) Apply default values only on new objects (id available)
* [#1583](https://github.com/bedita/bedita/pull/1583) Allow missing or empty `params` JSON object relations
* Make user `email` changeable if null

### Integration changes (4.0.0)

* [#1616](https://github.com/bedita/bedita/pull/1616) Travis can't install mysql-5.7 on trusty
* [#1619](https://github.com/bedita/bedita/pull/1619) Remove coverage on PG task
* [#1605](https://github.com/bedita/bedita/pull/1605) Fix Docker permissions
* [#1564](https://github.com/bedita/bedita/pull/1564) Set perms for webroot files in entrypoint.sh
* Use `bedita/dev-tools` stable releases
* Upgrade to `CakePHP 3.8.x` and support `PHP 7.3`

## Version 4.0.0.RC2 - Cactus

### API changes (4.0.0.RC2)

* [#1520](https://github.com/bedita/bedita/pull/1520) Ignore empty `password` on /auth/user update
* [#1525](https://github.com/bedita/bedita/pull/1525) Update child position with `POST` action as well
* [#1530](https://github.com/bedita/bedita/pull/1530) Query string filter fields with `null` or `not null`
* [#1534](https://github.com/bedita/bedita/pull/1534) Introduce `lang` query string #1494
* [#1536](https://github.com/bedita/bedita/pull/1536) feat: translations in `included` list with `lang` query string
* [#1533](https://github.com/bedita/bedita/pull/1533) Existing user signup custom error code
* [#1537](https://github.com/bedita/bedita/pull/1537) One Time Password (OTP) auth flow #1535
* [#1545](https://github.com/bedita/bedita/pull/1545) Add `include` filter in `/auth/user`
* [#1541](https://github.com/bedita/bedita/pull/1541) `parent` filter using type name on `object_types`
* [#1547](https://github.com/bedita/bedita/pull/1547) Avoid errors on `status` check with relations and `lang` filter
* [#1563](https://github.com/bedita/bedita/pull/1563) Modifiy error message searching with 1 or 2 letters
* [#1566](https://github.com/bedita/bedita/pull/1566) #1531 Fix update children position on folders
* [#1568](https://github.com/bedita/bedita/issues/1568) Fix weird response on `PATCH /model/property_types`
* [#1572](https://github.com/bedita/bedita/pull/1572) Make `params` optional when updating relations

### Core changes (4.0.0.RC2)

* [#1518](https://github.com/bedita/bedita/pull/1518) Introduce JSON Schema `date` and `datetime` property type
* [#1521](https://github.com/bedita/bedita/pull/1521) Setup json format for date object
* [#1522](https://github.com/bedita/bedita/pull/1522) Fix date & datetime input with ISO and other standard formats
* [#1465](https://github.com/bedita/bedita/pull/1465) Fix users validation: empty password not allowed
* [#1507](https://github.com/bedita/bedita/issues/1507) JSON Schema `params` validation on relations
* [#1499](https://github.com/bedita/bedita/issues/1499) Replace JSON Schema validation libraries
* [#1527](https://github.com/bedita/bedita/pull/1527) Decode JSON Schemas as objects instead of associative
* [#1539](https://github.com/bedita/bedita/pull/1539) `profiles.email` is now a non unique index
* [#1540](https://github.com/bedita/bedita/pull/1540) #1422 collect application rules errors inherited in main entity
* [#1422](https://github.com/bedita/bedita/issues/1422) Fix error saving user with duplicated `email`
* [#1549](https://github.com/bedita/bedita/pull/1549) Make user tokens types configurable
* [#1544](https://github.com/bedita/bedita/pull/1544) Read current application first to avoid errors in `/auth` or elsewhere
* [#1558](https://github.com/bedita/bedita/pull/1558) Make `username` searchable
* [#1552](https://github.com/bedita/bedita/issues/1552) Default `status` for new objects via configuration
* [#1548](https://github.com/bedita/bedita/pull/1548) Avoid `status` filter on resources

### Integration changes (4.0.0.RC2)

* [#1519](https://github.com/bedita/bedita/pull/1519) Coverage and unit test improvements
* [#1543](https://github.com/bedita/bedita/pull/1543) feat: php 7.1 as minimum version

## Version 4.0.0.RC - Cactus

### API changes (4.0.0.RC)

* [#1506](https://github.com/bedita/bedita/pull/1506) #1495 permanent remove folder
* [#1484](https://github.com/bedita/bedita/issues/1484) Folder children ordering
* [#1509](https://github.com/bedita/bedita/pull/1509) Annotations endpoint
* [#1505](https://github.com/bedita/bedita/pull/1505) Translations endpoint #1493
* [#1503](https://github.com/bedita/bedita/pull/1503) Password change in `/auth/user` #1501
* [#1514](https://github.com/bedita/bedita/pull/1514) chore: enable `fields` filter in `GET /auth/user`
* [#1497](https://github.com/bedita/bedita/pull/1497) Only canonical plural form as object endpoint
* [#1446](https://github.com/bedita/bedita/pull/1446) Allow the use of uname instead of id for objects API
* [#1431](https://github.com/bedita/bedita/issues/1431) Folder API write operations
* [#1451](https://github.com/bedita/bedita/pull/1451) #1431 GET /folders/:id/parent is here
* [#1459](https://github.com/bedita/bedita/pull/1459) Fix `?include=children,parent` query string
* [#1488](https://github.com/bedita/bedita/pull/1488) Application configuration #1486
* [#1450](https://github.com/bedita/bedita/pull/1450) Avilable types for folders relationships #1449
* [#1496](https://github.com/bedita/bedita/pull/1496) Include related entities in related endpoint
* [#1463](https://github.com/bedita/bedita/pull/1463) `parent`, `ancestor` and `roots` folder filters
* [#1464](https://github.com/bedita/bedita/pull/1464) path in meta for folders #1461
* [#1458](https://github.com/bedita/bedita/pull/1458) Don't list deleted objects in `children` and `parents`
* [#1468](https://github.com/bedita/bedita/pull/1468) Included deleted objects #1466

### Core changes (4.0.0.RC)

* [#1448](https://github.com/bedita/bedita/pull/1448) Add default `title` for profiles and users
* [#1504](https://github.com/bedita/bedita/pull/1504) I18n configuration and lang check #1502
* [#1492](https://github.com/bedita/bedita/issues/1492) I18n [1/3] - data model `translations`
* [#1495](https://github.com/bedita/bedita/issues/1495) Permanent remove folder -> children folders
* [#1508](https://github.com/bedita/bedita/pull/1508) Update profiles title also if blank
* [#1498](https://github.com/bedita/bedita/pull/1498) Translations model
* [#1512](https://github.com/bedita/bedita/pull/1512) Remove useLocalParser() from bootstrap
* [#1513](https://github.com/bedita/bedita/pull/1513) #1484 Sort contents in folder
* [#1456](https://github.com/bedita/bedita/pull/1456) Routing rules change
* [#1487](https://github.com/bedita/bedita/pull/1487) Object status filter on applications #1485
* [#1482](https://github.com/bedita/bedita/pull/1482) #1453 Soft delete and restore of folders
* [#1481](https://github.com/bedita/bedita/pull/1481) Prevent ghost folders #1479
* [#1480](https://github.com/bedita/bedita/pull/1480) #1454 Move folders action
* [#1477](https://github.com/bedita/bedita/pull/1477) Fix multiple issues with Folders' relationships #1471
* [#1473](https://github.com/bedita/bedita/pull/1473) Prevent login from blocked, deleted and off users
* [#1472](https://github.com/bedita/bedita/pull/1472) Fix foreign key `cascade` on delete for `objects`
* [#1491](https://github.com/bedita/bedita/pull/1491) Schema on disabled types and hidden properties
* [#1483](https://github.com/bedita/bedita/pull/1483) Tree maintenance shell #1476
* [#1475](https://github.com/bedita/bedita/pull/1475) fix: 500 error getting path of orphan folder
* [#1445](https://github.com/bedita/bedita/pull/1445) #1431 feat: write operations on parents relationships for objects
* [#1490](https://github.com/bedita/bedita/pull/1490) Add local cache of JSON Schema Draft-06 meta schema
* [#1469](https://github.com/bedita/bedita/pull/1469) Relations inheritance #1349
* [#1470](https://github.com/bedita/bedita/pull/1470) Avoid `setDirty` false on CTI
* [minor] fix: avoid cryptic fatal error if thumbnails config is missing

### Integration changes (4.0.0.RC)

* [#1429](https://github.com/bedita/bedita/issues/1429) External authentication providers integration (OAuth2 & C)
* [#1447](https://github.com/bedita/bedita/pull/1447) OAuth2 client signup & auth #1429
* [#1478](https://github.com/bedita/bedita/pull/1478) Postman update with folders and external auth/signup
* [minor] chore: docker - create default thumbs dir

## Version 4.0.0.beta2 - Cactus

### API changes (4.0.0.beta2)

* [#1434](https://github.com/bedita/bedita/pull/1434) Roles in `/auth/user`
* [#1438](https://github.com/bedita/bedita/issues/1438) Signup with given roles
* [#1402](https://github.com/bedita/bedita/issues/1402) Folders API
* [#1430](https://github.com/bedita/bedita/issues/1430) GET `files` fails with 405
* [#1421](https://github.com/bedita/bedita/pull/1421) Enable filter on multiple associated items by id
* [#1416](https://github.com/bedita/bedita/pull/1416) Add multiple types flag to `/home`
* [#1406](https://github.com/bedita/bedita/issues/1406) Schema info in response in `meta.schema`
* [#1415](https://github.com/bedita/bedita/pull/1415) Add `id` property to json schema
* [#1414](https://github.com/bedita/bedita/pull/1414) ETag header on json schema
* [#1411](https://github.com/bedita/bedita/pull/1411) Create/modify not allowed on abstract endpoints
* [#1435](https://github.com/bedita/bedita/pull/1435) Login authorization

### Core changes (4.0.0.beta2)

* [#1437](https://github.com/bedita/bedita/pull/1437) Passwd policy
* [#1436](https://github.com/bedita/bedita/pull/1436) Thumbnails generation
* [#1408](https://github.com/bedita/bedita/pull/1408) Folders data model
* [#1412](https://github.com/bedita/bedita/issues/1412) Error saving `object_type` with unchanged `parent_name`
* [#1418](https://github.com/bedita/bedita/issues/1418) Error saving empty date - CTI problem
* [#1407](https://github.com/bedita/bedita/pull/1407) Fix warning when including to-one resources
* [#1425](https://github.com/bedita/bedita/pull/1425) Avoid errors on blank `email`
* [#1405](https://github.com/bedita/bedita/issues/1405) JSON Schema cache
* [#1404](https://github.com/bedita/bedita/issues/1404) Add revision to JSON Schema
* [#1420](https://github.com/bedita/bedita/pull/1420) Error saving unchanged parent name
* [#1417](https://github.com/bedita/bedita/pull/1417) Disabled types updates
* [#1433](https://github.com/bedita/bedita/pull/1433) Chore: LocalAdapter path + test

### Integration changes (4.0.0.beta2)

* [#1427](https://github.com/bedita/bedita/pull/1427) Travis PHP 7.2 + new Scrutinizer engine
* [#1426](https://github.com/bedita/bedita/pull/1426) feat: postman tests refactor + image&stream (upload, create stream, etc.)

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
