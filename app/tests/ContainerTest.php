<?php
namespace Tests;

class ContainerTest extends TestCase
{   
  /**
   *  
   *
   *  @test
   */
  public function canRecursivelyInjectDependents()
  {
    $c = new \Cora\Container();
    $test = $c->{\Classes\TestMaster::class}('test');
    $this->assertEquals(7, $test->verifyNestedValue());
  }


  /**
   *  
   *
   *  @test
   */
  public function containerDefinitionsOverrideOnClassConfig()
  {
    $c = new \Cora\Container();

    // Explicitly define a resource on the container.
    // This should override any di_config() definition on the class.
    $c->{\Classes\TestMaster::class} = function($c) {
      return new \Classes\TestMaster(
        'test',
        $c->{\Classes\Test1::class}(),
        $c->{\Classes\Test6::class}()
      );
    };
    $test = $c->{\Classes\TestMaster::class}();
    $this->assertEquals(42, $test->verifyNestedValue());
  }


  /**
   *  
   *
   *  @test
   */
  public function eachRequestForAResourceReturnsANewObject()
  {
    $c = new \Cora\Container();

    // First object
    $test1 = $c->{\Classes\TestMaster::class}('Jacob');
    $this->assertEquals('Jacob', $test1->getName());

    // Second object
    $test2 = $c->{\Classes\TestMaster::class}('Annie');
    $this->assertEquals('Annie', $test2->getName());
  }


  /**
   *  Passing the DI definition for a resource into the singleton() method should 
   *  cause any requests for that resource to return the same item every time.
   *  NOTE: Defining an object as a singleton that requires runtime input, as seen in this example below,
   *  is probably a BAD idea as it won't even look at your inputs for the second call and won't give you any 
   *  errors. Don't do this in a real app... just using this for testing.
   *
   *  @test
   */
  public function verifySingletonsWorking()
  {
    $c = new \Cora\Container();

    // Define resource as a singleton
    $c->singleton(\Classes\TestMaster::class, function($c, $name) {
      return new \Classes\TestMaster(
        $name,
        $c->{\Classes\Test1::class}(),
        $c->{\Classes\Test6::class}()
      );
    });

    // First object
    $test1 = $c->{\Classes\TestMaster::class}('Jacob');
    $this->assertEquals('Jacob', $test1->getName());

    // Second object
    $test2 = $c->{\Classes\TestMaster::class}('Annie');
    $this->assertEquals('Jacob', $test2->getName());
  }


  /**
   *  
   *
   *  @test
   */
  public function grabbingNonExistantResourceTriggersException()
  {
    $this->expectException(\Exception::class);
    $c = new \Cora\Container();
    $c->{\Classes\NonDi::class}();
  }


  /**
   *  
   *
   *  @test
   */
  public function canUseDefinitionToCreateFactory()
  {
    $c = new \Cora\Container();
    $users = $c->{\Classes\UserManager::class}();
    $user1 = $users->getUser('John');
    $user2 = $users->getUser('Sarah');
    $this->assertEquals('John', $user1->name);
    $this->assertEquals('Sarah', $user2->name);
  }
}