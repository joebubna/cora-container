<?php
namespace Classes;

class TestMaster extends Test {

  protected $dep1;
  protected $dep2;
  protected $name;

  public function __construct($name, $dep1, $dep2)
  {
    //parent::construct();
    $this->name = $name;
    $this->dep1 = $dep1;
    $this->dep2 = $dep2;
  }


  public static function di_config($c, $name)
  {
    return new \Classes\TestMaster(
      $name,
      $c->{\Classes\Test1::class}(),
      $c->{\Classes\Test2::class}()
    );
  }


  public function sayHi($indentLevel = 0)
  {
    $indentLevel = $this->defaultIndent;
    $msg = "Hi there $this->name! Greetings from Classes/Test !!!<br>";
    $msg .= $this->indent($indentLevel).$this->dep1->sayHi($indentLevel + $this->defaultIndent);
    $msg .= $this->indent($indentLevel).$this->dep2->sayHi($indentLevel + $this->defaultIndent);
    return $msg;
  }

  public function verifyNestedValue()
  {
    return $this->dep2->verifyNestedValue();
  }

  public function getName()
  {
    return $this->name;
  }
}