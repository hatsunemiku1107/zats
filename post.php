<?php
require('lib.php');
//渡される値
	$data = array(
			'TID'	=> (int)$_POST['TID'],//スレッドID
			'NAME'	=> $_POST['NAME'],//投稿者名
			'TEXT'	=> $_POST['TEXT'],//投稿内容
			'EMAIL'=> $_POST['EMAIL'],//email
	);
require('config.php');
new Post($data,$dbname,$host,$user,$password);
?>