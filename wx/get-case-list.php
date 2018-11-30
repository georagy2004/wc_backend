<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  header('Content-Type:application/json');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();
  $rd = new ReturnData();
  
  $getData = $_GET;
  if($getData['id'] == null)
    exit(ErrorCode::WX_ID_NULL);
  
  $sql = "SELECT id, process_title, cover AS picture, process AS process 
          FROM adv_company_case WHERE adv_company_user_id = {$getData['id']}";
  $res = $dbh->selectFunc($sql);
  
  foreach($res as $key => $value){
    $res[$key]['picture'] = urldecode($res[$key]['picture']);
  }
  
  $res = json_encode($res);
  print_r($res);



?>