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
  $sql = "SELECT token FROM wx_user_token WHERE wx_user_id  = {$id}";
  $validateToken = $dbh->selectFunc($sql);
  $validateToken = $validateToken[0]['token'];
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::WX_TOKEN_ERROR);
  
  $sql = "SELECT company_id FROM wx_user_attention WHERE wx_user_id = {$id}";
  $attention = $dbh->selectFunc($sql);
  if($attention === false)
    exit(ErrorCode::WX_SELECT_ERROR);
  
  $attention = json_decode($attention[0]['company_id']);
  $attention = implode(",", array_filter($attention));
  $sql = "SELECT company_name, head_picture, certified, company_id, company_tab, tel FROM ((
          SELECT company_name AS company_name, 
          profile_photo AS head_picture,
          is_display AS certified, 
          adv_company_user_id AS company_id, 
          company_tab AS company_tab,
          adv_company_user_id AS id
          FROM adv_company_info 
          WHERE adv_company_user_id in ({$attention}))
          AS info 
          LEFT JOIN (SELECT tel AS tel, 
          adv_company_user_id 
          FROM adv_company_tel WHERE adv_company_user_id in ({$attention})) AS tel
          ON (info.id = tel.adv_company_user_id)
          )";
  
  $res = $dbh->selectFunc($sql);
  $res = json_encode($res);
  print_r($res);
  
?>
