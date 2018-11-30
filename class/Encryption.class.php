<?php
final class Encryption {
  // 生成密钥
  public static function randomKeys( $length ) {
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; //字符池
    for ($i=0;$i<$length;$i++) {
      $key .= $pattern{ mt_rand(0,61) }; //生成php随机数
    }
    return $key;
  }
  
  // 密码加密
  public function passwordEncryption($str, $key) {
    $password = hash_hmac('sha256', $str, $key) . '&' . $key;
    return $password;
  }
  
  // 密码解密（获取密钥，然后再次加密来验证密码）
  public function decryptionPassword (string $str) {
    $pieces = explode("&", $str);
    return $pieces[1];
  }
};

//$enc = new Encryption();



?>