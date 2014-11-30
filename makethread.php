<?php
require('lib.php');
$data = array(
	'TID'		=>'1',
	'NAME'	=> $_POST['NAME'],//投稿者名
	'TEXT'	=> $_POST['TEXT'],//投稿内容
	'EMAIL'=> $_POST['EMAIL'],//email

	'TITLE'	=>$_POST['TITLE'],
	'T_MAKEDATE'=>date("Y-m-d H:i:s",time()),
);

require('config.php');
new MakeTHREAD($data,$dbname,$host,$user,$password);
