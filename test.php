<?php
require_once("address.php");

$data = ['3','4Астр2','8б','23-31','Ч(8-12)','Н(13-17)'];

$expect = ['3','4А стр 2','8б','23','24','25','26','27',
           '28','29','30','31','8','10','12','13','15','17'];

$result = [];
foreach($data as $num)
{
  $nums = AddressHelper::resolveNumberSequence($num);
  foreach($nums as $h)
  {
    $result[] = $h;
  }
}

if($expect == $result)
{
  echo "OK\n";
}else{
  echo "FAIL\n\n";
  var_dump($result);
}

