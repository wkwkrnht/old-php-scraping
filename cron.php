<?php include_once('./functions.php');
$url = array('https://wkwkrnht.wordpress.com/feed/','http://wkwkrnht.gegahost.net/feed/');
while($val=current($url)){set_card_info($val);next($url);}
make_rss();?>
