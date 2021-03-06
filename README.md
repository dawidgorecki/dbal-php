# DBAL for PHP    
[![GitHub (pre-)release](https://img.shields.io/github/release/dawidgorecki/dbal-php/all.svg)](https://github.com/dawidgorecki/dbal-php/releases) [![GitHub license](https://img.shields.io/github/license/dawidgorecki/dbal-php.svg)](https://github.com/dawidgorecki/dbal-php/blob/master/LICENSE)


Database Abstraction Library for PHP with ActiveRecord features. It based on PDO extension.

## Requirements
- PHP 7.1.0+
- PDO driver
- Enabled extensions: pdo_pgsql and/or pdo_mysql

## Installation

Installation via Composer is the recommended way to install. Add this line to your composer.json file:
```
"dawidgorecki/dbal": "~2.0"
```
or run
```
composer require dawidgorecki/dbal
```
## Usage

### Configuration
```php
use Reven\DBAL\Configuration\DBConfig;
use Reven\DBAL\Configuration\DSN;

$dsn = new DSN(DSN::DRIVER_PGSQL, 'my_db');
// $dsn = new DSN(DSN::DRIVER_MYSQL, 'my_db', 'localhost', DSN::PORT_MYSQL);

$config = new DBConfig($dsn, 'username', 'passwd');
// $config = new DBConfig($dsn, 'username', 'passwd', 'utf8', true);
```
### Getting connection and DBAL instance
Using Connection Manager:
```php
use Reven\DBAL\ConnectionManager;
use Reven\DBAL\DBALDatabase;
use Reven\DBAL\Exceptions\DBALException;

try {
    ConnectionManager::createConnection($config);
    // ConnectionManager::createConnection($config1, 'db1', PDO::FETCH_ASSOC);
    // ConnectionManager::createConnection($config2, 'db2', PDO::FETCH_OBJ);
} catch (DBALException $e) {
    die($e);
}

$dbal = new DBALDatabase(ConnectionManager::getConnection());
// $dbal = new DBALDatabase(ConnectionManager::getConnection('db1'));
```
Using Database Factory:
```php
use Reven\DBAL\DatabaseFactory;
use Reven\DBAL\DBALDatabase;
use Reven\DBAL\Exceptions\DBALException;

try {
    $pdo = DatabaseFactory::getConnection($config, PDO::FETCH_ASSOC);
} catch (DBALException $e) {
    die($e);
}

$dbal = new DBALDatabase($pdo);
```
### Errors & Exceptions
You can turn off all exceptions thrown by DBALDatabase by setting false as second parameter in object constructor.
```php
$dbal = new DBALDatabase($pdo, false);
```
Getting error message and query string:
```php
$dbal->getLastError();
$dbal->getQueryString();
```
## DBAL API

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
Returns all rows of the query result
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
Returns first row of the query result as associative array
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
## ActiveRecord
Model private properties should have the same names as columns in database table.
### Naming convention
 * `Database Table` - Plural with underscores separating words (e.g., user_details)
 * `Model Class` - Singular with the first letter of each word capitalized (e.g., UserDetail)
### Usage
Extend your model class with `Reven\DBAL\ActiveRecord`
```php
<?php

namespace Reven\DBAL;

class User extends ActiveRecord
{
}
```
Create database connection
```php
ConnectionManager::createConnection($config, 'active_record');
```
Set connection name if it's not default  
```php
User::setConnectionName('active_record');
```
Change default database table (optional)
```php
User::setTableName('employers');
``` 
### Basic CRUD

#### Create
To create a new record in database (e.g. add new user) we instantiating a new object and then invoking the save() method.  
```php
// INSERT INTO users(name,email) VALUES('John','john@gmail.com')  
$user = new User();
$user->setName("John");
$user->setEmail("john@gmail.com");
$user->save();
```

#### Read
These are your basic methods to find and retrieve records from your database.  
```php
// SELECT * FROM users WHERE id=1 LIMIT 1
$user = User::findById(1);
echo $user->getName();

// SELECT * FROM users
$users = User::findAll();
foreach ($users as $user) {
    echo $user->getName();
}

// SELECT * FROM users WHERE email='john@gmail.com'
$users = User::findByQuery("SELECT * FROM users WHERE email=:email", [":email" => "john@gmail.com"]);
echo $users[0]->getName();
```

#### Update
To update you would just need to find a record first and then change one of attributes.  
```php
// UPDATE users SET name='Edwin' WHERE id=1
$user = User::findById(1);
$user->setName("Edwin");
$user->save();
```

#### Delete
That will call SQL query to delete the record in your database.  
```php
// DELETE FROM users WHERE id=1
$user = User::findById(1);
$user->delete();
```
## License

Licensed under the MIT license. (http://opensource.org/licenses/MIT)
