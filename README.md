# Hclear-bot
A PHP bot, used to fix linter errors in wikipedia. (Developing)

## Features
* Fix [multiple-unclosed-formatting-tags](https://www.mediawiki.org/wiki/Help:Extension:Linter/multiple-unclosed-formatting-tags) errors
* ...

## Instructions
### Prepared work
* Hclear-bot requires PHP 7.0+, mbstring extension and cURL extension.
* A [owner-only OAuth consumer](https://www.mediawiki.org/wiki/OAuth/Owner-only_consumers)
### Config
1. Copy config.etc.php to config.php
2. Modify config.php according to your own needs.
### Run
```shell
$ php run.php
```
