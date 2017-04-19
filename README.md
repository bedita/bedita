# BEdita, a back-end API

[![Build Status](https://travis-ci.org/bedita/bedita.svg?branch=4-cactus)](https://travis-ci.org/bedita/bedita)
[![Code Coverage](https://codecov.io/gh/bedita/bedita/branch/4-cactus/graph/badge.svg)](https://codecov.io/gh/bedita/bedita/branch/4-cactus)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bedita/bedita/badges/quality-score.png?b=4-cactus)](https://scrutinizer-ci.com/g/bedita/bedita/?branch=4-cactus)
[![Dependency Status](https://gemnasium.com/badges/github.com/bedita/bedita.svg)](https://gemnasium.com/github.com/bedita/bedita)

BEdita 4 is a ready to use back-end API to handle the data of your mobile, IoT, web and desktop applications.
It's also an extensible framework to build your custom back-end API via plugins.

It provides a complete content management solution with:
 * an HTTP driven server application with a complete REST API to model, create, modify and retrieve data
 * a default admin web application (not yet available)

BEdita 4 is built with [CakePHP 3](http://cakephp.org) and uses relational DBMS like [MySQL](http://www.mysql.com),
[Postgres](https://www.postgresql.org) or [SQLite](http://sqlite.com) in conjunction with (optional) NoSQL systems like [Redis](http://redis.io/), [Elastic Search](https://www.elastic.co/) or time series databases to boost performance and scale up to Big Data scenarios.

[JSON-API](http://jsonapi.org) is the primary exchange data format.

Development is currently in alpha stage - DON'T USE IT ON A PRODUCTION SYSTEM


## Prerequisites

 * PHP 7 (recommended) or PHP 5.6
 * MySQL 5.7 (recommended) or MySQL 5.6, Postgres 9.5/9.6
 * [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)


## Install

For a detailed setup overview see [Setup Documentation](http://bedita.readthedocs.io/en/4-cactus/setup.html)

Quick setup in four steps.

1. Clone repository

 ```bash
 $ git clone -b 4-cactus https://github.com/bedita/bedita.git
 ```

2. Run composer install

 ```bash
 $ cd bedita
 $ composer install
 ```

3. Create an empty MySQL database

4. Run shell script to initialize the database and create first admin user

 ```bash
 $ bin/cake bedita setup
 ```

See [Web Server setup](http://bedita.readthedocs.io/en/4-cactus/setup.html#web-server)
to configure a virtualhost in your webserver.
To see first [`/home` endpoint](http://bedita.readthedocs.io/en/4-cactus/endpoints/home.html) response you may point the browser to `http://your-vhost/home`

Otherwise, only for development or test setups, you can take advantage of PHP builtin server
with this simple command:

 ```bash
 $ bin/cake server
 ```

 and see `/home` endpoint response pointing to `http://localhost:8765/home`

## Documentation

 * Developer documentation can be found [here](http://bedita.readthedocs.org/en/4-cactus)

## Licensing

BEdita is released under [LGPL](/bedita/bedita/blob/master/LICENSE.LGPL), Lesser General Public License.

