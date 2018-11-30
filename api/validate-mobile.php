<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  error_reporting(0);
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);

  $mobile = $_POST['mobile'];
  if(!empty($mobile)){
    $sql = 'SELECT mobile FROM adv_company_user WHERE mobile = :mobile';
    $params = array(':mobile' => $mobile);
    $res = $dbh->generalSelect($sql, $params);
    print_r(count($res));
  }else{
    exit(ErrorCode::E_PARAMS_ERROR);
  }
 

?>