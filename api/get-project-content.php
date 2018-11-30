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
    
  $sql = "SELECT 
          company_project_content AS content
          FROM 
          ggxcx_company_project 
          WHERE company_project_id = {$postData['id']}";

  $res = $dbh->selectFunc($sql);
  $res = json_encode($res[0]);
  print_r($res)
  
?>
