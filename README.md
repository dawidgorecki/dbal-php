# DBAL for PHP  
[![GitHub (pre-)release](https://img.shields.io/github/release/dawidgorecki/dbal-php/all.svg)](https://github.com/dawidgorecki/dbal-php/releases) [![GitHub license](https://img.shields.io/github/license/dawidgorecki/dbal-php.svg)](https://github.com/dawidgorecki/dbal-php/blob/master/LICENSE)


Database Abstraction Library for PHP. It based on PDO extension.

## Requirements

- PHP 7.1.0+
- PDO driver
- Enabled extensions: pdo_pgsql and/or pdo_mysql

## Installation

Installation via Composer is the recommended way to install. Add this line to your composer.json file:
```
"dawidgorecki/dbal": "~1.0"
```
or run
```
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

#### getPDO()
Returns a PDO instance representing a connection to a database
```php
$dbal->getPDO();
```
#### startTransaction()
Initiates a transaction (turns off autocommit mode)
```php
$dbal->startTransaction();
```
#### commit()
Commits a transaction, returning the database connection to autocommit mode
```php
$dbal->commit();
```
#### rollback()
Rolls back the current transaction
```php
$dbal->rollback();
```
#### fetchAll()
Returns an array containing all of the result set rows
```php
$users = $dbal->fetchAll("SELECT * FROM users");
    
/* 
Array
(
    [0] => Array
        (
            [id] => 1
            [name] => Dawid
            [age] => 31
        )
)
*/
```
#### fetchFirst()
Returns first row of the query result
```php
$user = $dbal->fetchFirst("SELECT * FROM users ORDER BY id", [], PDO::FETCH_ASSOC);
    
/*
Array
(
    [id] => 1
    [name] => Dawid
    [age] => 31
)
*/
```
#### fetchArray()
Returns first row of the query result as numeric indexed array
```php
$user = $dbal->fetchArray("SELECT * FROM users ORDER BY id");
    
/*
Array
(
    [0] => 1
    [1] => Dawid
    [2] => 31
)
*/
```
#### fetchAssoc()
Return first row of the query result as associative array
```php
$user = $dbal->fetchAssoc("SELECT * FROM users WHERE name = ?", ["Dawid"]);
    
/*
Array
(
    [id] => 1
    [name] => Dawid
    [age] => 31
)
*/
```
#### fetchColumn()
Returns a single column from the first row of the query result
```php
$user = $dbal->fetchColumn("SELECT * FROM users WHERE id = ?", [1], 1);

// Dawid
```
#### delete()
Deletes rows of a given table
```php
$dbal->delete('users', ["id" => 1]);
$dbal->delete('users', ["name" => "Dawid"]);
```
#### insert()
Inserts a row into the given table
```php
$dbal->insert('users', ["name" => "John", "age" => 35]);
```
#### update()
Updates rows of a given table
```php
$dbal->update('users', ["name" => "New John", "age" => 40], ["id" => 15]);
```
#### executeQuery()
Executes a prepared statement with the given SQL and parameters and returns PDOStatement instance
```php
$stmt = $dbal->executeQuery("SELECT * FROM users");

while ($user = $stmt->fetchObject()) {
    print_r($user);
}

/*
stdClass Object
(
    [id] => 1
    [name] => Dawid
    [age] => 31
)
stdClass Object
(
    [id] => 15
    [name] => New John
    [age] => 40
)
*/
```
#### updateQuery()
Executes a prepared statement with the given SQL and parameters and returns the affected rows count
```php
$rows_affected = $dbal->updateQuery("DELETE FROM users WHERE name = ?", ["New John"]);
```
#### prepare()
Prepare a given SQL statement and return the PDOStatement instance
```php
$stmt = $dbal->prepare("SELECT * FROM users WHERE name LIKE 'D%'");
$stmt->execute();

while ($user = $stmt->fetch(PDO::FETCH_NUM)) {
    print_r($user);
}

/*
Array
(
    [0] => 1
    [1] => Dawid
    [2] => 31
)
Array
(
    [0] => 12
    [1] => Dominik
    [2] => 1
)
*/
```
#### quote()
Quotes a string for use in a query
```php
$quoted = $dbal->quote("Hello", PDO::PARAM_STR);
```

#### lastId()
Return ID of the last inserted row
```php
$last_id = $dbal->lastId();
```
## License

Licensed under the MIT license. (http://opensource.org/licenses/MIT)
