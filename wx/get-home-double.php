<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);

  $getData = $_GET;
  if($getData['id'] == null)
    exit(ErrorCode::WX_ID_NULL);
  if($getData['content'] == null)
    exit(ErrorCode::WX_PARAMETER_ERROR);
  
  if($getData['content'] == 'right'){
    $sql = "SELECT left_content AS content,
            left_title AS title 
            FROM adv_company_home_double WHERE id = {$getData['id']}";
    $res = $dbh->selectFunc($sql);
  }else if($getData['content'] == 'left'){
    $sql = "SELECT right_content AS content,
           right_title AS title
           FROM adv_company_home_double WHERE id = {$getData['id']}";
    $res = $dbh->selectFunc($sql);
  }
  $res = json_encode($res[0]);
  print_r($res);
  
?>