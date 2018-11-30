<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
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
    
  $sql = "SELECT * FROM (
          (SELECT 
          id AS id, 
          type AS type,
          left_title AS title, 
          left_cover AS cover, 
          right_title AS right_title, 
          right_cover AS right_cover,
          sort AS sort,
          UNIX_TIMESTAMP(create_time) AS time
          FROM adv_company_home_double WHERE adv_company_user_id = $id) 
          UNION ALL 
          (SELECT 
          id, 
          type, 
          title,
          cover,
          NULL,
          NULL,
          sort,
          UNIX_TIMESTAMP(create_time)
          FROM adv_company_home_single WHERE adv_company_user_id = $id) 
          )AS res
          ORDER BY res.sort, res.time
          ";
          
  $res = $dbh->selectFunc($sql);
  
  foreach($res as $key => $value){
    $res[$key]['cover'] = urldecode($res[$key]['cover']);
    if($res[$key]['right_cover'])
      $res[$key]['right_cover'] = urldecode($res[$key]['right_cover']);
      
    $res[$key]['time'] = date("Y年m月d日", $res[$key]['time']);
    $sort[] = $res[$key]['sort'];
    $time[] = $res[$key]['time'];
  }
  array_multisort($sort, SORT_DESC, $time, SORT_ASC, $res);
  $res = json_encode($res);
  print_r($res);

  
?>
