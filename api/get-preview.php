<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  error_reporting(0);
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
          count(home.*) AS home
          count(project.*) AS project,
          count(case.*) AS case,
          FROM 
          ggxcx_company_project AS project,
          ggxcx_company_case AS case,
          (SELECT * FROM ggxcx_company_home_double, ggxcx_company_home_single WHERE )
          WHERE 
          company_case_foreign_key = :id";
          
  $params = array(':id' => $id);

  $res = $dbh->generalSelect($sql, $params);
  if($res === false)
    exit(ErrorCode::E_SELECT_ERROR);
  
    
    
    
?>
    