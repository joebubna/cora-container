<?php
namespace Classes;

class Test {

  protected $defaultIndent = 6;

  public function indent($indentLevel)
  {
    $spacer = '';
    for ($i=0; $i < $indentLevel; $i++) {
      $spacer .= '&nbsp;';
    }
    return $spacer;
  }
}