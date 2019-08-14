<?php
namespace Cora;

/**
 *  Cora\AbstractFactory
 * 
 *  Has one public method "make" which takes a variable number of arguments and returns an object.
 */
class AbstractFactory
{
	protected $method;
  protected $container;

  /**
   * @param \Cora\Container $container A cora dependency injection container.
   * @param \Closure $method A closure that defines how to create a resource.
   * @return void
   */
  public function __construct($container, \Closure $method)
  {
    $this->container = $container;
    $this->method = $method;
  }


  /**
   * Intercepts function calls. Handles logic for calls to make()
   * 
   * @return Object
   */
  public function __call($name, $arguments)
  {
    if ($name == 'make') {
      // Add a Container reference as first argument.
      array_unshift($arguments, $this->container);

      // Call the callback with the provided arguments.
      return $this->assemble($arguments);
    } else {
      throw new \Exception('No such method');
    }
  }


  /**
   * Returns an object of the defined type.
   * 
   * @param Array $args
   * @return Object
   */
  protected function assemble($args)
	{
    return call_user_func_array($this->method, $args);
	}
}
