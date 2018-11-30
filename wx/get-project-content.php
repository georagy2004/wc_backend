<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);

  $getData = $_GET;
  if($getData['id'] == null)
    exit(ErrorCode::WX_ID_NULL);
  
  $sql = "SELECT content FROM adv_company_project WHERE id = :id";
  $params = array(':id' => $getData['id']);
  $res = $dbh->generalSelect($sql, $params);
  
  $res = json_encode($res[0]);
  print_r($res);
  