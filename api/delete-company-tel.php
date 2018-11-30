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
    
  $sql = "DELETE FROM adv_company_tel WHERE id = :id";
  $params = array(':id' => $postData['id']);
  $res = $dbh->generalDelete($sql, $params);
  
  if($res === false)
    exit('添加错误');
    
  $sql = "SELECT id, name, job, tel FROM adv_company_tel WHERE adv_company_user_id = :id";
  $params = array(':id' => $id);
  $res = $dbh->generalSelect($sql, $params);
  print_r(json_encode($res));
  
?>
    