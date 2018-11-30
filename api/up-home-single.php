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
  $cover = $_FILES['cover'];
  
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
  $dirName = __DIR__ ."/../images/xx_{$id}/home_single/{$postData['title']}/";
  if(!is_dir($dirName))
		mkdir($dirName, 0777, true);
  
  if($cover['name'] == null)
    exit(ErrorCode::E_UPLOAD_COVER);
  // 获取图片后缀, 创建图片名称
  $fileFormat = explode( ".", $cover['name'] );
  $formatRes = end( $fileFormat );
  $random = Encryption::randomKeys(6);
  //*** PNG会转换成JPG，所以后缀直接变成jpg
  $filename = time() . $random . '.' .  'jpg';
  
  // 判断图片类型
    if($cover['type'] == "image/jpeg"){
      $im = imagecreatefromjpeg($cover['tmp_name']);
      if($gd->resizeImage( $im, 500, 800, $dirName . $filename )){
        $postData['picture'] = $dbh::url . "images/xx_{$id}/home_single/{$postData['title']}/".$filename;
        $postData['picture'] = urlencode($postData['picture']);
      }else{
        exit(ErrorCode::E_UPLOAD_PICTURE);
      }
    }else if($cover['type'] == "image/png"){
      $im = imagecreatefrompng($value);
      if($gd->resizeImage( $im, 500, 800, $dirName . $filename )){
        $postData['picture'] = $dbh::url . "images/xx_{$id}/home_single/{$postData['title']}/".$filename;
        $postData['picture'] = urlencode($postData['picture']);
      }else{
        exit(ErrorCode::E_UPLOAD_PICTURE);
      }
    }else{
      exit(ErrorCode::E_PICTURE_FORMAT);
    }
    
  // 对富文本进行XSS过滤
  $content = $purifier->purify($_POST['content']);
  
  //  添加到数据库
  $sql = "INSERT INTO adv_company_home_single
         (title, cover, content, adv_company_user_id) 
         VALUES
         (:title, :cover, :content, :adv_company_user_id)";
  $params = array(':title' => $postData['title'], ':cover' => $postData['picture'], ':content' => $content, ':adv_company_user_id' => $id);
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
