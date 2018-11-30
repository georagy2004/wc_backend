<?php
  require_once __DIR__ . '/../IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();
  $rd = new ReturnData();  
  
  $sql = "SELECT a.company_user_id, group_concat(CONCAT('{id:', b.company_project_id,'},'picture:', b.company_project_picture)) AS picture, group_concat(b.company_project_id) AS id FROM ggxcx_company_user AS a INNER JOIN ggxcx_company_project AS b ON(a.company_user_id = b.company_project_foreign_key) GROUP BY a.company_user_id";

//  $sql = "SELECT a.*, group_concat(b.company_project_picture) AS picture FROM (SELECT * FROM ggxcx_company_user LIMIT 0,6) AS a INNER JOIN ggxcx_company_project AS b ON (a.company_user_id = b.company_project_foreign_key)";
  
//  查询用户前3
//  用前三的结果去内连接查询项目

//  $sql = "select a1.* from article a1
//  inner join
//  (select a.type,a.date from article a left join article b
//  on a.type=b.type and a.date<=b.date
//  group by a.type,a.date
//  having count(b.date)<=3
//  )b1
//
//  on a1.type=b1.type and a1.date=b1.date
//  order by a1.type,a1.date desc ";



//  $sql = "select info.*, group_concat(picture.company_project_picture), group_concat(picture.company_project_id) from (SELECT * FROM ggxcx_company_info LIMIT 0,4) AS info
//  inner join
//  (select mya.company_project_id, mya.company_project_picture, myb.company_project_foreign_key from ggxcx_company_project AS mya left join ggxcx_company_project AS myb
//  on mya.company_project_foreign_key = myb.company_project_foreign_key and mya.company_project_picture <= myb.company_project_picture
//  group by mya.company_project_foreign_key, mya.company_project_picture
//  having count(myb.company_project_picture)<=4
//  )picture
//  on info.company_info_id = picture.company_project_foreign_key 
//  group by info.company_info_id ";


  $sql = "select info.company_user_name AS company_name, info.company_profile_photo AS head_picture,
          info.company_info_display AS certified, info.company_foreign_key AS company_id, info.company_project_tab AS company_tab, 
          group_concat(picture.company_project_picture) AS project_picture, group_concat(picture.company_project_id) AS project_id
          
          from (SELECT * FROM ggxcx_company_info LIMIT 0,4) AS info
          inner join
          (select mya.company_project_id, mya.company_project_picture, myb.company_project_foreign_key 
          from ggxcx_company_project AS mya left join ggxcx_company_project AS myb
          on mya.company_project_foreign_key = myb.company_project_foreign_key and mya.company_project_picture <= myb.company_project_picture
          group by mya.company_project_id
          having count(myb.company_project_picture)<=3
          )picture
          on info.company_info_id = picture.company_project_foreign_key 
          group by info.company_info_id ";



  $res = $dbh->selectFunc($sql);
  print_r($res);
  echo 'a'
?>
  