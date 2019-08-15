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