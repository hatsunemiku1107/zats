<?php
date_default_timezone_set('Asia/Tokyo');
error_reporting(E_ALL);
class Init {//データベース初期設定
	function __construct($_dummy,$dbname,$host,$user,$password) {
		$db = new Db($_dummy,$dbname,$host,$user,$password);
		printf("これよりDBの初期設定を開始します\n");
		ob_flush();sleep(1);
		$db->DB_initialize($dbname);
		printf("初期設定が終了しました。\n");
		ob_flush();
	}
}
class Post {//投稿クラス(呼び出しだけで書き込み可能)
	function __construct(&$_data,$dbname,$host,$user,$password) {
		$data = new Data($_data);
		$db = new Db($_data,$dbname,$host,$user,$password);
		$db->POST();
//		$db->POST_THREAD_confirm();
//		$db->POST_dbWrite();
		$db->POST_fileWrite();
		$db->MAKE_fileWrite();
	}
}//end of class

class MakeTHREAD {//スレッド作成クラス(呼び出しだけで書き込み可能)
	function __construct(&$_data,$dbname,$host,$user,$password) {
		$data = new Data($_data);
		$db = new Db($_data,$dbname,$host,$user,$password);
		$db->MAKE();
//		$db->MAKE_THREAD_confirm();
//		$db->MAKE_dbWrite();
		$db->MAKE_fileWrite();
		$db->POST_fileWrite();
	}
}//end of class
class READ {//作成途中。無視
	function __construct(&$_data,$dbname,$host,$user,$password) {
		$data = new Data($_data);
		$db = new Db($_data,$dbname,$host,$user,$password);
	}
}//end of class

class Methods {//メソッド類。インスタンス不要
	public static function e ($t){//empty
		return empty($t); 
	}
	public static function h ($str){//htmlspecialchars
		return htmlspecialchars($str,ENT_QUOTES,'UTF-8'); 
	}
	public static function n ($n){//is_numeric
		return (is_numeric($n))? $n: FALSE;
	}
	public static function jump($message){
		require('post_NG.php');
	}
	public static function escape (&$data){//$data = array()
		//各変数をエスケープ処理
		foreach($data as $key=>&$val){//実体参照
			if (!Methods::e($val)){
				mb_convert_encoding($val,'UTF-8','auto');
				$val = Methods::h($val);
			}else if($key == 'NAME'){
				require_once('config_default_name.php');
				$data['NAME'] = $default_name;
			}else if($key != 'EMAIL'){
				echo"不正なアクセスが検出されました。\n";
				Methods::jump(Methods::h($key)."が入力されていません");
				die();
			}
		}
		//Methods::escape($data);
		//Dbクラスに読みこむ時にエスケープする。

	}
}//end of class

class Data {//データチェック
	function __construct(&$data) {
	}
}//end of class

