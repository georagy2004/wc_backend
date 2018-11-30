<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  
  $getData = $_GET;
  if($getData['id'] == null)
    exit(ErrorCode::WX_ID_NULL);
  if($getData['str'] == null)
    exit(ErrorCode::WX_PARAMETER_ERROR);

  $projectSql = "SELECT 
                id AS project_id, 
                title AS title, 
                cover AS picture 
                FROM adv_company_project 
                WHERE adv_company_user_id = {$getData['id']} 
                AND title LIKE '%{$getData['str']}%'";
  $projectRes = $dbh->selectFunc($projectSql);
  if(count($projectRes) != 0){
    foreach($projectRes as $key => $value){
      $projectRes[$key]['picture'] = urldecode($projectRes[$key]['picture']);
    }
  }

  $caseSql = "SELECT 
             id AS id, 
             process_title AS process_title, 
             cover AS picture, 
             process AS process 
             FROM adv_company_case 
             WHERE adv_company_user_id = {$getData['id']} 
             AND (process_title LIKE '%{$getData['str']}%' OR title LIKE '%{$getData['str']}%')";
  $caseRes = $dbh->selectFunc($caseSql);
  if(count($caseRes) != 0){
    foreach($caseRes as $key => $value){
      $caseRes[$key]['picture'] = urldecode($caseRes[$key]['picture']);
    }
  }
  
  $res = [];
  $res['company_project'] = $projectRes;
  $res['company_case'] = $caseRes;
  
  
  $res = json_encode($res);
  print_r($res);
  
  
?>
