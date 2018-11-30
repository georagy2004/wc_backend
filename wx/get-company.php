<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  
  $getData = $_GET;
  if($getData['id'] == null)
    exit(ErrorCode::WX_PARAMETER_ERROR);
  
  $sql = "SELECT company_name, profile_photo AS head_picture,
          is_display AS certified, adv_company_user_id AS company_id, company_tab
          FROM adv_company_info WHERE adv_company_user_id = {$getData['id']}";
  $userinfo = $dbh->selectFunc($sql);
  if(count($userinfo) != 1)
    exit(ErrorCode::WX_SELECT_ERROR);
  if( is_array(json_decode($userinfo[0]['company_tab'],true) )){
    $userinfo[0]['company_tab'] = json_decode($userinfo[0]['company_tab'],true);
  }else{
    $userinfo[0]['company_tab'] = [];
  }
  $userinfo = json_encode($userinfo[0]);
  
  print_r($userinfo);
  
  

?>
