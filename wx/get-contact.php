<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  header('Content-Type:application/json');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();
  $rd = new ReturnData();
  
  $getData = $_GET;
//  $sql = "SELECT company_contact_tel AS phone, company_contact_adress AS adress, company_contact_location AS map 
//          FROM ggxcx_company_contact WHERE company_contact_foreign_key = {$getData['id']}";
  $contact = $dbh->selectContact($getData['id']);
//  var_dump($userTel);
//  $contactData = $contact[0];
//  foreach($contactData as $key => $value){
//    $contactData[$key] = json_decode($value);
//  }
//  
//  $res = json_encode($contactData);
  print_r(json_encode($contact));



?>