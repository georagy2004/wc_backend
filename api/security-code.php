<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
	$dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  
//  生成验证码并入库打印---------------------------------------
  $postData = $_POST;
  $str = Encryption::randomKeys(6);
  $sql = "INSERT INTO adv_register_code (mobile, code) 
          VALUES (:mobile, :code)
          ON DUPLICATE KEY 
          UPDATE code = :str";
          
  $params = array(':mobile' => $postData['mobile'], ':code' => $str, ':str' => $str);
  $key = $dbh->generalUpdate($sql, $params);
  var_dump($key);
  echo $str;
  
?>