<?php

/*
select m.kladr_code, 
concat( m.kladr_socr,' ' ,m.kladr_name) as MKR, 
k.kladr_code,concat( k.kladr_socr,' ', k.kladr_name) as City, 
concat(r.kladr_socr,' ',r.kladr_name) as Reg ,
concat(s.kladr_socr,' ' ,s.kladr_name) as Subj  
from KLADR m inner join KLADR k on k.kladr_code = concat(substr(m.kladr_code,1,9),'0000') 
inner join KLADR r on r.kladr_code=concat(substr(k.kladr_code,1,6),'0000000') 
inner join KLADR s on s.kladr_code=concat(substr(k.kladr_code,1,3),'0000000000') 
where lower(m.kladr_name) like 'костро%' and m.kladr_socr <> 'обл' and m.kladr_socr <> 'р-н'  
order by substr(m.kladr_code,4,3) , substr(m.kladr_code,9) 
limit 20;
 */


class AddressHelper
{
  public static function findCity($dbh,$query)
  {
    
    $sql = <<<SQL
    
select 
    lower(m.kladr_name) like '$query%' as strength,
    m.kladr_code as code,
    m.kladr_name as name,
    m.kladr_socr as socr,
    c.kladr_code as ccod,
    c.kladr_name as cname, 
    c.kladr_socr as cs,
    r.kladr_code as rcod,
    r.kladr_name as region,
    r.kladr_socr as rs, 
    s.kladr_code as scod,
    s.kladr_name as subject , 
    s.kladr_socr as ss
    
    from KLADR m
    inner join KLADR c on c.kladr_code=concat(substr(m.kladr_code,1,9),'0000')
    inner join KLADR r on r.kladr_code=concat(substr(m.kladr_code,1,6),'0000000') 
    inner join KLADR s on s.kladr_code=concat(substr(m.kladr_code,1,3),'0000000000') 
    where 
    lower(m.kladr_name) like lower('%$query%') and m.kladr_socr <> 'обл' and m.kladr_socr <> 'р-н' and m.kladr_socr <> 'Респ'

    order by strength desc, substr(m.kladr_code,4,3) asc, substr(m.kladr_code,9) asc
    limit 15;
    
SQL;
    
    $result = $dbh->query($sql);
    
    $retArr = array(); 
        /* извлечение ассоциативного массива */
    while ($row = $result->fetch_assoc()) {
      $retArr[] = $row;
    }
    return $retArr;
  }

  public static function findStreet($dbh,$city_code,$query)
  {
    if(12  < strlen($city_code))
    {
       $city_code = substr($city_code,0,12);
    }

    $sql = <<<SQL
    select *, lower(kladr_name) like lower('$query%') as strength from STREET
    where kladr_code like '{$city_code}%' and lower(kladr_name) like lower('%$query%') 
    order by strength desc , kladr_name asc
SQL;
    $result = $dbh->query($sql);
    var_dump($result);
  $retArr = array();
  while ($row = $result->fetch_assoc())
  {
    $retArr[] = $row;
  }

    return $retArr;

  }

  public static function getHouses($dbh,$street_code)
  {
    $sql = <<<SQL
   select kladr_name as name , kladr_code as code, kladr_index as `index` from DOMA where kladr_code like '$street_code%'
SQL;

    $result = $dbh->query($sql);
    var_dump($sql);

  $retArr = array();
  while ($row = $result->fetch_assoc())
  {
    $retArr[] = $row;
  }

    return self::resolveHouseNumbers($retArr);

  }

  public static function resolveHouseNumbers($nums)
  {
    $result = array();
    foreach ($nums as $chunk)
    {
      $index = $chunk['index'];
      $houses = explode(',',$chunk['name']);
      foreach($houses as $house)
      {
        $numbers = self::resolveNumberSequence($house);
        foreach($numbers as $n)
        {
          $result[$n] = $index;
        }
      }
    }
    return $result;
  }

  public static function resolveNumberSequence($seq)
  {
    $seq = trim($seq);
    if(preg_match('#Н\(\d+-\d+\)#is',$seq) || preg_match('#Ч\(\d+-\d+\)#is',$seq))
    {
      preg_match_all('#.\((\d+)-(\d+)\)#is',$seq,$matches);
       return range((int)$matches[1][0],(int)$matches[2][0],2);
    }
    if(preg_match_all('#(\d+)-(\d+)#is',$seq,$matches))
    {
      return range((int)$matches[1][0],(int)$matches[2][0],1);
    }

    return array(str_replace('стр',' стр ',$seq));

  }


}

