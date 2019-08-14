<?php
namespace Classes;

class Test3 extends Test {

  protected $dep1;

  public function __construct($dep1)
  {
    //parent::__construct();
    $this->dep1 = $dep1;
  }

  public static function di_config($c)
  {
    return new \Classes\Test3(
      $c->{\Classes\Test5::class}()
    );
  }

  public function sayHi($indentLevel = 0)
  {
    $msg = 'Hi there from Classes/Test3 !!!<br>';
    $msg .= $this->indent($indentLevel).$this->dep1->sayHi($indentLevel + $this->defaultIndent);
    return $msg;
  }

  public function verifyNestedValue()
  {
    return $this->dep1->verifyNestedValue();
  }
}