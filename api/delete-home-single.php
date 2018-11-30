<?php
require_once __DIR__ . '/IncludeAll.php';
header('Access-Control-Allow-Origin:*');
$dbh = PdoConnect::getInstance($dsn, $user, $pswd);
$jwt = new Jwt();

  $postData = $_POST;
//  print_r($postData);
  // 验证token信息是否正确，并返回token参数
  $tokenInfo = $jwt->verifyToken($postData['token']);
  if($tokenInfo === false)
    exit(ErrorCode::E_LOGIN_TIMEOUT);
  // 对比数据库token，查看是否重复登陆
  $id = $tokenInfo['id'];
  $validateToken = $dbh->validateToken($postData['token'], $id);
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::E_VALIDATION_FAILS);

  $sql = "DELETE FROM ggxcx_company_home_single WHERE company_single_id = {$postData['id']}";
  $res = $dbh->deleteFunc($sql);
  
  if($res === true){
    echo 'true';
  }else{
    echo 'false';
  }

?>
