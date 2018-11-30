<?php
session_start();
	if(empty($_SESSION['user']) || empty($_SESSION['id'])){
		echo '您还没有登陆，请登陆后在试';
		echo '<meta http-equiv="Refresh" content="1;url=login.php" />';
		exit;
	}
require('./inc/config.inc.php');
require('./inc/PdoConnect.class.php');
	$obj = PdoConnect::getInstance($dsn, $user, $pswd);
?>
</body>
</html>