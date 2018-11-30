<?php
require_once __DIR__ . '/api/IncludeAll.php';
header('Access-Control-Allow-Origin:*');
$dbh = PdoConnect::getInstance($dsn, $user, $pswd);

//  $res = $dbh->validationSecurityCode ( 'HuQIfA' );
//  echo $res;
//  $res2 = $dbh->removeSecurityCode( 'HuQIfA' );
//  echo $res2;
//  
  

//echo hash_hmac('sha256', 'The quick brown fox jumped over the lazy dog.', 'secret');

//  $enc = new Encryption();
//  $code = $enc->randomKeys( 6 );
//  $res3 = $dbh->getVerification( $code );
//  print_r($res3)

if(function_exists ( 'openssl_pkey_new' )){
  echo '1';
}else{
  echo '00';
}
?>