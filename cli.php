<?php
$query = $argv[1];
require_once('address.php');

$db = new mysqli('localhost','root','');
echo $db->connect_error,"\n\n";
$db->select_db('addr2');

$result = null;

if($argc == 2 ){
$result = AddressHelper::findCity($db,$query);
}else if ($argc == 3)
{
  $result = AddressHelper::findStreet($db,$argv[2],$query);
}else if($argc == 4)
{
  $result = AddressHelper::getHouses($db,$argv[1]);
}
print_r($result);


