# 10up PHPCS Configuration

> Composer library to provide drop in installation and configuration of [WPCS](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards) and [PHPCompatibilityWP](https://github.com/PHPCompatibility/PHPCompatibilityWP), setting reasonable defaults for WordPress development with nearly zero configuration.

[![Support Level](https://img.shields.io/badge/support-active-green.svg)](#support-level) [![MIT License](https://img.shields.io/github/license/10up/phpcs-composer.svg)](https://github.com/10up/phpcs-composer/blob/master/LICENSE)

## Installation

Install the library via Composer:

```bash
$ composer require --dev 10up/phpcs-composer:dev-master
```

That's it!

## Usage

Lint your PHP files with the following command:

```bash
$ ./vendor/bin/phpcs .
```

If relying on Composer, edited the `composer.json` file by adding the following:

```json
	"scripts": {
		"lint": [
			"phpcs ."
		],
	}
```

Then lint via:

```bash
$ composer run lint
```

### Continuous Integration

PHPCS Configuration plays nicely with Continuous Integration solutions. Out of the box, the library loads the `10up-Default` ruleset, and checks for syntax errors for PHP 7 or higher.

To override the default PHP version check, set the `--runtime-set testVersion 7.0-` configuration option. Example for PHP version 7.2 and above:

```bash
$ ./vendor/bin/phpcs --runtime-set testVersion 7.2-
```

See more [information about specifying PHP version](https://github.com/PHPCompatibility/PHPCompatibility#sniffing-your-code-for-compatibility-with-specific-php-versions).

Note that you can only overrule PHP version check from the command-line.

### IDE Integration

Some IDE integrations of PHPCS fail to register the `10up-Default` ruleset. In order to rectify this, place `.phpcs.xml.dist` at your project root:

```xml
<?xml version="1.0"?>
<ruleset name="Project Rules">
	<rule ref="10up-Default" />
</ruleset>
```

## Support Level

**Active:** 10up is actively working on this, and we expect to continue work for the foreseeable future including keeping tested up to the most recent version of WordPress. Bug reports, feature requests, questions, and pull requests are welcome.

## Like what you see?

<p align="center">
<a href="http://10up.com/contact/"><img src="https://10up.com/uploads/2016/10/10up-Github-Banner.png" width="850"></a>
</p>
