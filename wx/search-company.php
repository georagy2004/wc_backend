<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();
  $rd = new ReturnData();
  
  $getData = $_GET;
  if($getData['str'] == null)
    exit(ErrorCode::WX_PARAMETER_ERROR);
    
  $getData = $_GET;
  if($getData['limit'] == null)
    exit(ErrorCode::WX_PAGE_NULL);
    
  $sql = "SELECT info.company_user_name AS company_name, 
          info.company_profile_photo AS head_picture,
          info.company_info_display AS certified, 
          info.company_foreign_key AS company_id, 
          info.company_project_tab AS company_tab, 
          group_concat(picture.company_project_picture) AS project_picture, 
          group_concat(picture.company_project_id) AS project_id
          
          FROM (SELECT * FROM ggxcx_company_info 
          WHERE company_user_name LIKE '%{$getData['str']}%' OR company_project LIKE '%{$getData['str']}%' LIMIT {$getData['limit']},10)AS info
          
          INNER JOIN
          (SELECT mya.company_project_id, 
                  mya.company_project_picture, 
                  myb.company_project_foreign_key 
          FROM ggxcx_company_project AS mya 
          LEFT JOIN ggxcx_company_project AS myb
          ON mya.company_project_foreign_key = myb.company_project_foreign_key 
          AND mya.company_project_picture <= myb.company_project_picture
          group by mya.company_project_id
          having count(myb.company_project_picture)<=3
          )picture
          on info.company_info_id = picture.company_project_foreign_key 
          group by info.company_info_id ";
  
  $userinfo = $dbh->selectFunc($sql);
  
  for($i=0; $i<count($userinfo); $i++){
    if( is_array(json_decode($userinfo[$i]['company_tab'],true) )){
      $userinfo[$i]['company_tab'] = json_decode($userinfo[$i]['company_tab'],true);
    }else{
      $userinfo[$i]['company_tab'] = [];
    }
    $userinfo[$i]['project_picture'] = explode(",",urldecode($userinfo[$i]['project_picture']));
    $userinfo[$i]['project_id'] = explode(",",$userinfo[$i]['project_id']);
    $userinfo[$i]['project'] = array_combine($userinfo[$i]['project_id'],$userinfo[$i]['project_picture']);
    unset($userinfo[$i]['project_picture'], $userinfo[$i]['project_id']);
  }
  $userinfo = json_encode($userinfo);
  
  print_r($userinfo);
  
  

?>
