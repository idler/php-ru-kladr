<?php 

$ftypes = array(
"character" => 'varchar(600)',
"date" => 'date',
"integer" => 'int',
"number" => 'float',
"boolean" => 'bool',
"float" => 'float',
);

function getSql($fname){
  global $ftypes;
  $table = str_ireplace(".dbf","",$fname);
  
  $db = dbase_open($fname, 0);
  
  if ($db) {
    $record_numbers = dbase_numrecords($db);
    
    echo "/*NUM: $record_numbers*/\n";
 //   var_dump(dbase_get_header_info($db));
    $headers = dbase_get_header_info($db);
    $fields = array();
    foreach($headers as $header)
    {
      $t = $ftypes[$header['type']];
      $header['name'] = strtolower($header['name']);
      $fields[] = "kladr_{$header['name']} {$t}"; 
  
    }
    echo "create table $table ( ".implode(", ",$fields)." )default charset=utf8 engine=innodb;\n";
    echo " /* DATA START */ \n"; 
    
    for ($i = 1; $i <= $record_numbers; $i++) {
      $row = dbase_get_record($db, $i);
      echo "insert into $table set ";
      $fvals = array();
      foreach ($row as $k=>$v)
      {
        if($k==='deleted') continue;
        $val = trim(iconv('cp866','utf-8',$v));
        $fvals[] = "kladr_{$headers[$k]['name']} = '$val' ";
      }
      echo implode(", ",$fvals).";";
      echo "\n/**************************************/\n\n";
    }
  }
  else{
    die("can not open $fname\n");
  }
}
$a = glob('*.DBF');
foreach($a as $f)
{
  echo " /** DUMPING $f **/\n\n";
  getSql($f);
}
