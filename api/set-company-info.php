<?php
require_once __DIR__ . '/IncludeAll.php';
header('Access-Control-Allow-Origin:*');
$dbh = PdoConnect::getInstance($dsn, $user, $pswd);
$jwt = new Jwt();
$rd = new ReturnData();
  
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
  
  $project_tab = json_encode($postData['type'], JSON_UNESCAPED_UNICODE);
  $setUserinfo = $dbh->updateUserinfo($postData['project'], $postData['adress'], $project_tab, $postData['introduction'], $id);
  if($setUserinfo === false)
    exit(ErrorCode::E_UPLOAD_DATA);
    
  // 更新token
  $pl = [
    'iss'=>'jwt_admin', //该JWT的签发者
    'iat'=>time(), //签发时间
    'exp'=>time()+7200, //过期时间
    'nbf'=>time(), //该时间之前不接收处理该Token
    'sub'=>'dobeboy.net', //面向的用户
    'jti'=>md5(uniqid('JWT').time()), //该Token唯一标识
    'id'=>$id,
    ];
  $token = Jwt::getToken($pl);
  $updateToken = $dbh->updateToken($token, $id);
  if($updateToken === false)
    exit(ErrorCode::E_CREATE_TOKEN);
//  获取当前数据库信息userinfo信息
  $userinfo = $dbh->selectCompanyUserInfo($id);
  $res = $rd->ReturnUserinfo($userinfo, $token);
  print_r($res);
  
  
  
  
  
    
  
  
  

?>