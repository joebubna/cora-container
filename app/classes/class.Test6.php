<?php
namespace Classes;

class Test6 extends Test {

  public static function di_config($c)
  {
    return new \Classes\Test6();
  }

  public function sayHi($indentLevel = 0)
  {
    return 'Hi there from Classes/Test6 !!!<br>';
  }

  public function verifyNestedValue()
  {
    return 42;
  }
}