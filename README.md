# Frodo

A simple link shortener in php

#### Table of Contents

* [Getting started](#getting-started)
	* [Install dependencies](#install-dependencies)
* [Run Locally](#run-locally)
	* [Make some short urls](#make-some-short-urls)
	* [Redirection](#redirection)
	* [Stats](#stats)
* [Contributing](#contributing)
* [Install dev dependencies](#install-dev-dependencies)
* [Discussion and Improvements](#discussion-and-improvements)

## Getting started

### Install dependencies

Frodo depends on php 7.x and sqlite3.

Make sure you have an appropriate version of php (check `php -v`), and install sqlite3 using your preferred package manager. 

All other dependencies are managed by [composer](https://getcomposer.org/).

```
$ git clone <repo>
Cloning into 'frodo'...
$ cd frodo
$ make install
Installing composer dependencies (no dev)...
php bin/composer.phar install --no-dev --no-interaction --no-progress --quiet
```

### Run Locally

Run frodo using the builtin php development server. (This server is not meant for production. In production frodo will use [apache](https://httpd.apache.org/).)

```
$ php -S localhost:8080 router.php
```

#### Make some short urls

```
$ curl 'localhost:8080/tools/shorten?longurl=https:/www.google.com/' | jq
{
  "status": 200,
  "response": "http://localhost:8080/b"
}
```
(Example uses jq - commandline JSON processor to make the output more readable)

#### Redirection

```
$ curl -v "http://localhost:8080/b"
*   Trying ::1...
* TCP_NODELAY set
* Connected to localhost (::1) port 8080 (#0)
> GET /b HTTP/1.1
> Host: localhost:8080
> User-Agent: curl/7.54.0
> Accept: */*
>
< HTTP/1.1 302 Found
< Host: localhost:8080
< Date: Sun, 14 Apr 2019 06:31:50 +0000
< Connection: close
< X-Powered-By: PHP/7.1.26
< Location: https://www.google.com/
< Content-type: text/html; charset=UTF-8
<
* Closing connection 0
```

#### Stats

```
$ curl "http://localhost:8080/tools/stats" | jq
{
  "status": 200,
  "response": {
    "most_visited": {
      "b": 2,
      "d": 1
    },
    "most_shortened": {
      "https://www.example.com/": 3,
      "https://www.foo.com/": 1,
      "https://www.google.com/": 1,
    }
  }
}
```
```
$ curl "http://localhost:8080/tools/stats?shorturl=b" | jq
{
  "status": 200,
  "response": {
    "short_url": "b",
    "long_url": "https://www.google.com/",
    "create_date": "2019-04-14T06:28:59+00:00",
    "total_hits": 2,
    "hits_per_day_hist": {
      "2.00": 1
    }
  }
}
```

## Contributing

### Install dev dependencies

```
$ git clone <repo>
Cloning into 'frodo'...
$ cd frodo
$ make
Installing composer dependencies (including dev)...
php bin/composer.phar install --no-dev --no-interaction --no-progress --quiet
```

This will also run the code sniffer, phpunit suite, and [phan](https://github.com/phan/phan) (static analysis). Phan depends on PHP 7.x with the php-ast extension (0.1.5+ or 1.0.0+) and supports PHP version 7.0-7.3 syntax. Installation instructions for php-ast can be found here.

You can run these checks separately as well, e.g.

```
make phpunit
```

See Makefile for all targets.

## Discussion and Improvements

- Sqlite is convenient, but if we need to scale the application, we'll need to run the database on a separate node or cluster. Switch to e.g. MySQL
- Caching: In the real world, a link shortener would be a read-heavy application. Additionally, a small portion of shortened urls probably make up for a large volume of the traffic. And the mapping from short to long url never changes, so we don't need to worry about invalidation (unless we were to implement deletion).
- Server environment: Run the application in apache
- Separate visit logging from main read path