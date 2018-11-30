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
  $id = $tokenInfo['id']; //获取ID
  $validateToken = $dbh->validateToken($postData['token'], $id);
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::E_VALIDATION_FAILS);
  
  // 设置文件输出目录
  $dirName = __DIR__ ."/../images/xx_{$id}/profile_picture/";
  if(!is_dir($dirName))
		mkdir($dirName, 0777, true);
  $random = Encryption::randomKeys(6);
  $fileName = $id . '_' . $random . '_' . time() .'.jpg';
  $path = $dirName . $fileName;
  
  // 判断base64的格式
  // print_r($postData['profile_photo']);
  if(strpos($postData['profile_photo'], 'data:image/png;base64,') !== false){
    $imgType = 'data:image/png;base64,';
  }else if(strpos($postData['profile_photo'], 'data:image/jpg;base64,') !== false){
    $imgType = 'data:image/jpg;base64,';
  }else{
    exit(ErrorCode::E_DATA_ERROR);
  }
  
  // 输出图片到本地，并更新图片
  $baseImg = str_replace($imgType, '', $postData['profile_photo']);
  $res = file_put_contents($path, base64_decode($baseImg));
  $adress = $dbh::url;
  $url = $adress . "images/xx_{$id}/profile_picture/{$fileName}";
  $setProfilePhoto = $dbh->setUserProfilePhoto ($url, $id);
  if($setProfilePhoto === false)
    exit(ErrorCode::E_PROFILE_PICTURE);
    
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
    exit(ErrorCode::E_REFRESH_TOKEN);
    
  // 获取当前数据库信息userinfo信息
  $userinfo = $dbh->selectCompanyUserInfo($id);
  $res = $rd->ReturnUserinfo($userinfo, $token);
  print_r($res);
  
?>
