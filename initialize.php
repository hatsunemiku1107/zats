<?php
//初期設定
	require('lib.php');
	require('config.php');

	echo <<<EOM
<!DOCUTYPE html>
<html>
	<head>
		<meta charset=\"UTF-8\">
		<link rel=\"stylesheet\" href=\"css.css\">
		<link rel=\"text/javascript\" href=\"js.js\">
		<title>DB初期設定</title>
	</head>
	<body>
EOM;

	$db = mysqli_connect($host,$user,$password);
// Check connection
	if (mysqli_connect_errno())echo "Failed to connect to MySQL: " . mysqli_connect_error();
// Create database
	$sql = array(
		"CREATE DATABASE IF NOT EXISTS ".$dbname." DEFAULT COLLATE utf8mb4_general_ci;",
		);
	foreach ($sql as $sql){
		if (!mysqli_query($db,$sql)){
			echo "Error creating database: " . mysqli_error($db);
		}
	}unset($sql);
	$data = array('dummy','dummy');
	new Init($data,$dbname,$host,$user,$password);
	echo <<<EOM
		<h3>テストスレッドを作成します。</h3>
		<form method="POST"action="makethread.php"
			<input type="hidden" name="TID"value="1">
			<input type="hidden" name="NAME" value="">
			<input type="hidden" name="TEXT" value="これはテストです">
			<input type="hidden" name="EMAIL" value="">
			<input type="hidden" name="TITLE" value="テスト">
			<input type="submit" value="続けるにはここをクリックしてください。">
		</form>
	</body>
</html>
EOM;

?>