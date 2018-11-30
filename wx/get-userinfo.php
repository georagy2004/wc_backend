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
  $sql = "SELECT token  FROM wx_user_token WHERE wx_user_id = {$id}";
  $validateToken = $dbh->selectFunc($sql);
  $validateToken = $validateToken[0]['token'];
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::WX_TOKEN_ERROR);
    
  $sql = "SELECT 
          profile_photo AS picture,
          name,
          create_time AS registration 
          FROM wx_user 
          WHERE id  = {$id}";
          
  $res = $dbh->selectFunc($sql);
  $res[0]['registration'] = date("Y年m月d日", strtotime($res[0]['registration']));
  $res = json_encode($res[0]);
  print_r($res)
  

    
  
  ?>
  