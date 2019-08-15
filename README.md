# Cora Container

## Terms:

DI = Dependency Injection

## The Big Idea

This library helps you manage dependency injection for your application in a sane and easy to understand manner.
Rather than focus just on "services" or utilize magic to inject them (like some libraries and frameworks), 
this library gives you finer control over the injection process than probably any tool in existance. 

Using this tool you can wire up any class within your app for injection - even those that 
require runtime input. The dependency injections will happen recursively, potentially making a series of class instantiations that 
would be horrendous to do by hand a breeze. By handling all the injections for you, this also means you can change 
class signatures and add new dependencies in the future without having to modify every instantiation throughout your codebase.

Finally, if you hate cluttering up your codebase with factory classes (like me), rejoice! Each of the class definitions you 
create as part of wiring up your app for dependency injection can also be easily re-used to create factories when dynamic object creation
is needed within a class.

## Basic Usage

Everything revolves around the idea of defining resources for your application and how to create them. 

### Simple Example

Say you had the following class that needed a database abstraction class as a dependency:

```php
<?php
namespace Classes;

class UserManager {

  protected $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function fetch($id)
  {
    // do stuff
  }
} // end class
```

In order for the container to do dependency injection for you, you must define how an object of that class 
and any dependencies it requires are also created:


```php
<?php
// Create a container
$container = new \Cora\Container();

// Define some resources on the container
$container->{\Classes\UserManager::class} = function ($c) {
  return new \Classes\UserManager(
    $c->{\Classes\Database::class}()
  );
};

$container->{\Classes\Database::class} = function ($c) {
  return new \Classes\Database();
};

// Grab an instance of UserManager, letting the container handle injection of the database class
$users = $container->{\Classes\UserManager::class}();
$users->fetch($id);

```

### In-Class Definitions (with a more complicated example)

What you saw done in the "simple example" above are direct container definitions. The upside to doing definitions that way is 
that everything is in one place; however it's also the downside. The problem you'll encounter 
doing resource definitions that way is your definitions list will start to get REALLY LONG for larger apps.
You'll end up doing searches for class names and wondering to yourself if there's not some better way to split 
the definitions up in some organized way. 

It was this problem that spawned the addition of In-Class DI definitions. In class definitions allow you to add a static 
method named "di_config" to any class that defines how to instantiate it with injections. This allows you to avoid the long 
definitions file problem and still have everything organized in a distributed way such that you'll know exactly where to look 
to find the definition for any class (in the class file itself). 

Let's see how this would work with a more complicated version of the simple example above. In this one we'll see the DI container do recursive injection 
multiple levels deep:

```php
<?php
// Create a container
$container = new \Cora\Container();

// Define database connection as singleton so it will return the same object every time.
$container->singleton(\Classes\DatabaseConnection::class, function($c) {
  $config = include('/config/database.php');
  return new \Classes\DatabaseConnection($config);
});

// Grab an instance of UserManager, letting the container handle injection of dependencies
$users = $container->{\Classes\UserManager::class}();
$users->fetch($id);

```

Wait, I thought you said this was going to be a more complicated example??? It is. In the code above, the `$users = $container->{\Classes\UserManager::class}();` 
line actually causes a chain reaction of 6 classes being instantiated and injected. 
The UserManager class will have both a Database and UserRepository class injected into it as dependencies, but each of those also in-turn 
require their own dependencies. To understand what's going on, look at the class definitions below and see how for any dependencies defined in the constructor 
for a class, there will be a matching di_config() method that defines how to create the object using the injection container. If that doesn't clear things up, 
below these class definitions I'll write out the equivalent of what's going on if you were to do the same thing by hand.

```php
<?php
namespace Classes;

class UserManager {

  protected $db;
  protected $repo;

  public function __construct($db, $userRepository)
  {
    $this->db = $db;
    $this->repo = $userRepository;
  }

  public static function di_config($c)
  {
    return new \Classes\UserManager(
      $c->{\Classes\Database::class}(),
      $c->{\Classes\UserRepository::class}()
    );
  }

  public function fetch($id)
  {
    // do stuff
  }
} // end class
```

```php
<?php
namespace Classes;

class Database {

  protected $conn;

  public function __construct($connection)
  {
    $this->conn = $connection;
  }

  public static function di_config($c)
  {
    return new \Classes\Database(
      $c->{\Classes\DatabaseConnection::class}()
    );
  }
} // end class
```

