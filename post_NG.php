<?php

echo <<<EOM
<!DOCUTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="refresh"content="5;URL=./">
<title>{$message}</title>
<link rel="stylesheet" href="../css.css">

</head>
<body>
<h3>エラーが発生しました。{$message}</h3><br>
<h3>元のページに戻ります...</h3>
</body>
</html>
EOM;
?>