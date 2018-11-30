<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $jwt = new Jwt();

  $attentionSql = "SELECT user_info_attention FROM  ggxcx_user_info WHERE user_info_foreign_key = 2";
  $attentionRes = $dbh->selectFunc($attentionSql);
  if($attentionRes === false)
    exit('查询收藏列表失败');
    
  print_r($attentionRes);
  