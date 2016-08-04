# BEdita, API-first content management framework

[![Build Status](https://travis-ci.org/bedita/bedita.svg?branch=4-develop)](https://travis-ci.org/bedita/bedita)
[![Code Coverage](https://codecov.io/gh/bedita/bedita/branch/4-develop/graph/badge.svg)](https://codecov.io/gh/bedita/bedita/branch/4-develop)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bedita/bedita/badges/quality-score.png?b=4-develop)](https://scrutinizer-ci.com/g/bedita/bedita/?branch=4-develop)
[![Dependency Status](https://gemnasium.com/badges/github.com/bedita/bedita.svg)](https://gemnasium.com/github.com/bedita/bedita)

## Install

Prerequisites:
 * PHP >= 5.5.9 or PHP 7
 * MySQL >= 5.5
 * [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)


1. Clone repository
2. Run composer install


 ```bash
 $ git clone -b 4-develop https://github.com/bedita/bedita.git
 $ cd bedita
 $ composer install
 ```

3. Configure your database connection editing `Datasources.default` in `config/app.php`
4. Run shell script to initialize the database

 ```bash
 $ bin/cake db_admin init
 ```

5. If you have configured a virtualhost in your webserver you can point
 the browser to `http://your-vhost/home` or you can take advantage of
 PHP server launching it with

 ```bash
 $ bin/cake server
 ```

 and following the link shown pointing to `/home` endpoint,
 for example `http://localhost:8765/home`


## Documentation

 * Developer documentation can be found [here](http://bedita.readthedocs.org/en/4-develop)

## Licensing

BEdita is released under [LGPL](/bedita/bedita/blob/master/LICENSE.LGPL), Lesser General Public License.

## More info

 * For an overview of BEdita please visit http://www.bedita.com/
 * Get support on [Google Groups](https://groups.google.com/forum/#!forum/bedita)

