<?php
namespace Classes;

class Test4 extends Test {

  public static function di_config($c)
  {
    return new \Classes\Test4();
  }

  public function sayHi($indentLevel = 0)
  {
    return 'Hi there from Classes/Test4 !!!<br>';
  }
}