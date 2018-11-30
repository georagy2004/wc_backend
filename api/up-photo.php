<?php
require_once __DIR__ . '/IncludeAll.php';
header('Access-Control-Allow-Origin:*');
$dbh = PdoConnect::getInstance($dsn, $user, $pswd);
$jwt = new Jwt();
$rd = new ReturnData();
$gd = new GD();

  $postData = $_POST;
  $img = $_FILES['picture'];
  
  // 验证token信息是否正确，并返回token参数
  $tokenInfo = $jwt->verifyToken($postData['token']);
  if($tokenInfo === false)
    exit(ErrorCode::E_LOGIN_TIMEOUT);
    
  // 对比数据库token，查看是否重复登陆
  $id = $tokenInfo['id'];
  $validateToken = $dbh->validateToken($postData['token'], $id);
  if($postData['token'] !== $validateToken)
    exit(ErrorCode::E_VALIDATION_FAILS);
  
  // 获取图片
  if($img == null)
    exit(ErrorCode::E_PICTURE_NULL);
    
  // 检测格式
  foreach($img['type'] as $value){
    if($value != "image/jpeg" && $value != "image/png")
    exit(ErrorCode::E_PICTURE_FORMAT);
  }
  
  // 创建目录
  $dirName = __DIR__ ."/../images/xx_{$id}/project_picture/";
  if(!is_dir($dirName))
		mkdir($dirName, 0777, true);
  
  $data = [];
  foreach($img['tmp_name'] as $key => $value){
    $fileFormat = explode( ".", $img["name"][$key] );
    $formatRes = end( $fileFormat );
    $random = Encryption::randomKeys(6);
    $filename = time() . $random . '.' .  $formatRes;
      if($img['type'][$key] == "image/jpeg"){
        $im = imagecreatefromjpeg($value);
        if($gd->resizeImage( $im, 700, 2000, $dirName . $filename )){
          $data[$key] = $dbh::url . "images/xx_{$id}/project_picture/".$filename;
        }else{
          exit(ErrorCode::E_SAVE_PICTURE);
        }
      }else if($img['type'][$key] == "image/png"){
        $im = imagecreatefrompng($value);
        if($gd->resizeImage( $im, 700, 2000, $dirName . $filename )){
          $data[$key] = $dbh::url . "images/xx_{$id}/project_picture/".$filename;
        }else{
          exit(ErrorCode::E_SAVE_PICTURE);
        }
      }else{
        exit(ErrorCode::E_PICTURE_FORMAT);
      }
  }
  $dataRes['errno'] = 0;
  $dataRes['data'] = $data;
  print_r(json_encode($dataRes));


?>
