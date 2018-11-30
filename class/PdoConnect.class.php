<?php
final class PdoConnect {

	private static $instance = null;
	private $pdo;
  const url = 'https://dobeboy.net/sv/';
  
  // 创建PDO
	private function __construct( $dsn, $user, $pswd ) {
		try {
			$this->$pdo = new PDO( $dsn, $user, $pswd );
      $this->$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		} catch ( PDOException $e ) {
			die( $e->getMessage() );
		}
	}

	private function __clone() {}

  // 抛出错误(暂未使用)
	private function throw_exception( $errMsg ) {
		echo "<div style=''width:80%; background-color:red; color:white; font-size:20px;>{$errMsg}</div>";
	}
  
  // 单例模式
	public static function getInstance( $dsn, $user, $pswd ) {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self( $dsn, $user, $pswd );
		}
		return ( self::$instance );
	}
  
  // 邀请码获取入库
  public function getVerification( $code ) {
    $sql = "INSERT INTO ggxcx_register_verification ( register_verification_code ) VALUES ( '{$code}' )";
    $res = $this->$pdo->exec( $sql );
    $this->getPDOError();
    if ($res == 0) {
      return '返回验证码失败';
    }else{
      return $code;
    }
  }
  
  // 验证验证码
  public function validationSecurityCode ( $securityCode ) {
    $sql = "SELECT register_verification_code FROM ggxcx_register_verification WHERE register_verification_code = '{$securityCode}'";
    $res = $this->$pdo->query($sql);
    $arr = $res->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($arr)){
      return true;
    }else{
      return false;
    }
  }
  // 删除验证码
  public function removeSecurityCode($mobile, $securityCode ) {
    $sql = "DELETE FROM adv_register_code WHERE mobile = {$mobile} AND code = '{$securityCode}'";
    $res = $this->$pdo->exec($sql);
    if($res == 1){
      return true;
    }else{
      return false;
    }
  }
  
  
  
  
  // 注册账号
  public function userRegistration ( $account, $password, $mobile, $company ) {
    try{
      $options = array(PDO::ATTR_AUTOCOMMIT, 0);
      $this->$pdo->beginTransaction();
      // 向用户表添加账号
      $accountSql = "INSERT INTO adv_company_user (account, password , mobile ) 
                     VALUES (:account, :password, :mobile)";
      $params = array(':account' => $account, ':password' => $password, ':mobile' => $mobile);
      $accountRes = $this->$pdo->prepare($accountSql);
      $accountRes->execute($params);
      
      if(count($accountRes) != 1)
        throw new PDOException('账户注册失败');
        
//    使用外键ID，向userInfo表添加企业名
      $id = $this->selectID($account);
      if($id === false)
        throw new PDOException('注册账户失败，原因：ID查询失败，不能关联用户信息');

      $companySql = "INSERT INTO adv_company_info (company_name, adv_company_user_id) 
                    VALUES (:company, :id)";
      $params = array(':company' => $company, ':id' => $id);
      $companyRes = $this->$pdo->prepare($companySql);
      $companyRes->execute($params);
      if(count($companyRes) != 1)
        throw new PDOException('企业注册异常，请检查企业用户名是否重复');

      $this->$pdo->commit();
      return true;
    }catch( PDOException $e ){
      $this->$pdo->rollBack();
      echo $e->getMessage();
      return false;
    }
  }
  
  // 查询联系方式
  public function selectContact ( $postId ) {
    try{
      $options = array(PDO::ATTR_AUTOCOMMIT, 0);
      $this->$pdo->beginTransaction();
      //通用参数
      $params = array(':postId' => $postId); 
//      获取电话
      $telSql = "SELECT name, job, tel FROM adv_company_tel WHERE adv_company_user_id = :postId";
      $telRes = $this->$pdo->prepare($telSql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $telRes->execute($params);
      if($telRes == false)
        throw new PDOException('查询电话号码失败');
      $tel = $telRes->fetchAll(PDO::FETCH_ASSOC);
//      获取地址
      $adressSql = "SELECT place_name AS name, adress FROM adv_company_adress WHERE adv_company_user_id = :postId";
      $adressRes = $this->$pdo->prepare($adressSql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $adressRes->execute($params);
      if($adressRes == false)
        throw new PDOException('查询地址失败');
      $adress = $adressRes->fetchAll(PDO::FETCH_ASSOC);
      
//      获取定位
      $locationSql = "SELECT name, longitude, latitude FROM adv_company_position WHERE adv_company_user_id = :postId";
      $locationRes = $this->$pdo->prepare($locationSql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $locationRes->execute($params);
      if($locationRes == false)
        throw new PDOException('查询地址失败');
      $location = $locationRes->fetchAll(PDO::FETCH_ASSOC);

      $result = [];
      $result['phone'] = $tel;
      $result['adress'] = $adress;
      $result['map'] = $location;

      $this->$pdo->commit();
      return $result;
    }catch( PDOException $e ){
      $this->$pdo->rollBack();
      echo $e->getMessage();
      return false;
    }
  }
  

//  注册账户时，查询当前注册时的ID，然后返回ID 用于给外键使用（外键值）
  public function selectID ($account){
    $sql = "SELECT id FROM adv_company_user WHERE account = :account";
    $params = array(':account' => $account);
    $res = $this->$pdo->prepare($sql);
    $res->execute($params);
    if($res == false)
      return false;
    
    $result = $res->fetchAll(PDO::FETCH_ASSOC);
    return $result[0]['id'];
  }
  
//查询账号是否已被注册(未提交注册时查询)
  public function selectAccount($account){
    $sql = "SELECT company_user_account FROM ggxcx_company_user WHERE company_user_account = '{$account}'";
    $select = $this->$pdo->query($sql);
    $res = $select->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($res)){
      return false;
    }else{
      return true;
    }
  }

//查询企业名是否已经被注册(未提交注册时查询)
  public function selectCompany($company){
    $sql = "SELECT company_user_name FROM ggxcx_company_info WHERE company_user_name = '{$company}'";
    $select = $this->$pdo->query($sql);
    $res = $select->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($res)){
      return false;
    }else{
      return true;
    }
  }
  
//查询用户，取出账号的记录行（登录验证使用）
  public function selectCompanyLoginInfo($account){
    $sql = "SELECT id, account, password FROM adv_company_user WHERE account = '{$account}'";
    $select = $this->$pdo->query($sql);
    $res = $select->fetchAll(PDO::FETCH_ASSOC);
    if((!empty($res)) && (count($res) == 1)){
      return $res[0];
    }else{
      return false;
    }
  }
  
//查询用户信息（返回token时附带使用）
  public function selectCompanyUserInfo ($id){
    $sql = "SELECT * FROM adv_company_info WHERE adv_company_user_id = '{$id}'";
    $select = $this->$pdo->query($sql);
    $res = $select->fetchAll(PDO::FETCH_ASSOC);
    if((!empty($res)) && (count($res) == 1)){
      return $res[0];
    }else{
      return false;
    }
  }
  
//登录时重新更新数据库token，用于验证是否重复登陆
  public function updateToken($token, $id){
    $sql = "INSERT INTO adv_company_token (token, adv_company_user_id) 
            VALUES ('{$token}', {$id})
            ON DUPLICATE KEY 
            UPDATE token = '{$token}', adv_company_user_id = {$id}";
    
    $res = $this->$pdo->exec($sql);
    $this->getPDOError();
    if($res > 1){
      return true;
    }else{
      return false;
    }
  }

//对比数据库token，检测是否是当前用户 (已经修改)
  public function validateToken ($token, $id) {
    $sql = "SELECT token FROM adv_company_token WHERE adv_company_user_id = '{$id}'";
    $select = $this->$pdo->query($sql);
    $res = $select->fetchAll(PDO::FETCH_ASSOC);
    if((!empty($res)) && (count($res) == 1)){
      return $res[0]['token'];
    }else{
      return false;
    }
  }

//更改设置用户信息
  public function updateUserinfo ($project, $adress, $project_tab, $introduction, $id) {
    $sql = "UPDATE adv_company_info SET 
            main_project = :project,
            adress = :adress,
            company_tab = :project_tab,
            introduction = :introduction
            WHERE adv_company_user_id = :id";
    $res = $this->$pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $res->execute(array(':project' => $project,
                        ':adress' => $adress,
                        ':project_tab' => $project_tab,
                        ':introduction' => $introduction,
                        ':id' => $id
                    )
                  );
    if($res == true){
      return true;
    }else{
      return false;
    }
  }

  
//  更改用户头像
  public function setUserProfilePhoto ($path, $id) {
    $sql = "UPDATE adv_company_info SET profile_photo = '{$path}' WHERE adv_company_user_id = {$id}";   
    $res = $this->$pdo->exec($sql);
    if($res == 1){
      return true;
    }else{
      return false;
    }
  }
  
//  通用查询(prepare)
  public function generalSelect($sql, $params) {
    $res = $this->$pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    //    $error1 =$this->$pdo->errorInfo();
//    print_r($error1);
    $res->execute($params);
    //    $error1 =$this->$pdo->errorInfo();
//    print_r($error1);
    if($res == false)
      return false;
    
    $result = $res->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
//通用查询(query)
  public function selectFunc( $strSql, $queryMode = 'All', $debug = false ) {
		if ( $debug === true )$this->debug( $strSql );
		$recordset = $this->$pdo->query( $strSql );
		$this->getPDOError();
    
		if ( $recordset ) {
			$recordset->setFetchMode( PDO::FETCH_ASSOC );
			if ( $queryMode == 'All' ) {
				$result = $recordset->fetchAll();
			} else if ( $queryMode == 'Row' ) {
				$result = $recordset->fetch();
			} else {
				$result = '参数错误';
			}
			return $result;
		}else{
      return false;
    }
	}
  
//通用添加
  public function generalUpdate($sql, $params, $debug = false) {
    if ( $debug === true )$this->debug( $sql );
    $res = $this->$pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//    $error1 =$this->$pdo->errorInfo();
//    print_r($error1);
    $res->execute($params);
//    $error1 =$this->$pdo->errorInfo();
//    print_r($error1);
    $count = $res->rowCount();
    if($count > 0){
      return true;
    }else{
      return false;
    }
  }


  public function updateFunc($strSql, $debug = false) {
    if ( $debug === true )$this->debug( $strSql );
    $res = $this->$pdo->exec($strSql);
    $error = $this->$pdo->errorInfo();
    if($res != 0){
      return true;
    }else{
    print_r($error);
      return false;
    }
  }
  
  //通用删除
  public function deleteFunc($strSql, $debug = false) {
    if ( $debug === true )$this->debug( $strSql );
    $res = $this->$pdo->exec($strSql);
    $error = $this->$pdo->errorInfo();
    if($res != 0){
      return true;
    }else{
    print_r($error);
      return false;
    }
  }
  
  public function generalDelete($sql, $params, $debug = false) {
    if ( $debug === true )$this->debug( $sql );
    $res = $this->$pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $res->execute($params);
    $count = $res->rowCount();
    if($count > 0){
      return true;
    }else{
      return false;
    }
  }
  
//微信通用添加更新
  public function wxUpdateFunc($strSql, $debug = false) {
    if ( $debug === true )$this->debug( $strSql );
    $res = $this->$pdo->exec($strSql);
    $error = $this->$pdo->errorCode();
    if($res != 0){
      return true;
    }else{
      return $error;
    }
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
//  				if ( $file_type == "image/jpg" || $file_type == "image/jpeg" ) {
//					$im = imagecreatefromjpeg( $uploadfile );
//				} elseif ( $file_type == "image/png" ) {
//					$im = imagecreatefrompng( $uploadfile );
//				} elseif ( $file_type == "image/gif" ) {
//					$im = imagecreatefromgif( $uploadfile );
//				}
  
  


	//插入数据---------------------------------------------------------------------------------------------


	public
	function insert( $strSql ) {
		$res = $this->pdo->exec( $strSql );
		if ( $res == 0 ) {
			echo $this->throw_exception( '添加数据库失败' );
		}
	}


	//查询数据--------------------------------------------------------------------------------------------




	public function delete( $strSql, $debug = false ) {
		if ( $debug === true )$this->debug( $strSql );
		$recordset = $this->$pdo->exec( $strSql );
		$this->getPDOError();
		if ( $recordset ) {
			echo "删除成功";
		} else {
			echo "删除失败";
		}

	}


//	更新数据--------------------------------------------------------------------------------------------

//			public function update($table, $arrayDataValue, $where = '', $debug = false){
//				$this->checkFields($table, $arrayDataValue);
//				if ($where) {
//					$strSql = '';
//				foreach ($arrayDataValue as $key => $value) {
//					$strSql .= ", `$key`='$value'";
//				}
//					$strSql = substr($strSql, 1);
//					$strSql = "UPDATE `$table` SET $strSql WHERE $where";
//				} else {
//					$strSql = "REPLACE INTO `$table` (`".implode('`,`', array_keys($arrayDataValue))."`) VALUES ('".implode("','", $arrayDataValue)."')";
//				}
//				if ($debug === true) $this->debug($strSql);
//				$result = $this->pdo->exec($strSql);
//				$this->getPDOError();
//				return $result;
//			}
    
//更新数据 -----------------------------------------
    
    public function upNewData($strSql, $debug = false){
        if ( $debug === true )$this->debug( $strSql );
        $res = $this->$pdo->exec($strSql);
        $error = $this->getPDOError();
        if($res){
            return '更改成功';
        }
        if(($res === 0) && $error){
            return '更改成功';
        }
    }
    
    
    
			

	//上传单文件

	public
	function upImg() {
		$filetype = array( "jpg", "gif", "jpeg", "png" );
		$filegs = explode( ".", $_FILES[ "img" ][ "name" ] );
		//echo '$_FILES["img"]["name"]';
		$endd = end( $filegs );

		if ( ( ( $_FILES[ "img" ][ "type" ] == "image/jpg" ) ||
				( $_FILES[ "img" ][ "type" ] == "image/gif" ) ||
				( $_FILES[ "img" ][ "type" ] == "image/jpeg" ) ||
				( $_FILES[ "img" ][ "type" ] == "image/png" ) &&
				( $_FILES[ "img" ][ "size" ] < 2000000 ) &&
				in_array( $endd, $filetype ) ) ) {

			if ( $_FILES[ "img" ][ "error" ] > 0 ) {
				echo "错误：" . $_FILES[ "img" ][ "error" ];
			} else {
				echo "文件名称：" . $_FILES[ "img" ][ "name" ] . "<br/>";
				echo "文件类型：" . $_FILES[ "img" ][ "type" ] . "<br/>";
				echo "文件大小：" . ( $_FILES[ "img" ][ "size" ] / 1024 ) . "Kb<br/>";
				echo "文件临时储存地址：" . $_FILES[ "img" ][ "tmp_name" ] . "<br/>";
				if ( file_exists( "images/" . $_FILES[ "img" ][ "name" ] ) ) {
					echo '"images/" . $_FILES["img"]["name"] . "文件已经存在"';
				} else {
					move_uploaded_file( $_FILES[ "img" ][ "tmp_name" ], "images/" . $_FILES[ "img" ][ "name" ] );
					echo "文件储存在" . "images/" . $_FILES[ "img" ][ "name" ];
					$img = "images/" . $_FILES[ "img" ][ "name" ];
				}
			}
		} else {
			echo "文件格式不正确";
		}
		$sql = "INSERT INTO test(clas, screen, img, pageimg, pagetext) VALUES ('$_POST[clas]','$_POST[screen]','$img','$img','$_POST[pagetext]')";
		return ( $sql );
	}


	//坤佑 “上传文件” 并返回SQL语句----------------------------------------------------------------------------------------------------

	public function updata() {
		//			var_dump($_FILES);
		//			var_dump($_POST);

		function delEmpty( $v ) {
			return $v != '';
		}
		//			print_r($_FILES);
		$upFileName = array_filter( $_FILES[ 'file' ][ 'name' ], delEmpty );
		//			print_r($upFileName);

		if ( count( $upFileName ) < 1 ) {
			exit( "请添加封面图片" . "<a href='index.php'>返回</a>" );
		}
		if ( $_POST[ clas ] === '请选择类别' || $_POST[ screen ] === '请选择项目' ) {
			exit( "请选择类别与项目" . "<a href='index.php'>返回</a>" );
		}
		$this->validation( $upFileName );

		$dirName = 'images/' . date( "ymd" );
		if ( !is_dir( $dirName ) ) {
			mkdir( $dirName );
		}
		$path = [];
		$upFilePath = [];
		foreach ( $upFileName as $k => $v ) {
			$newFilePath = $dirName . '/' . rand( 1, 1000 ) . time() . $v;
			//print_r($newFilePath);
			if ( is_uploaded_file( $_FILES[ 'file' ][ 'tmp_name' ][ $k ] ) ) {
				//					if(!move_uploaded_file($_FILES['file']['tmp_name'][$k], $newFilePath)){
				//						echo "上传文件失败";
				//						exit;
				//					}
				
				$uploadfile = $_FILES['file']['tmp_name'][$k];
				$file_type = $_FILES[ "file" ][ "type" ][$k];

				if ( $file_type == "image/jpg" || $file_type == "image/jpeg" ) {
					$im = imagecreatefromjpeg( $uploadfile );
				} elseif ( $file_type == "image/png" ) {
					$im = imagecreatefrompng( $uploadfile );
				} elseif ( $file_type == "image/gif" ) {
					$im = imagecreatefromgif( $uploadfile );
				}
				if ( $this->resizeImage( $im, 600, 600, $newFilePath ) ) {
				$imgpath = 'https://dobeboy.net/' . $newFilePath;
				array_push( $path, $imgpath );
				array_push( $upFilePath, $imgpath );
				}else{
					echo "压缩封面图片失败";
				}

			} else {
				echo "上传封面失败";
			}
		}
		$sql = "INSERT INTO test(title,clas,screen,img,content) VALUES ('$_POST[title]','$_POST[clas]','$_POST[screen]','$path[0]','$_POST[content]')";
		array_push( $upFilePath, $sql );
		return ( $upFilePath );
	}


	private
	function uploadFile( $ossClient, $bucket, $object, $filePath ) {
		try {
			$ossClient->uploadFile( $bucket, $object, $filePath );
		} catch ( OssException $e ) {
			printf( __FUNCTION__ . ": FAILED\n" );
			printf( $e->getMessage() . "\n" );
			return;
		}
		print( __FUNCTION__ . ": OK" . "\n" );
	}



	//格式检测-----------------------------------------------------
	private
	function validation( $fileName ) {
		$filetype = array( "jpg", "JPG", "gif", "GIF", "jpeg", "JPEG", "png", "PNG" );
		foreach ( $fileName as $k => $v ) {
			$filegs = explode( ".", $v );
			$endd = end( $filegs );
			if ( !in_array( $endd, $filetype ) ) {
				exit( "格式不正确" . "<a href='index.php'>返回</a>" );
			}

		}

	}


	//压缩图片------------------------------------------------------------

	private
	function resizeImage( $uploadfile, $maxwidth, $maxheight, $filename ) {
		//取得当前图片大小
		$width = imagesx( $uploadfile );
		$height = imagesy( $uploadfile );

		//压缩比值

		$i = 0.5;
		//生成缩略图的大小
		if ( ( $width > $maxwidth ) || ( $height > $maxheight ) ) {

			$widthratio = $maxwidth / $width;
			$heightratio = $maxheight / $height;

			if ( $widthratio < $heightratio ) {
				$ratio = $widthratio;
			} else {
				$ratio = $heightratio;
			}

			$newwidth = $width * $ratio;
			$newheight = $height * $ratio;

			if ( function_exists( "imagecopyresampled" ) ) {
				$uploaddir_resize = imagecreatetruecolor( $newwidth, $newheight );
				//echo $uploaddir_resize;

				imagecopyresampled( $uploaddir_resize, $uploadfile, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
			} else {
				$uploaddir_resize = imagecreate( $newwidth, $newheight );
				imagecopyresized( $uploaddir_resize, $uploadfile, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
			}

			ImageJpeg( $uploaddir_resize, $filename );
			ImageDestroy( $uploaddir_resize );
			return true;
		} else {
			ImageJpeg( $uploadfile, $filename );
            return true;
		}
	}







	//		
	//		
	//		public function updata(){
	//			
	//			function delEmpty($v){
	//				return $v!='';	
	//			}
	////			print_r($_FILES);
	//			$upFileName = array_filter($_FILES['file']['name'],delEmpty);
	////			print_r($upFileName);
	//
	//			if(count($upFileName) < 2){
	//				exit("请添加图片与内容图片"."<a href='index.html'>返回</a>");
	//				}
	//			$this->validation($upFileName);
	//
	//			$dirName = 'images/'.date("ymd");
	//			if(!is_dir($dirName)){
	//				mkdir($dirName);
	//			}
	//			$path=[];
	//			foreach($upFileName as $k=>$v) {
	//				$newFilePath = $dirName .'/' .rand(1,1000) .time() .$v ;
	//				//print_r($newFilePath);
	//				if(is_uploaded_file($_FILES['file']['tmp_name'][$k])){
	//					if(!move_uploaded_file($_FILES['file']['tmp_name'][$k], $newFilePath)){
	//						echo "上传文件失败";
	//						exit;
	//					}
	//			$imgpath = 'https://dobeboy.net/'.$newFilePath;
	//			array_push($path, $imgpath);
	//			
	//			}else{
	//				echo "上传失败";
	//			}
	//			}
	//			$sql = "INSERT INTO test(clas, title, screen, img, pageimg, pagetext) VALUES ('$_POST[clas]','$_POST[title]','$_POST[screen]','$path[0]','$path[1]','$_POST[pagetext]')";
	//			return($sql);
	//		}
	//		
	//		
	//		private function validation ($fileName){
	//			$filetype = array("jpg","gif","jpeg","png");
	//			foreach($fileName as $k=>$v){
	//			$filegs = explode(".", $v);
	//			$endd = end($filegs);
	//				if(!in_array($endd, $filetype)){
	//					exit("格式不正确"."<a href='index.html'>返回</a>");
	//				}
	//
	//			}
	//
	//		}





	//检查错误--------------------------------------------------------------------------------------------

	private
	function debug( $debugInfo ) {
		var_dump( $debugInfo );
		exit();
	}

	private
	function getPDOError() {
		if ( $this->$pdo->errorCode() != '00000' ) {
			$arrayError = $this->$pdo->errorInfo();
			$this->outputError( $arrayError[ 2 ] );
		}else{
          return true;  
        };
	}

	private
	function outputError( $strErrMsg ) {
		throw new Exception( 'MySQL Error: ' . $strErrMsg );
	}


} //Class end



//echo "<pre>";
//$obj = PdoConnect::getInstance($dsn, $user, $pswd);
////$obj->insert("INSERT INTO pdotest(name, age, height) VALUES ('name10', 20, 180)");
//$my=$obj->query('SELECT * FROM pdotest');
//print_r($my);

?>