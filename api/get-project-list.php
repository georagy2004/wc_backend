<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $jwt = new Jwt();
  
  $postData = $_POST;
  // 验证token信息是否正确，并返回token参数
  $tokenInfo = $jwt->verifyToken($postData['token']);
  if($tokenInfo === false)
    exit(ErrorCode::E_LOGIN_TIMEOUT);
    
  // 对比数据库token，查看是否重复登陆
  $id = $tokenInfo['id'];
  $validateToken = $dbh->validateToken($postData['token'], $id);
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::E_VALIDATION_FAILS);
    
  $sql = "SELECT id AS id, 
          title AS title, 
          cover AS cover 
          FROM 
          adv_company_project 
          WHERE adv_company_user_id = {$id}";

  $res = $dbh->selectFunc($sql);
  foreach($res as $key => $value){
    $res[$key]['cover'] = urldecode($res[$key]['cover']);
  }
  
  $res = json_encode($res);
  print_r($res)
  
?>
