<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
//  error_reporting(0);
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
    
  $sql = "SELECT 
          id AS id,
          cover AS cover,
          process_title AS process_title,
          process AS process
          FROM 
          adv_company_case 
          WHERE 
          adv_company_user_id = :id";
          
  $params = array(':id' => $id);

  $res = $dbh->generalSelect($sql, $params);
  if($res === false)
    exit(ErrorCode::E_SELECT_ERROR);
  
  foreach($res as $key => $value){
    $res[$key]['cover'] = urldecode($res[$key]['cover']);
    if(is_array(json_decode($res[$key]['process']))){
    $res[$key]['process'] = json_decode($res[$key]['process']);
    }else{
      $res[$key]['process'] = [];
    }

  }
  
  $res = json_encode($res);
  print_r($res);
    
    
    
?>
    