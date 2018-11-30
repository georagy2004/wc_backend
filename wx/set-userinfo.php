<?php
  require_once 'wxIncludeAll.php';
  header('Access-Control-Allow-Origin:*');
  $dbh = PdoConnect::getInstance($dsn, $user, $pswd);
  $jwt = new Jwt();

  $getData = $_GET;
  if($getData['code'] == null)
  exit(ErrorCode::WX_PARAMETER_ERROR);
  
  $appid = 'wxfbd55caafb2163f1';
  $secret = '5df4b6d5faaa702663a99e3bb0ef05eb';
  $js_code = $getData['code'];
  
  $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$js_code}&grant_type=authorization_code";
  $url = urldecode($url);
  
  // 发送请求到微信服务器
  $curl = curl_init(); // 启动一个CURL会话
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HEADER, 0);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
  $curlRes = curl_exec($curl);     //返回api的json对象
  curl_close($curl);

  // 接收服务器数据，验证结果后添加到数据库
  $curlRes = json_decode($curlRes, true);
  if($curlRes['session_key'] && $curlRes['openid']){
    $sql = "INSERT INTO wx_user(openid, session, profile_photo, name) 
            VALUE('{$curlRes['openid']}', '{$curlRes['session_key']}', '{$getData['picture']}', '{$getData['name']}') 
            ON DUPLICATE KEY UPDATE 
            openid = '{$curlRes['openid']}', 
            session = '{$curlRes['session_key']}',
            profile_photo = '{$getData['picture']}',
            name = '{$getData['name']}'";
            
    $res = $dbh->wxUpdateFunc($sql);
  }
  
  if(($res !== true) && ($res != 00000))
    exit(ErrorCode::WX_LOGIN_FAILED);
  
  $sql = "SELECT id FROM wx_user WHERE openid = '{$curlRes['openid']}'";
  $res = $dbh->selectFunc($sql);
  if(count($res) == 0)
    exit(ErrorCode::WX_SELECT_ACCOUNT_FAILED);
  $id = $res[0]['id'];
  
  $pl = [
  'iss'=>'jwt_admin', //该JWT的签发者
  'iat'=>time(), //签发时间
  'exp'=>time()+ (86400 * 7), //过期时间
  'nbf'=>time(), //该时间之前不接收处理该Token
  'sub'=>'dobeboy.net', //面向的用户
  'jti'=>md5(uniqid('JWT').time()), //该Token唯一标识
  'id'=>$id,
  ];
//  
  $token = Jwt::getToken($pl);
  if($token === false)
    exit(ErrorCode::WX_CREAT_TOKEN_ERROR);
    
  $sql = "INSERT INTO wx_user_token (token, wx_user_id) 
          VALUES ('{$token}', {$id})
          ON DUPLICATE KEY 
          UPDATE token = '{$token}'";
  $res = $dbh->wxUpdateFunc($sql);
  
  if(($res !== true) && ($res != 00000))
    exit(ErrorCode::WX_UPDATE_TOKEN_FAILED);
    
  $attentionSql = "SELECT company_id FROM wx_user_attention WHERE wx_user_id = {$id}";
  $attentionRes = $dbh->selectFunc($attentionSql);
  if($attentionRes === false)
    exit(ErrorCode::WX_SELECT_ATTENTION_FAILED);
  $attentionRes = json_decode($attentionRes[0]['user_info_attention']);
  $attentionRes = array_values(array_filter($attentionRes));
  if($attentionRes == null)
    $attentionRes = [];
    
  
  
  $result = [];
  $result['token'] = $token;
  $result['profile_photo'] = $getData['picture'];
  $result['username'] = $getData['name'];
  $result['attention'] = $attentionRes;
  $result = json_encode($result);
  print_r($result); //返回json对象
  

?>
