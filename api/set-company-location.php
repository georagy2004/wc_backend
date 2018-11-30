<?php
require_once __DIR__ . '/IncludeAll.php';
header('Access-Control-Allow-Origin:*');
$dbh = PdoConnect::getInstance($dsn, $user, $pswd);
$jwt = new Jwt();
$rd = new ReturnData();
  
  
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
    
  //存储到数据库
  $sql = "INSERT INTO adv_company_position 
          (name, longitude, latitude, adv_company_user_id) 
          VALUES 
          (:name, :longitude, :latitude,:id)";
  $params = array(':name' => $postData['name'], 
                  ':longitude' => 'asdasdasd', 
                  ':latitude' => $postData['latitude'],
                  ':id' => $id);
  $res = $dbh->generalUpdate($sql, $params);
  if($res != true)
    exit(ErrorCode::E_ADD_POSITION);

  print_r($res);







?>
