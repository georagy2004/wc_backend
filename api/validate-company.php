<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  error_reporting(0);
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);

  $company = $_POST['company'];
  if(!empty($company)){
    $sql = 'SELECT company_name FROM adv_company_info WHERE company_name = :company';
    $params = array(':company' => $company);
    $res = $dbh->generalSelect($sql, $params);
    print_r(count($res));
  }else{
    exit(ErrorCode::E_PARAMS_ERROR);
  }
 

?>