<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $enc = new Encryption();
  $rd = new ReturnData();
  
  $getData = $_GET;
  if($getData['limit'] == null)
    exit(ErrorCode::WX_PAGE_NULL);
    
  $sql = "select 
          info.company_name AS company_name, 
          info.profile_photo AS head_picture,
          info.is_display AS certified, 
          info.adv_company_user_id AS company_id, 
          info.company_tab AS company_tab, 
          group_concat(picture.cover) AS project_picture, 
          group_concat(picture.id) AS project_id
          from 
          (SELECT * FROM adv_company_info LIMIT {$getData['limit']},10) AS info
          inner join
          (select mya.id, 
          mya.cover, 
          myb.adv_company_user_id 
          from 
          adv_company_project AS mya left join adv_company_project AS myb
          on mya.adv_company_user_id = myb.adv_company_user_id 
          and mya.cover <= myb.cover
          group by mya.id
          having count(myb.cover)<=3
          )picture
          on info.id = picture.adv_company_user_id 
          group by info.id";
  
//  $sql = "SELECT company_user_name AS company_name, company_profile_photo AS head_picture,
//          company_info_display AS certified, company_foreign_key AS company_id, company_project_tab AS company_tab
//          FROM ggxcx_company_info LIMIT {$getData['limit']},4";
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
