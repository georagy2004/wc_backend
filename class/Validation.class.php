<?php
final class Validation {
  //  字符串验证，只允许大写、小写字母,数字及下划线
  public static function stringValidation( string $str) {
    $pattern = '/^[a-zA-Z0-9_]+$/';
    $res = preg_match( $pattern, $str, $match );
    return $res;  //返回1或0
  }
  
  // 字符串验证。只允许大写、小写字母,数字及下划线和中文
  public function nameValidation( string $str) {
    $pattern = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]+$/u';
    $res = preg_match( $pattern, $str, $match );
    return $res;  //返回1或0
  }
  
}
//
//$test = new Validation();

?>