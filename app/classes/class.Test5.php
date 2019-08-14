<?php
namespace Classes;

class Test5 extends Test {

  public static function di_config($c)
  {
    return new \Classes\Test5();
  }

  public function sayHi($indentLevel = 0)
  {
    return 'Hi there from Classes/Test5 !!!<br>';
  }

  public function verifyNestedValue()
  {
    return 7;
  }
}