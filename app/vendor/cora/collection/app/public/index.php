<?php
// Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include Composer autoload
include('../vendor/autoload.php');

$collection = new \Cora\Collection([
  ['name'=>'User1', 'type'=>'Type1'],
  ['name'=>'User2', 'type'=>'Type1'],
  ['name'=>'User3', 'type'=>'Type2'],
  ['name'=>'User4', 'type'=>'Type2'],
  ['name'=>'User5', 'type'=>'Type1'],
  ['name'=>'User6', 'type'=>'Type3']
], 'name');

echo "Size of the collection: ".$collection->count()."<br>";

$subset = $collection->where('type', 'Type2');
echo "Items of Type2: ".count($subset)."<br>";
echo "The name of the third user: ".$subset->User3['name']."<br>";
echo "<br><br>";

echo $collection->get(0)['name']."<br>";
echo $collection->get('User1')['name']."<br>";