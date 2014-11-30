<?php
//板(スレッド一覧)作成
$res_dec = $res -1;
$_text[0]=
"<!DOCUTYPE html>\n".
"<html>\n".
"	<head>\n".
"		<meta charset=\"UTF-8\">\n".
"		<base href=\"http://localhost/k/\">\n".
"		<link rel=\"stylesheet\" href=\"css.css\">\n".
"		<link rel=\"text/javascript\" href=\"js.js\">\n".
"		<title>雑談掲示板(現在".$res_dec."スレッド</title>\n".
"	</head>\n".
"	<body>\n".
"		<h2 id=\"title\">雑談掲示板(現在".$res_dec."スレッド</h2>\n".
"		<div>\n".
"			<dl>";

for ($i = 1,$tmp=''; $i < $res;$i++){
	$_text[$i] = 
		"	<tr>\n".
		"		<dt>\n".
		"			<td>".$i." </td><td><a href=\"./".$text[$i][0]."\">".$text[$i][1]."</a></td><td>  レス数:</td><td>".$text[$i][2]."</td>\n".
		"		</dt>\n".
		"	</tr>\n";
		$tmp .=
	"					<option value=\"".$text[$i][0]."\">".$text[$i][1]."</option>\n";
}
$_text[$res] =
	"			</dl>".
	"		</div>\n".
	"		<div>\n".
	"			<form action=\"post.php\" method=\"post\">\n".
	"				<select name=\"TID\">\n";
$_text[$res] .= $tmp;//option(ドロップダウンリスト)	
$_text[$res].=	
	"				</select><br>\n".
	"				NAME:<input name=\"NAME\" type=\"text\"><br>\n".
	"				E-mail:<input name=\"EMAIL\" type=\"e-mail\"><br>\n".
	"				Text:<input name=\"TEXT\" type=\"text\" value=\"\"><br>\n".
	"				<input type=\"submit\" value=\"送信\"><input type=\"reset\" value=\"リセット\">\n".
	"			</form>\n".
	"			\n".
	"			<form action=\"makethread.php\" method=\"post\">\n".
	"				TITLE:<input name=\"TITLE\" type=\"text\"><br>\n".
	"				NAME:<input name=\"NAME\" type=\"text\"><br>\n".
	"				E-mail:<input name=\"EMAIL\" type=\"e-mail\"><br>\n".
	"				Text:<input name=\"TEXT\" type=\"text\" value=\"\"><br>\n".
	"				<input type=\"submit\" value=\"送信\"><input type=\"reset\" value=\"リセット\">\n".
	"			</form>\n".
	"		</div>\n".
	"		<footer>\n".
	"			<hr>\n".
	"			雑談掲示板\n".
	"		</footer>\n".
	"	</body>\n".
	"</html>\n";
//var_dump($_text);

foreach ($_text as $val){
	if(!fwrite($fp,$val)){
		$this->failed("fwrite()failed\n","書き込みに失敗しました\n");
	}
	fflush($fp);

}
unset($val);//不要な変数を削除
?>