class Db{//データベース操作
//クラス変数
	private $db;
	public $data;
	function __construct(&$_data,$_dbname,$_host,$_user,$_password)
	{
      		//DB設定
		$_dsn = 'mysql:dbname='.$_dbname.';host='.$_host.';charset=utf8';
		$_user = $_user;
		$_password = $_password; 
		Methods::escape($_data);
		$this->data = $_data;
		$this->DB_connect($_dsn,$_user,$_password);
		unset($_dsn,$_user,$_password);
	}
	function __destruct()
	{
		$this->db = null;
	}
	private function failed($e,$message)//エラー時
	{	$this->__destruct();
		Methods::jump($e."\n\n".$message);
		die();
	}
	private function DB_connect($_dsn,$_user,$_password)
	{
		//DB接続
		try {
			$this->db = new PDO($_dsn,$_user,$_password);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);//よくわからない。あとで調べること
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//$this->db->MYSQL_ATTR_INIT_COMMAND('SET NAMES utf8');
		}
		catch(PDOException $e){
			$this->failed($e,"データベースの接続に失敗しました。\n");
		}
	}
	function DB_initialize($dbname)//DB初期設定
	{
		require_once('initialize_sql.php');
		$this->db->beginTransaction();
		try{		$stmt = $this->db;
			$stmt->exec($SQL_CREATE_TABLE_POST);
			$stmt->exec($SQL_CREATE_TABLE_THREAD);
			$stmt->query($SQL_INSERT_THREAD_DUMMY);
		}catch(PDOException $e){
			$this->db->rollBack();
			$this->failed($e,"データベース初期設定に失敗しました。");
		}
		$this->db->commit();
		return TRUE;
	}
	function POST(){
	//ポスト時のスレッド存在確認
		$stmt =	$this->search_T_ID('THREAD',$this->data['TID']);
		if (($i = $stmt->fetchAll()) == FALSE){//失敗
			$this->failed("POST:THREAD check failed","データの取得に失敗しました");
		}
		if (count($i) == 0){//スレッドが存在しない
			$this->failed("POST:THREAD is Not Found","そのようなスレッドは存在しません\n");
		}
		if (count($i) > 2){//スレッドが重複している?(とにかくおかしい)
			$this->failed("POST:THREAD is Duplicate","スレッドが複数存在します。管理者に問い合わせてください。\n");
		}
		$this->data['TITLE'] = $i[0]['T_TITLE'];//スレタイを格納
		if (empty($this->data['TITLE'])){
			$this->failed("THREAD_TITLE is not set","スレッド名を取得できません。管理者に問い合わせてください。\n");
		}
	//ポスト時のDBへの書き込み
		//書き込み処理
		$this->INSERT_POST($this->data['TID'],$this->data['NAME'],$this->data['EMAIL'],$this->data['TEXT']);
		//レス数の更新
		$this->UPDATE_RES($this->data['TID']);
	}
	function INSERT_POST($TID,$NAME,$EMAIL,$TEXT){//投稿内容を書き込み
		$sql = "INSERT INTO POST (T_ID,P_NAME,P_EMAIL,P_TEXT)"
			."VALUES	(:TID,:NAME,:EMAIL,:TEXT);";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(":TID", $TID, PDO::PARAM_INT);
		$stmt->bindValue(":NAME", $NAME);
		@$stmt->bindValue(":EMAIL", $EMAIL);
		$stmt->bindValue(":TEXT", $TEXT);
		try{
			$stmt->execute();
			unset($sql);//不要な変数を削除
		}catch(PDOException $e){
			$this->failed($e,"投稿に失敗しました。\n");
			//echo "投稿に失敗しました\n".$e->getMessage();
		}
		return TRUE;
	}
	function UPDATE_RES($_tid)//レス数の更新
	{
		//書き込み処理
		$stmt = $this->search_T_ID('POST',$_tid);
		$i = count($stmt->fetchAll());
		unset($stmt);
		$sql = 'UPDATE THREAD SET T_RES = :res WHERE T_ID = :tid;';
		$stmt = $this->db->prepare($sql);
			$stmt->bindValue(':res',$i,PDO::PARAM_INT);
			$stmt->bindValue(':tid',$_tid,PDO::PARAM_INT);
		try{
		$stmt->execute();
		}catch(PDOException $e){
			$this->failed($e,"データベースへの書き込みに失敗しました");
		}
		return TRUE;
	}

	function MAKE(){
	//スレッドの存在確認
		$stmt =	$this->search_T_TITLE('THREAD',$this->data["TITLE"]);
		if (($i = $stmt->fetchAll()) == FALSE){//失敗
		//	$this->failed("THREAD check failed","データの取得に失敗しました");
		}
		if (count($i) != 0){//スレッドが存在する
			$this->failed("THREAD is already make","そのスレッドは既に存在します\n");
		}
	//スレッド作成時のDBへの書き込み
		$this->INSERT_MAKE($this->data['TITLE'],$this->data['T_MAKEDATE']);
	//>>1書き込み
		$this->data['TID'] = $this->search_TID_by_T_TITLE($this->data['TITLE']);
		//書き込み処理
		$this->INSERT_POST($this->data['TID'],$this->data['NAME'],$this->data['EMAIL'],$this->data['TEXT']);
		//レス数の更新
		$this->UPDATE_RES($this->data['TID']);
	}
	function INSERT_MAKE($T_TITLE,$T_MAKEDATE)//スレッド作成
	{
		//書き込み処理
		$sql = "INSERT INTO THREAD (T_TITLE, T_MAKEDATE)"
			."VALUES (:TITLE,:T_MAKE);";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(":TITLE",$T_TITLE);
		$stmt->bindValue(":T_MAKE",$T_MAKEDATE, PDO::PARAM_INT);
		try{
		$stmt->execute();
		}catch(PDOException $e){
			//$this->failed($e,"スレッドの作成に失敗しました。\n");
		}
		return TRUE;
	}

	function POST_fileWrite(){
	//読み込んだレスのhtml書き出し
		$stmt =	$this->search_T_ID('POST',$this->data['TID']);
		$res = 1;
		$dir = './'.$this->data['TID'].'/';
		$file = 'index.html';
		if(!is_dir($dir)){
			mkdir($dir, 0777);
		}
		if ($fp = fopen($dir.$file,"w")){
			 flock($fp,LOCK_EX);
			$d = $stmt->fetchAll();
			for ($i = 0;$i < count($d);$i++){
				$_text[$i+1] = array(
					$d[$i]['P_NAME'],$d[$i]['P_EMAIL'],$d[$i]['P_POSTTIME'],$d[$i]['P_TEXT']
				);
			}
			$res = $i;
			require_once('mkhtml_thread.php');
			flock($fp,LOCK_UN);
			fclose($fp);
		}
		require 'post_ok.php';
	}

	function MAKE_fileWrite(){
	//スレッド一覧のhtml書き出し
		$stmt =	$this->search_THREAD_ALL();
		$dir = './';
		$file = 'index.html';
		if ($fp = fopen($dir.$file,"w")){
			 flock($fp,LOCK_EX);
			if (($d = $stmt->fetchAll())==FALSE){
				$this->failed("MAKE:THREAD_ALL fetch is failed","スレッドフェッチに失敗\n");
			}
			//var_dump(count($d));
			for ($i = 1; $i < count($d);$i++){
				$text[$i] = array(
					$d[$i]['T_ID'],$d[$i]['T_TITLE'],$d[$i]['T_RES']
				);
			}
			$res = $i;
			require_once('mkhtml_ita.php');
			flock($fp,LOCK_UN);
			fclose($fp);
		}
	}

	function search_T_TITLE_by_TID($TID)//TIDからスレッド名を検索
	{
		if ($TID > 1){ //1は使用しないため
			$stmt = $this->search_SELECT("THREAD","T_ID",$TID);
			$i = $stmt->fetch(PDO::FETCH_ASSOC);
			if(isset($i['T_TITLE'])){
				unset($stmt);//不要な変数を削除
				return $i['T_TITLE'];
			}else{
				unset($stmt);//不要な変数を削除
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}
	function search_TID_by_T_TITLE($T_TITLE)
	{
		$stmt = $this->search_T_TITLE('THREAD',$T_TITLE);
		$i = $stmt->fetch(PDO::FETCH_ASSOC);
		if(isset($i['T_ID'])){
			unset($stmt);//不要な変数を削除
			return (int)$i['T_ID'];
		}else{
			unset($stmt);//不要な変数を削除
			return FALSE;
		}
	}
	function search_T_TITLE($_table, $_title){//任意のT_TITLEを任意のテーブルから検索
		switch ($_table){
			case 'THREAD':
			$sql = "SELECT * FROM THREAD WHERE T_TITLE = :title;";
			break;
			case 'POST':
			$sql = "SELECT * FROM POST WHERE T_TITLE = :title;";
			break;
		}
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':title',$_title);
		try{
			$stmt -> execute();
		}catch(PDOException $e){
			$this->failed($e,"ERROR:search_THERAD_T_TITLE\n");
		}
		return $stmt;
	}
	function search_T_ID($_table, $_tid){//任意のT_IDを任意のテーブルから検索
		switch ($_table){
			case 'THREAD':
			$sql = "SELECT * FROM THREAD WHERE T_ID = :tid;";
			break;
			case 'POST':
			$sql = "SELECT * FROM POST WHERE T_ID = :tid;";
			break;
		}
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue(':tid',$_tid);
		try{
			$stmt -> execute();
		}catch(PDOException $e){
			$this->failed($e,"ERROR:search_THERAD_T_ID\n");
		}
		return $stmt;
	}
	function search_THREAD_ALL(){//全スレッド取得
		$sql = "SELECT * FROM THREAD;";
		$stmt = $this->db->prepare($sql);
		try{
			$stmt -> execute();
		}catch(PDOException $e){
			$this->failed($e,"ERROR:search_THERAD_T_ID\n");
		}
		return $stmt;
	}




}