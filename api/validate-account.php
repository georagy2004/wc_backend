<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  error_reporting(0);
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);

  $account = $_POST['account'];
  if(!empty($account)){
    $sql = 'SELECT account FROM adv_company_user WHERE account = :account';
    $params = array(':account' => $account);
    $res = $dbh->generalSelect($sql, $params);
    print_r(count($res));
  }else{
    exit(ErrorCode::E_PARAMS_ERROR);
  }

?>