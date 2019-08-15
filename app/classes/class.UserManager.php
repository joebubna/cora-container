<?php
namespace Classes;

class UserManager {

  protected $factory;

  public function __construct($userFactory)
  {
    $this->factory = $userFactory;
  }
  
  public static function di_config($c)
  {
    return new \Classes\UserManager(
      $c->getFactory(\Classes\User::class)
    );
  }

  public function getUser($name)
  {
    return $this->factory->make($name);
  }
}