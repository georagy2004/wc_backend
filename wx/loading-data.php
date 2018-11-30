<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $jwt = new Jwt();
  
  $postData = $_POST;
  
  //验证token是否正确、过期
  $tokenInfo = $jwt->verifyToken($postData['token']);
  if($tokenInfo === false)
    exit(ErrorCode::E_LOGIN_TIMEOUT);
    
  // 对比数据库token，查看是否重复登陆
  $id = $tokenInfo['id'];
  $sql = "SELECT token FROM wx_user_token WHERE wx_user_id = {$id}";
  $validateToken = $dbh->selectFunc($sql);
  $validateToken = $validateToken[0]['token'];
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::WX_TOKEN_ERROR);
  
  $validateRes = [];
  $validateRes['validate'] = 1;
  
  $sql = "SELECT company_id  FROM wx_user_attention WHERE wx_user_id  = {$id}";
  $res = $dbh->selectFunc($sql);
  if($res === false)
    exit(ErrorCode::WX_SELECT_ERROR);
  $res = json_decode($res[0]['company_id']);
  $validateRes['attention'] = array_values(array_filter($res));
  if($validateRes['attention'] == null)
    $validateRes['attention'] = [];
  
  $res = json_encode($validateRes);
  print_r($res);
  
  
?>
  