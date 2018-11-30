<?php
  require_once __DIR__ . '/IncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();

//   验证$post长度, 字符串验证
  function get_register_info () {
    $data = $_POST;
    $postData = array();
    if (count($data) === 6) {
      foreach( $data as $key => $value ){
        if($key == 'company'){
          $str = Validation::nameValidation($value);  //验证企业名
          if($str !== 1){
            exit(ErrorCode::E_COMPANY_NAME);
          }else{
            $postData[$key] = $value;
          }
        }else{
          $str = Validation::stringValidation($value);  //验证字符串
          if($str !== 1){
            exit(ErrorCode::E_VALIDATE_STRING);
          }else{
            $postData[$key] = $value;
          }
        }
      } 
    }
    // 判断2次密码是否一致
    if ($postData['pass'] === $postData['checkPass']) {
      return $postData;
    }else{
      exit(ErrorCode::E_CONFIRM_PASSWORD);
    }
  }
  
  $postData = get_register_info();
  if(!is_array($postData)){
    exit(ErrorCode::E_PARAMS_ERROR);
  }

  $sql = "SELECT mobile, code FROM adv_register_code WHERE mobile = :mobile AND code = :code";
  $params = array( 'mobile' => $postData['mobile'], 'code' => $postData['vcode']);
  $validationSecurityCode = $dbh->generalSelect($sql, $params);

  if(count($validationSecurityCode) !== 1)
    exit(ErrorCode::E_SECURITY_CODE);
    
  $key = $enc->randomKeys(6);
  $encryptedPassword = $enc->passwordEncryption($postData['pass'], $key);
  $res = $dbh->userRegistration($postData['account'], $encryptedPassword , $postData['mobile'], $postData['company']);
  if($res !== true)
    exit(ErrorCode::E_REGISTER_FAILS);
    
  $remove = $dbh->removeSecurityCode($postData['mobile'], $postData['vcode'] );
  if($remove !== true)
    exit(ErrorCode::E_DELETE_CODE);
    
  echo '1';
  
?>
