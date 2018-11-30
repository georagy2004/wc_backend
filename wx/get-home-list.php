<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  header('Content-Type:application/json');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();
  $rd = new ReturnData();
  
  $getData = $_GET;
  if($getData['id'] == null)
  exit('ID为空');
  
  $sql = "SELECT * FROM (
          (SELECT 
          id AS id, 
          type AS type,
          left_title AS title, 
          left_cover AS cover, 
          left_content AS content, 
          right_title AS right_title, 
          right_cover AS right_cover,
          right_content AS right_content, 
          sort AS sort,
          create_time AS time
          FROM adv_company_home_double WHERE adv_company_user_id = {$getData['id']}) 
          UNION ALL 
          (SELECT 
          id, 
          type, 
          title,
          cover, 
          content,
          NULL,
          NULL,
          NULL,
          sort,
          create_time
          FROM adv_company_home_single WHERE adv_company_user_id = {$getData['id']}) 
          )AS res
          ORDER BY res.sort, res.time
          ";
  $res = $dbh->selectFunc($sql);
  
  foreach($res as $key => $value){
    $res[$key]['cover'] = urldecode($res[$key]['cover']);
    $res[$key]['right_cover'] = urldecode($res[$key]['right_cover']);
  }
  
  $res = json_encode($res);
  print_r($res);





?>