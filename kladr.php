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
    $names = array();
    foreach($headers as $header)
    {
      $t = $ftypes[$header['type']];
      $header['name'] = strtolower($header['name']);
      $fields[] = "kladr_{$header['name']} {$t}"; 
      $names[] = "kladr_{$header['name']}";
  
    }
    echo "create table $table ( ".implode(", ",$fields)." )default charset=utf8 engine=innodb;\n";
    echo " /* DATA START */ \n"; 

    echo "insert into $table (".implode(',',$names).") VALUES ";

    for ($i = 1; $i <= $record_numbers; $i++) {
      if($i>1) echo ",";
      $row = dbase_get_record($db, $i);
      $fvals = array();
      unset($row['deleted']);
      $l = count($row);
      echo "(";
      for($ii =0 ; $ii<$l ; $ii++)
      {
        if($ii >0 ) echo " , ";
        $val = trim(iconv('cp866','utf-8',$row[$ii]));
        echo "'$val'"; 
      }
      echo ")\n";

    }
    echo ";\n";
  
  }else{
    die("can not open $fname\n");
  }
}
$a = glob('*.DBF');
echo "SET NAMES utf8;\n";
foreach($a as $f)
{
  echo " /** DUMPING $f **/\n\n";
  getSql($f);
}
