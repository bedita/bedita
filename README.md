# BEdita, a back-end API

[![Build Status](https://travis-ci.org/bedita/bedita.svg?branch=4-cactus)](https://travis-ci.org/bedita/bedita)
[![Code Coverage](https://codecov.io/gh/bedita/bedita/branch/4-cactus/graph/badge.svg)](https://codecov.io/gh/bedita/bedita/branch/4-cactus)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bedita/bedita/badges/quality-score.png?b=4-cactus)](https://scrutinizer-ci.com/g/bedita/bedita/?branch=4-cactus)
[![Dependency Status](https://gemnasium.com/badges/github.com/bedita/bedita.svg)](https://gemnasium.com/github.com/bedita/bedita)

BEdita 4 is a ready to use back-end API to handle the data of your mobile, IoT, web and desktop applications.
It's also an extensible framework to build your custom back-end API via plugins.

It provides a complete content management solution with:

* a _headless_ HTTP server application with a complete REST API to model, create, modify and retrieve data
* a default admin web application (not yet available)

BEdita 4 is built with [CakePHP 3](http://cakephp.org) and uses relational DBMS like [MySQL](http://www.mysql.com),
[Postgres](https://www.postgresql.org) or [SQLite](http://sqlite.com) in conjunction with (optional) NoSQL systems like [Redis](http://redis.io/), [Elastic Search](https://www.elastic.co/) or time series databases to boost performance and scale up to Big Data scenarios.

[JSON-API](http://jsonapi.org) is the primary exchange data format.

[GrapQL](http://graphql.org) initial support available via dedicated plugin.

Development is currently in _beta_ stage - DON'T USE IT ON A PRODUCTION SYSTEM

The easiest and quickest way to try out BEdita4 is via [Docker](https://www.docker.com), [read instructions below](#docker).

## Prerequisites

* PHP 7 (recommended) or PHP 5.6
* MySQL 5.7 (recommended) or MySQL 5.6, Postgres 9.5/9.6 or SQLite 3
* [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

## Install

For a detailed setup overview see [Setup Documentation](https://docs.bedita.net/en/latest/setup.html)

Quick setup in three steps.

1. Create project via composer

```bash
 composer create-project -s dev bedita/bedita
```

If you are using a **.zip** or **.tar.gz** release file you just need to unpack it and then run ``composer install``.

1. Create an empty database either MySQL or PostgresSQL. Do nothing for SQLite.

1. Change to the newly created folder and run this script to initialize the database and create first admin user:

```bash
 bin/cake bedita setup
```

See [Web Server setup](https://docs.bedita.net/en/latest/setup.html#web-server)
to configure a virtualhost in your webserver.
To see first [`/home` endpoint](https://docs.bedita.net/en/latest/endpoints/home.html) response you may point to `http://your-vhost/home`

Curl example:

```bash
 curl -H Accept:application/json http://your-vhost/home
```

Otherwise, only for development or test setups, you can take advantage of PHP builtin server with this simple command:

```bash
 bin/cake server
```

and see `/home` endpoint response pointing to `http://localhost:8765/home` like this:

```bash
 curl -H Accept:application/json http://localhost:8765/home
```

For an explanation on `Accept` headers usage [read here](https://docs.bedita.net/en/latest/endpoints/intro.html#headers).

## Docker

See [Docker setup documentation](https://docs.bedita.net/en/latest/setup.html#setup-docker) for a more detailed overview.

### Pull official image

Get latest offical image build from Docker Hub

```bash
 docker pull bedita/bedita:latest
```

You may also use `:4-cactus` tag instead of `:latest`, they are currently synonyms.

### Build image

If you want to build an image from local sources you can do it like this from BEdita root folder:

```bash
 docker build -t bedita4-local .
```

You may of course choose whatever name you like for the generated image instead of `bedita4-local`.

### Run

Run a Docker image setting an initial API KEY and admin username and password like this:

```bash
 docker run -p 8090:80 --env BEDITA_API_KEY=1029384756 \
    --env BEDITA_ADMIN_USR=admin --env BEDITA_ADMIN_PWD=admin \
    bedita/bedita:latest
```

This will launch a BEdita4 instance using `SQLite` as its storage backend. It should become available at http://localhost:8090/home almost instantly.

Replace `bedita/bedita:latest` with `bedita4-local` (or other chosen name) to lanch a local built image.

## Documentation

Developer documentation can be found [here](https://docs.bedita.net)

## Licensing

BEdita is released under [LGPL](/bedita/bedita/blob/master/LICENSE.LGPL), Lesser General Public License v3.
