<?php
$text[0]=
"<!DOCUTYPE html>\n".
"<html>\n".
"	<head>\n".
"		<meta charset=\"UTF-8\">\n".
"		<base href=\"http://localhost/k/\">\n".
"		<link rel=\"stylesheet\" href=\"css.css\">\n".
"		<link rel=\"text/javascript\" href=\"js.js\">\n".
"		<title>".@$this->data['TITLE']."</title>\n".
"	</head>\n".
"	<body>\n".
"		<h2 id=\"title\">".$this->data['TITLE']."</h2>\n".
"		<div>\n".
"			<dl>\n";
for ($i = 1; $i <= $res;$i++){
//	echo"$i:";var_dump($i);

	$text[$i] = 
		"	<tr>\n".
		"		<dt>\n".
		"			<td>".$i." </td><td>名前:</td><td>".((isset($_text[$i][1])&& !empty($_text[$i][1]))?"<a href=\"mailto:".$_text[$i][1]."\">".$_text[$i][0]."</a>":$_text[$i][0])."</td><td>  Time:</td><td>".$_text[$i][2]."</td>\n".
		"		</dt>".
		"	</tr>".
		"	<tr rowspan=\"6\">\n".
		"		<dd>\n".
		"			<td colspan=\"3\">".$_text[$i][3]."</td>\n".
		"		</dd>".
		"	</tr>";
}
unset($i);//不要な変数を削除
$text[$res+1]=
	"			</dl>\n".
	"		</div>\n".
	"		<div>\n".
	"			<form action=\"post.php\" method=\"post\">\n".
	"				<input type=\"hidden\" name=\"TID\"value=\"".$this->data['TID']."\">\n".
	"				NAME:<input name=\"NAME\" type=\"text\"><br>\n".
	"				E-mail:<input name=\"EMAIL\" type=\"e-mail\"><br>\n".
	"				Text:<input name=\"TEXT\" type=\"text\" value=\"\"><br>\n".
	"				<input type=\"submit\" value=\"送信\"><input type=\"reset\" value=\"リセット\">\n".
	"			</form>\n".
	"			\n".

	"		<footer>\n".
	"			<hr>\n".
	"			雑談掲示板<br>\n".
	"			<a href=\"./\">トップ</a>\n".
	"		</footer>\n".
	"	</body>\n".
	"</html>\n";

foreach ($text as $val){
	if(!fwrite($fp,$val)){
		$this->failed("fwrite()failed\n","書き込みに失敗しました\n");
	}
	fflush($fp);

}
unset($val);//不要な変数を削除
?>