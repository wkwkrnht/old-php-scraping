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
		<section id="feed">
			<?php
			$rss1=simplexml_load_file('https://wkwkrnht.wordpress.com/feed/');
				foreach($rss1->channel->item as $item){$link=$item->link;$title=$item->title;$date=date("Y年n月j日",strtotime($item->pubDate));$description=mb_strimwidth(strip_tags($item->description),0,150,"…","utf-8");
					echo'<div class="card"><a href="' . $link . '" target="_blank"><h3 class="title">' . $title . '</h3><span class="date">' . $date . '</span><br /><p class="text">' . $description . '</p></a></div>';
				};
			$rss2=simplexml_load_file('http://wkwkrnht.gegahost.net/feed/');
				foreach($rss2->channel->item as $item){$link=$item->link;$title=$item->title;$date=date("Y年n月j日",strtotime($item->pubDate));$description=mb_strimwidth(strip_tags($item->description),0,150,"…","utf-8");
					echo'<div class="card"><a href="' . $link . '" target="_blank"><h3 class="title">' . $title . '</h3><span class="date">' . $date . '</span><br /><p class="text">' . $description . '</p></a></div>';
				};
			?>
		</section>
	</body>
</html> 