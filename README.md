# DBAL for PHP  
[![GitHub (pre-)release](https://img.shields.io/github/release/dawidgorecki/dbal-php/all.svg)](https://github.com/dawidgorecki/dbal-php/releases) [![GitHub license](https://img.shields.io/github/license/dawidgorecki/dbal-php.svg)](https://github.com/dawidgorecki/dbal-php/blob/master/LICENSE)


Database Abstraction Library for PHP. It based on PDO extension.

## Requirements

- PHP 7.1.0+
- PDO driver
- Enabled extensions: pdo_pgsql and/or pdo_mysql

## Installation

Installation via Composer is the recommended way to install. Add this line to your composer.json file:
```json
"dawidgorecki/dbal": "~1.0"
```
or run
```json
composer require dawidgorecki/dbal
```
## Usage

### Configuration
```php
use Reven\DBAL\Configuration\Dsn;
use Reven\DBAL\Configuration\Configuration;

$dsn = new Dsn('pgsql', 'my_db', 'localhost', 5432);
$config = new Configuration($dsn, 'username', 'passwd');
```
### Getting connection and DBAL instance
```php
use Reven\DBAL\DatabaseFactory;
use Reven\DBAL\DBALDatabase;

$conn = DatabaseFactory::getInstance()->getConnection($config);
$dbal = new DBALDatabase($conn);
```
## API
```php
// TODO
```
