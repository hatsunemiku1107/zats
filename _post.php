<?php
//date_default_timezone_set('Asia/Tokyo');
new settings();

/*function e ($t){//empty
	return empty($t); 
}
function h ($str){//htmlspecialchars
	return htmlspecialchars($str,ENT_QUOTES,'UTF-8'); 
}
function n ($n){//is_numeric
	return (is_numeric($n))? $n: FALSE;
}
function jump ($message){
	require('post_NG.php');
}
*/
$func = new methods();
function main(){
	//渡される値
	$data = array(
			'TID'	=> (int)$_POST['TID'],//スレッドID
			'NAME'	=> $_POST['NAME'],//投稿者名
			'TEXT'	=> $_POST['TEXT'],//投稿内容
			'EMAIL'=> $_POST['EMAIL'],//email
	);
/*	//NAMEが空の場合名無しさんに変更()
	//$default_name = '名無しさん@OKNCT';
	//if (e($data['NAME']))$data['NAME'] = $default_name;
	//EMAILが'sage'の場合''に変更(まだ実装しない)
	$data = new data($data);
	//各変数をエスケープ処理
	foreach($data as $key=>&$val){//実体参照
		if (!e($val)){
			$val = h($val);
			mb_convert_encoding($val,'UTF-8','auto');
			$val = h($val);
		}else if($key != 'EMAIL'){
			echo"不正なアクセスが検出されました。\n";
			jump($key."が入力されていません");
			exit(); //とりあえずexit
		}else{
		}
	}
	unset($val,$key);//不要変数削除
*/
	//DB設定
		$dsn = 'mysql:dbname=mydb;host=localhost';
		$user = 'root';
		$password = 'root'; 
	//DB接続
	try {
		$db = new PDO($dsn, $user, $password);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);//よくわからない。あとで調べること
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		//echo "接続に失敗:\n".$e->getMessage();
		$db = null;
		jump("データベースの接続に失敗しました。\n");
		die();//どこかにジャンプさせるかも?とりあえず
	}
//スレッドの存在確認
	$sql = "SELECT * FROM THREAD WHERE T_ID = :TID;";
	$stmt = $db->prepare($sql);
	$stmt->bindParam(":TID",$data['TID'], PDO::PARAM_INT);
	//DBからデータの読み込み
	try{
	$stmt -> execute();
	unset($sql);//不要な変数を削除
	}catch(PDOException $e){
		//echo "表示に失敗しました\n".$e->getMessage();
		$db = null;//DBとの接続を終了
		jump("そのようなスレッドは存在しません\n");
		die();//どこかにジャンプさせるかも?とりあえず
	}
	while ($i = $stmt->fetch(PDO::FETCH_ASSOC)) {
		mb_convert_encoding($i['T_TITLE'],'UTF-8','auto');
		echo $i['T_TITLE']."を見つけました\n";
	}
	//echo $i['T_TITLE']."を見つけました\n";
	unset($i);//不要な変数を削除
	unset($stmt);//$stmt初期化
//書き込み処理
	$sql = 
		"INSERT INTO POST (T_ID,P_NAME,P_EMAIL,P_TEXT)".
		"VALUES	(:TID,:NAME,:EMAIL,:TEXT);";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(":TID", $data['TID'], PDO::PARAM_INT);
		$stmt->bindParam(":NAME", $data['NAME']);
		@$stmt->bindParam(":EMAIL", $data['EMAIL']);
		$stmt->bindParam(":TEXT", $data['TEXT']);
	try{
	$stmt->execute();
	unset($sql);//不要な変数を削除
	}catch(PDOException $e){
		//echo "投稿に失敗しました\n".$e->getMessage();
		$db = null;//DBとの接続を終了
		jump("投稿に失敗しました。\n");
		die();//どこかにジャンプさせるかも?とりあえず
	}
	unset($stmt);//$stmt初期化
//読み込んだデータの表示
	$sql = "SELECT * FROM POST WHERE T_ID =:TID";
	$stmt = $db->prepare($sql);
	$stmt->bindParam(":TID",$data['TID'], PDO::PARAM_INT);
	try{
	$stmt -> execute();
	unset($sql);//不要な変数を削除
	}catch(PDOException $e){
		//echo "表示に失敗しました\n".$e->getMessage();
		$db = null;//DBとの接続を終了
		jump("スレッドに書込みできません\n");
		die();//どこかにジャンプさせるかも?とりあえず
	}
	$res = 1;
	$rand = rand();
	$dir = './'.$data['TID'].'/';
	$file = 'index'.'.html';
	if ($fp = fopen($dir.$file,"w")){
		 flock($fp,LOCK_EX);
		while ($i = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if(!fwrite($fp,'<p>'.$res.' '.$i['P_NAME'].' '.$i['P_POSTTIME'].'<br>'.$i['P_TEXT'].'<br>'."\n")){
				jump("書き込みに失敗しました");}
				fflush($fp);
				$res++;
				echo '<p>' . $i['P_ID'] . ':' . $i['T_ID']. $i['P_NAME'] . ':' . $i['P_TEXT'] . ':'. $i['P_POSTTIME'] ."</p>\n";
		}
		flock($fp,LOCK_UN);
		fclose($fp);
		unset($i,$res,$fp,$res,$rand);//不要な変数を削除
	}else{
		jump("書き込みに失敗しました");}
	$db = null;//DBとの接続を終了
	require 'post_ok.php';
}
main();
?>