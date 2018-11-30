<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);

  $getData = $_GET;
  if($getData['id'] == null)
  exit(ErrorCode::WX_ID_NULL);
  
  $sql = "SELECT content, title
         FROM adv_company_home_single WHERE id = {$getData['id']}";
  $res = $dbh->selectFunc($sql);
  
  $res = json_encode($res[0]);
  print_r($res);
  
?>