<?php
require_once __DIR__ . '/IncludeAll.php';
header('Access-Control-Allow-Origin:*');
$dbh = PdoConnect::getInstance($dsn, $user, $pswd);
$jwt = new Jwt();
$gd = new GD();
$rd = new ReturnData();

//Xss过滤
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

  $postData = $_POST;
  $cover = $_FILES;
  
  // 验证token信息是否正确，并返回token参数
  $tokenInfo = $jwt->verifyToken($postData['token']);
  if($tokenInfo === false)
    exit(ErrorCode::E_LOGIN_TIMEOUT);
    
  // 对比数据库token，查看是否重复登陆
  $id = $tokenInfo['id'];
  $validateToken = $dbh->validateToken($postData['token'], $id);
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::E_VALIDATION_FAILS);
  
  // 创建目录
  $dirName = __DIR__ ."/../images/xx_{$id}/home_double/{$postData['title']}/";
  if(!is_dir($dirName))
		mkdir($dirName, 0777, true);
    
    
  
  foreach($cover as $key => $value){
  if($cover[$key] == null)
    exit(ErrorCode::E_UPLOAD_COVER);
  // 获取图片后缀, 创建图片名称
  $fileFormat = explode( ".", $cover[$key]['name'] );
  $formatRes = end( $fileFormat );
  $random = Encryption::randomKeys(6);
  //*** PNG会转换成JPG，所以后缀直接变成jpg
  $filename = time() . $random . '.' .  'jpg';
  
  // 判断图片类型
    if($cover[$key]['type'] == "image/jpeg"){
      $im = imagecreatefromjpeg($cover[$key]['tmp_name']);
      if($gd->resizeImage( $im, 500, 800, $dirName . $filename )){
        $postData[$key] = $dbh::url . "images/xx_{$id}/home_double/".$filename;
        $postData[$key] = urlencode($postData[$key]);
      }else{
        exit(ErrorCode::E_UPLOAD_PICTURE);
      }
    }else if($cover[$key]['type'] == "image/png"){
      $im = imagecreatefrompng($cover[$key]['tmp_name']);
      if($gd->resizeImage( $im, 500, 800, $dirName . $filename )){
        $postData[$key] = $dbh::url . "images/xx_{$id}/home_double/".$filename;
        $postData[$key] = urlencode($postData[$key]);
      }else{
        exit(ErrorCode::E_UPLOAD_PICTURE);
      }
    }else{
      exit(ErrorCode::E_PICTURE_FORMAT);
    }
  }
    
  // 对富文本进行XSS过滤
  $left_content = $purifier->purify($postData['left_editor']);
  $right_content = $purifier->purify($postData['right_editor']);
  
  //  添加到数据库
  $sql = "INSERT INTO adv_company_home_double
         (left_title, left_cover, left_content, 
          right_title, right_cover, right_content,
          adv_company_user_id) 
         VALUES
         (:left_title, :left_cover, :left_content, 
          :right_title, :right_cover, :right_content,
          :id)";
  $params = array(':left_title' => $postData['left_title'],
                  ':left_cover' => $postData['left_cover'],
                  ':left_content' => $left_content,
                  ':right_title' => $postData['right_title'],
                  ':right_cover' => $postData['right_cover'],
                  ':right_content' => $right_content,
                  ':id' => $id);
  $res = $dbh->generalUpdate($sql, $params);
  
  if($res === false)
    exit(ErrorCode::E_UPLOAD_DATA);
  
//  更新token
  $pl = [
    'iss'=>'jwt_admin', //该JWT的签发者
    'iat'=>time(), //签发时间
    'exp'=>time()+7200, //过期时间
    'nbf'=>time(), //该时间之前不接收处理该Token
    'sub'=>'dobeboy.net', //面向的用户
    'jti'=>md5(uniqid('JWT').time()), //该Token唯一标识
    'id'=>$id,
    ];
  $token = Jwt::getToken($pl);
  $updateToken = $dbh->updateToken($token, $id);
  if($updateToken === false)
    exit(ErrorCode::E_REFRESH_TOKEN);
//  获取当前数据库信息userinfo信息
  $userinfo = $dbh->selectCompanyUserInfo($id);
  $res = $rd->ReturnUserinfo($userinfo, $token);
  print_r($res);

  
  
?>
