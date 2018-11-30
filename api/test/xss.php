<?php
require_once '../IncludeAll.php';
header('Access-Control-Allow-Origin:*');
$dbh = PdoConnect::getInstance($dsn, $user, $pswd);
$jwt = new Jwt();
$gd = new GD();
$rd = new ReturnData();

//Xss过滤
$config = HTMLPurifier_Config::createDefault();
//$config->set('HTML.Allowed', 'img[src|alt|style],span[style]');
//$config->set('HTML.AllowedAttributes', array('style' => TRUE), false);

print_r($config)



?>
