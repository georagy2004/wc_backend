<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $jwt = new Jwt();
  
  $postData = $_POST;
  if($postData['id'] == null)
  exit('ID为空');
  
  // 验证token信息是否正确，并返回token参数
  $tokenInfo = $jwt->verifyToken($postData['token']);
  if($tokenInfo === false)
    exit(ErrorCode::E_LOGIN_TIMEOUT);
    
  // 对比数据库token，查看是否重复登陆
  $id = $tokenInfo['id'];
  $validateToken = $dbh->validateToken($postData['token'], $id);
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::E_VALIDATION_FAILS);
    
  if($postData['left_content'] == true){
    $sql = "SELECT left_content AS content FROM adv_company_home_double WHERE id = {$postData['id']}";
    $res = $dbh->selectFunc($sql);
    $res = json_encode($res[0]);
    print_r($res);
    
  }else if($postData['right_content'] != null){
    $sql = "SELECT right_content AS content FROM adv_company_home_double WHERE id = {$postData['id']}";
    $res = $dbh->selectFunc($sql);
    $res = json_encode($res[0]);
    print_r($res);
    
  }else{
    exit('参数错误');
  }
    


  
?>
