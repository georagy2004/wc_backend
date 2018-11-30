<?php
class ReturnData {
//处理数据库的查询结果(user_info表内的所有数据)，并输出
  public function ReturnUserinfo( $userInfo, $token ){
    if(is_array($userInfo)){
//      $info['id'] = $id;
      $info['name'] = $userInfo['company_name'];
      $info['profile_photo'] = $userInfo['profile_photo'];
      $info['project'] = $userInfo['main_project'];
      $info['adress'] = $userInfo['adress'];
      if( is_array(json_decode($userInfo['company_tab'],true) )){
        $info['type'] = json_decode($userInfo['company_tab'],true);
      }else{
        $info['type'] = [];
      }

      $info['introduction'] = $userInfo['introduction'];
      $info['registration_date'] = $userInfo['create_time'];

      $res['token'] = $token;
      $res['userinfo'] = $info;
      $returnObject = json_encode($res);
      return ($returnObject);
    }else{
      return "处理结果失败";
    }
  }

}





?>