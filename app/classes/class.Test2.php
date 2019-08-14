<?php
namespace Classes;

class Test2 extends Test {

  protected $dep1;
  protected $dep2;

  function __construct($dep1, $dep2)
  {
    //parent::__construct();
    $this->dep1 = $dep1;
    $this->dep2 = $dep2;
  }

  public static function di_config($c)
  {
    return new \Classes\Test2(
      $c->{\Classes\Test3::class}(),
      $c->{\Classes\Test4::class}()
    );
  }

  public function sayHi($indentLevel = 0)
  {
    $msg = 'Hi there from Classes/Test2 !!!<br>';
    $msg .= $this->indent($indentLevel).$this->dep1->sayHi($indentLevel + $this->defaultIndent);
    $msg .= $this->indent($indentLevel).$this->dep2->sayHi($indentLevel + $this->defaultIndent);
    return $msg;
  }

  public function verifyNestedValue()
  {
    return $this->dep1->verifyNestedValue();
  }
}