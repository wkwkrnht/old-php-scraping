<?php include_once('./functions.php');?>
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="wkwkrnht関連のRSSフィード">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<link rel="canonical" href="http://wkwkrnht.gegahost.net/rss/index.php">
		<title>wkwkrnht関連のRSSフィード</title>
		<style>body,header{width:100%;padding:0;margin:0;}header,.sitetitle{background-color:#ffcc00;text-align:center;}header{color:#fff;}.sitetitle{width:80%;}a{color:#333;text-decoration:none;}.sitetitle a{color:#fff;}.card{background-color:#fff;box-shadow:0 3px 6px rgba(0,0,0,.4);height:155px;width:680px;padding:10px;margin:15px calc(50% - 340px);}</style>
	</head>
	<body>
		<header>
			<h1>RT狂の思考ログのRSS</h1>(&copy;2016 wkwkrnht)
		</header>
		<main id="feed">
			<?php
			$url = array('https://wkwkrnht.wordpress.com/feed/','http://wkwkrnht.gegahost.net/feed/');
		   	while($val=current($url)){set_card_info($val);next($url);}
			make_card();
			make_rss();?>
		</main>
	</body>
</html>
