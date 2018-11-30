<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  
  $getData = $_GET;
  if($getData['id'] == null)
    exit(ErrorCode::WX_ID_NULL);
    
  $sql = "SELECT id AS project_id, title, cover AS picture 
          FROM adv_company_project WHERE adv_company_user_id = :id";
  
  $params = array(':id' => $getData['id']);
  $res = $dbh->generalSelect($sql, $params);
  if(count($res) != 0){
    foreach($res as $key => $value){
      $res[$key]['picture'] = urldecode($res[$key]['picture']);
    }
  }
  
//  $userinfo = $dbh->selectFunc($sql);
//  if(count($userinfo) != 1)
//    exit('查询错误');
//  if( is_array(json_decode($userinfo[0]['company_tab'],true) )){
//    $userinfo[0]['company_tab'] = json_decode($userinfo[0]['company_tab'],true);
//  }else{
//    $userinfo[0]['company_tab'] = [];
//  }
//  $userinfo = json_encode($userinfo[0]);
  $res = json_encode($res);
  
  print_r($res);
  
  
  
    