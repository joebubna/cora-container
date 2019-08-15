# Cora Collection

## Basic Usage

### Flexible interface that allows you to use your favorite format
```php
$collection = new \Cora\Collection();
$collection->add('Hello World');
echo $collection->get(0);       // Outputs "Hello World"
echo $collection[0];            // Outputs "Hello World"
echo $collection->get("off0");  // Outputs "Hello World"
echo $collection->off0;         // Outputs "Hello World"
```

### Easily filter contents using WHERE method
```php
$collection = new \Cora\Collection([
    new \Classes\Event('Debit', '10/10/1980'),
    new \Classes\Event('Debit', '10/10/2001'),
    new \Classes\Event('Deposit', '02/14/2008'),
    new \Classes\Event('Debit', '10/10/1990'),
    new \Classes\Event('Debit', '10/10/2003'),
    new \Classes\Event('Deposit', '02/14/2004')
]);
$this->assertEquals(6, $collection->count());
$this->assertEquals(4, count($collection->where('name', 'Debit')));
$this->assertEquals(2, count($collection->where('name', 'Deposit')));
$this->assertEquals(4, count($collection->where('timestamp', new \DateTime('01/01/2000'), '>=')));
```

### Can set an object property or associative array key as the primary for access
Here we pass in "name" as the second constructor argument:
```php
$collection = new \Cora\Collection([
    new \Classes\User('User1', 'Type1'),
    new \Classes\User('User2', 'Type1'),
    new \Classes\User('User3', 'Type2'),
    new \Classes\User('User4', 'Type2'),
    new \Classes\User('User5', 'Type1'),
    new \Classes\User('User6', 'Type3')
], 'name');

$this->assertEquals('User3', $collection->User3->name);
$this->assertEquals('Type2', $collection->User3->type);
$this->assertEquals('Type2', $collection->get('User3')->type);
```

### And more...

Supports merging, mapping, filtering, sorting, grouping, min, max, sum, count, etc. 



## Running Tests

If you have Docker, you can download the project and `docker-compose up` from the command line. 
Then run `./App/phpunit.sh tests` from the command line.

## Documentation

For complete documentation please see the GitHub pages website here:
http://joebubna.github.io/Cora/

## About Cora

Cora is a set of flexible tools for rapid app development. 

## License

The Cora framework is licensed under the [MIT license](http://opensource.org/licenses/MIT).

