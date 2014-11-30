<?php

echo <<<EOM
<!DOCUTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<
<title>処理が完了しました</title>
<link rel="stylesheet" href="../css.css">
</head>
<body>
<h3>処理が完了しました。元のページに戻ります...</h3>
<input type="button" action="./{$this->data['TID']}">
<a href="./{$this->data['TID']}">{$this->data['TID']}</a>
</body>
</html>
EOM;

?>