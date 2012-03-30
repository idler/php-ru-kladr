<?php
$query = $argv[1];

$sql = <<<SQL

select 

c.kladr_name as name, 
c.kladr_socr as cs,
r.kladr_name as region,
r.kladr_socr as rs, 
s.kladr_name as subject , 
s.kladr_socr as ss

from KLADR c 
inner join KLADR r on r.kladr_code=concat(substr(c.kladr_code,1,6),'0000000') 
inner join KLADR s on s.kladr_code=concat(substr(c.kladr_code,1,3),'0000000000') where 
lower(c.kladr_name) like lower('%$query%');

SQL;

$db = new mysqli('localhost','root','');
echo $db->connect_error,"\n\n";
var_dump($db);
$db->select_db('kladr');
$result = $db->query($sql);
var_dump($result);
var_dump($db->error_list);

    /* извлечение ассоциативного массива */
while ($row = $result->fetch_assoc()) {
  print_r($row);
}




