<?php
  require_once __DIR__ . '/../IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();
  $rd = new ReturnData();  
  
//  $sql = "SELECT a1.* FROM test a1 LEFT JOIN (SELECT time FROM test WHERE fore = 1 GROUP BY time having b1 on a1.time = b1.time group by a1.time ";
//$sql = "SELECT a1.* FROM test a1 INNER JOIN (SELECT a.* FROM test a WHERE a.fore = 1 AND a.type = 2 LIMIT 0,1)b1 ON a1.time = b1.time GROUP BY id ORDER BY time";

//  $sql = "SELECT * FROM 
//        (
//          SELECT a.company_double_id AS left_id, a.company_double_picture AS left_picture 
//          FROM ggxcx_company_home_double AS a
//          INNER JOIN
//          (
//            SELECT b.company_double_id AS rigth_id, b.company_double_picture AS rigth_picture, b.company_double_group AS double_type
//            FROM ggxcx_company_home_double AS b WHERE b.company_double_type = 2
//          ) res
//          ON a.company_double_group = res.double_type
//          group by left_id 
//          ) result";
        
        
  $sql = "SELECT * FROM 
        
          (SELECT company_double_id AS left_id, company_double_picture AS left_picture, company_double_group AS double_group,
                  company_double_sort AS double_sort
          FROM ggxcx_company_home_double WHERE company_double_foreign_key = 5) AS a
          INNER JOIN
          (
            SELECT company_double_id AS rigth_id, company_double_picture AS rigth_picture, company_double_group AS double_group
            FROM ggxcx_company_home_double AS b WHERE company_double_type = 2
          ) AS res
          ON a.double_group = res.double_group
          GROUP BY a.double_group
          ORDER BY a.double_sort ";
           
  $sql = "select b.* from tb b where 2 > (select count(*) from tb where name = b.name and val > b.val ) ";
        
        
  $res = $dbh->selectFunc($sql);
  print_r($res);
  
  ?>