<?php
$query = $type = $city = $street = $region = '';
$functions = array('findCity','findCityByRegion','findStreet','getHouses');
error_reporting(0);

$type = isset($_GET['cmd'])?$_GET['cmd']:'not found';
if( !in_array($type,$functions )){
  header('HTTP/1.1 404 Not Found');
}


$query     = isset($_GET['q'])      ? $_GET['q']      : '' ;
$city      = intval( isset($_GET['city'])   ? $_GET['city']   : '' );
$street    = intval( isset($_GET['street']) ? $_GET['street'] : '' );
$region    = intval( isset($_GET['region']) ? $_GET['region'] : '' );


require_once(__DIR__.'/address.php');



$db = new mysqli('localhost','max','');
echo $db->connect_error,"\n\n";
$db->select_db('kladr');
$db->query('set names utf8');



$result = null;

switch($type){
  case 'findCity':
    $result = AddressHelper::findCity($db,$query);
    break;
  case 'findCityByRegion':
    $result = AddressHelper::findCityByRegion($db,$query,$region);
    break;
  case 'findStreet':
    $result = AddressHelper::findStreet($db,$city,$query);
    break;
  case 'getHouses':
    $result = AddressHelper::getHouses($db,$street);
    break;
  default:
    header('HTTP/1.1 404 Not Found');
    exit();
}

header('Content-Type: text/plain; charset=utf-8');
echo json_encode($result);
exit;

