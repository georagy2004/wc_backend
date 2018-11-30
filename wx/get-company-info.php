<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  
  $getData = $_GET;
  if($getData['id'] == null)
    exit(ErrorCode::WX_PARAMETER_ERROR);
  
  $sql = "SELECT 
          profile_photo AS picture, 
          company_name AS name,
          is_display AS certified,
          introduction,
          main_project AS project,
          adress,
          UNIX_TIMESTAMP(create_time) AS time
          FROM adv_company_info 
          WHERE adv_company_user_id = {$getData['id']}";
          
  $res = $dbh->selectFunc($sql);
  if(count($res) != 1)
    exit(ErrorCode::WX_SELECT_ERROR);
    
  $res = $res[0];
  $info = [
          ['title' => '公司简介', 'text' => $res['introduction']],
          ['title' => '主营项目', 'text' => $res['project']],
          ['title' => '公司地址', 'text' => $res['adress']],
          ['title' => '入驻时间', 'text' => date("Y年m月d日", $res['time'])],
  ];
  
  $res['info'] = $info;
  unset($res['introduction'], $res['project'], $res['adress'], $res['time']);
  $res = json_encode($res);
  print_r($res);
  
  

?>
