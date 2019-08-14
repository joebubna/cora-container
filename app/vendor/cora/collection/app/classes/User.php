<?php
namespace Classes;

class User {
  public $name;
  public $type;

  public function __construct($name, $type)
  {
    $this->name = $name;
    $this->type = $type;
  }
}