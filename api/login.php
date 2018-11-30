<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();
  $rd = new ReturnData();  

  // 获取POST信息，查询账户，拆分账户密码
  $loginInfo = $_POST;
  $selectUser = $dbh->selectCompanyLoginInfo($loginInfo['account']);
  if($selectUser != false){
    $id = $selectUser['id'];
    $account = $selectUser['account'];
    $password = $selectUser['password'];
    // 获取密码的密钥，并再次加密
    $passwordKey = $enc->decryptionPassword ($password);
    $endPassword = $enc->passwordEncryption($loginInfo['password'], $passwordKey);
  }else{
    exit(ErrorCode::E_ACCOUNT_PASSWORD);
  }
  
  // 如果密码正确则发放token
  if($password === $endPassword){
    $userInfo = $dbh->selectCompanyUserInfo($id);
    if($userInfo !== false){
      $pl = [
      'iss'=>'jwt_admin', //该JWT的签发者
      'iat'=>time(), //签发时间
      'exp'=>time()+7200, //过期时间
      'nbf'=>time(), //该时间之前不接收处理该Token
      'sub'=>'dobeboy.net', //面向的用户
      'jti'=>md5(uniqid('JWT').time()), //该Token唯一标识
      'id'=>$id,
      ];
      $jwt = Jwt::getToken($pl);
      $updateToken = $dbh->updateToken($jwt, $id);
      if($updateToken === false)
      exit(ErrorCode::E_CREATE_TOKEN);
    //  $verify1 = Jwt::verifyToken($jwtt);
    //  var_dump($verify1);
    // 处理查询结果，返回相应字段
      $returnData = $rd->ReturnUserinfo($userInfo, $jwt);
      print_r($returnData);
    }else{
      exit(ErrorCode::E_SELECT_USERINFO);
    }

  }else{
    exit(ErrorCode::E_PASSWORD);
  };

  
  


?>
