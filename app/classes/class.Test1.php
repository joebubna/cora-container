<?php
namespace Classes;

class Test1 extends Test {

  public static function di_config($c)
  {
    return new \Classes\Test1();
  }

  public function sayHi($indentLevel = 0)
  {
    return 'Hi there from Classes/Test1 !!!<br>';
  }
}