<?php
namespace Classes;

class Event {
  public $name;
  public $timestamp;

  public function __construct($name, $timestamp)
  {
    $this->name = $name;
    $this->timestamp = new \DateTime($timestamp);
  }
}