```php
<?php
namespace Classes;

class UserRepository {

  protected $gateway;
  protected $factory;

  public function __construct($gateway, $factory)
  {
    $this->gateway = $gateway;
    $this->factory = $factory;
  }

  public static function di_config($c)
  {
    return new \Classes\UserRepository(
      $c->{\Classes\UserGateway::class}(),
      $c->{\Classes\UserFactory::class}()
    );
  }
} // end class
```

```php
<?php
namespace Classes;

class UserGateway {

  protected $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public static function di_config($c)
  {
    return new \Classes\UserGateway(
      $c->{\Classes\Database::class}()
    );
  }
} // end class
```

```php
<?php
namespace Classes;

class UserFactory {

  public function __construct()
  {
    // stuff
  }

  public static function di_config($c)
  {
    return new \Classes\UserFactory();
  }
} // end class
```

#### Doing it by hand

Above we stated that the `$users = $container->{\Classes\UserManager::class}();` line was invoking the DI container to do a whole load of work for us 
by creating and injecting a number of classes. Using the same class definitions as seen above, let's look at what doing the equivalent work by hand 
would look like:

```php
$databaseConfig = include('/config/database.php');
$databaseConnection = new \Classes\DatabaseConnection($databaseConfig);
$users = new \Classes\UserManager(
  new \Classes\Database($databaseConnection),
  new \Classes\UserRepository(
    new \Classes\UserGateway(
      new \Classes\Database($databaseConnection)
    ),
    new \Classes\UserFactory()
  )
);
$users->fetch($id);
```


#### How On-Class Definitions Work...

Although this may seem a bit magical, it's really not. When a resource is asked for that isn't explicitly defined on the container, 
the container code will call `elseif (method_exists($name, 'di_config')) {` to check if the class exists and has a "di_config" 
method defined on it. If such a class and method exists, then it will use the on-class definition. Very simple.

For the purists out there, let's be clear that this is NOT the 
service locator pattern - you aren't passing the container to the constructor when creating an instance of an object. The 
DI config method is static and serves only to hold the definition. The object will only receive the dependencies it needs 
injected through the constructor. The ability to have the definition right next to the constructor function is convenient, 
and certainly better than a 2x increase in the number of files that would happen if each class needed a separate definition file.

### Runtime Input

You can easily setup definitions to accept runtime inputs like so:

```php
<?php
namespace Classes;

class User {

  public $name;

  public function __construct($name)
  {
    $this->name = $name;
  }

  public static function di_config($c, $name)
  {
    return new \Classes\User($name);
  }
} // end class
```

```php
<?php
// Create a container
$container = new \Cora\Container();

$user = $container->{\Classes\User::class}('Bob');
echo $user->name; // Outputs "Bob"
```

### Abstract Factories

When you define how to create an object for the container, you are essentially doing the same thing you would do 
if you were creating a factory for a class. Both a container and a factory require some logic be built-in to know 
how to create an object, and they return that object to you. So if you are already defining how an object is made for 
the container, why duplicate that logic in any factory files? This was the inspiration behind Abstract Factories.

An Abstract Factory is a generic wrapper you can use with any class, all you need is the DI definition. The biggest difference 
between using a factory built using the AbstractFactory class vs. a typical factory is that in a typical factory the call to 
make() is usually static like `$factory::make()`. When using an AbstractFactory it won't know what class to make for you until
you instantiate it, so calls to make must be non-static like so: `$factory->make()`.

To show how you can create dynamic factories for injection, let's pretend you have some sort of UserManager class that needs 
to create User objects. The normal solution would be to create a UserFactory class, but see below for how you could avoid that:

```php
<?php
namespace Classes;

class UserManager {

  protected $factory;

  public function __construct($userFactory)
  {
    $this->factory = $userFactory
  }

  public static function di_config($c)
  {
    return new \Classes\UserManager(
      $c->getFactory(\Classes\User::class)
    );
  }

  public function create($name)
  {
    return $this->factory->make($name);
  }
} // end class
```

```php
<?php
namespace Classes;

class User {

  public $name;

  public function __construct($name)
  {
    $this->name = $name;
  }
  
  public static function di_config($c, $name)
  {
    return new \Classes\User($name);
  }
}
```

```php
// Create a container
$container = new \Cora\Container();
$users = $container->{\Classes\UserManager::class}();
$user = $users->create('John');
echo $user->name; // Outputs "John"
```

## Running Tests

If you have Docker, you can download the project and `docker-compose up` from the command line. 
Then run `./App/phpunit.sh tests/AutoloadTest` from the command line.

## Documentation

For complete documentation please see the GitHub pages website here:
http://joebubna.github.io/Cora/

## About Cora

Cora is a set of flexible tools for rapid app development. 

## License

The Cora framework is licensed under the [MIT license](http://opensource.org/licenses/MIT).

