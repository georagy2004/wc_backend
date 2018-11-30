<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $jwt = new Jwt();
  
  $postData = $_POST;
  if($postData['arr'] == null)
    $postData['arr'] = '';
//    exit(ErrorCode::WX_ARRAY_NULL);
    
  if($postData['token'] == null)
    exit(ErrorCode::WX_TOKEN_NULL);
  
  //验证token是否正确、过期
  $tokenInfo = $jwt->verifyToken($postData['token']);
  if($tokenInfo === false)
    exit(ErrorCode::WX_LOGIN_TIMEOUT);
    
  // 对比数据库token，查看是否重复登陆或令牌无效
  $id = $tokenInfo['id'];
  $sql = "SELECT token FROM wx_user_token WHERE wx_user_id = {$id}";
  $validateToken = $dbh->selectFunc($sql);
  $validateToken = $validateToken[0]['token'];
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::WX_TOKEN_ERROR);
  $sql = "INSERT INTO wx_user_attention(company_id, wx_user_id) 
          VALUE('{$postData['arr']}', {$id}) 
          ON DUPLICATE KEY UPDATE 
          company_id = '{$postData['arr']}',
          wx_user_id = {$id}";

  $res = $dbh->wxUpdateFunc($sql);
  if($res !== true && $res != 00000)
    exit(ErrorCode::WX_UPDATE_ERROR);
  
  $sql = "SELECT company_id AS user_info_attention FROM wx_user_attention WHERE wx_user_id = {$id}";
  $res = $dbh->selectFunc($sql);
  if($res === false)
    exit(ErrorCode::WX_SELECT_ERROR);
  print_r($res);
  
?>
