<?php
class MyDB{
    public $mysqli; // mysqliオブジェクト
    public $mode;   // 戻り値の形式："json" or "array"（連想配列）
    public $count;  // SQLによって取得した行数 or 影響した行数

    // コンストラクタ
    function __construct($mode = "json"){
        $this->mode = $mode;
        // DB接続
        $this->mysqli = new mysqli('sql209.gegahost.net','gega_16937875','085gnt230','gega_16937875_rssdata');
        //$this->mysqli = new mysqli('localhost', 'DB-USER', 'DB-PASS', 'DB-NAME');
        if ($this->mysqli->connect_error){
            echo $this->mysqli->connect_error;
            exit;
        }else{
            $this->mysqli->set_charset("utf8");
        }
    }
    function __destruct(){$this->mysqli->close();}

    // SQL実行（SELECT/INSERT/UPDATE/DELETE に対応）
    function query($sql){
        $result = $this->mysqli->query($sql);
        if ($result === FALSE){
            $error = $this->mysqli->errno.": ".$this->mysqli->error;
            $rtn = array(
                'status' => FALSE,
                'count'  => 0,
                'result' => "",
                'error'  => $error
            );
            if($this->mode == "array"){return $rtn;}else{return json_encode($rtn);} // JSON形式で返す（デフォルト）
        }

        if($result === TRUE){
            $this->count = $this->mysqli->affected_rows;
            $rtn = array(
                'status' => TRUE,
                'count'  => $this->count,
                'result' => "",
                'error'  => ""
            );
            if($this->mode == "array"){return $rtn;}else{return json_encode($rtn);}
        }else{
            $this->count = $result->num_rows;
            $data = array();
            while($row = $result->fetch_assoc()){$data[] = $row;}
            $result->close();
            $rtn = array(
                'status' => TRUE,
                'count'  => $this->count,
                'result' => $data,
                'error'  => ""
            );
            if($this->mode == "array"){return $rtn;}else{return json_encode($rtn);}
        }
    }

    function escape($str){return $this->mysqli->real_escape_string($str);}
}

function get_data(){
    $db    = new MyDB();
    $json  = $db->query("SELECT * FROM rssdata");
    $array = json_decode($json,true);
    $data  = $array["result"];
    return $data;
}

function set_card_info($url){
	$rss = simplexml_load_file($url);
    $db = new MyDB();
	foreach($rss->channel->item as $item){
		$link = $item->link;
        $array_link = $db->query("SELECT link FROM rssdata");
        $array_link = json_decode($array_link,true);
        $array_link = $array["result"];
        if(array_key_exists($link,$array_link)===false){
            $title       = $item->title;
            $description = mb_strimwidth(strip_tags($item->description),0,150,"…","utf-8");
    		$date        = date("Y年n月j日",strtotime($item->pubDate));
            $db->query("INSERT INTO rssdata(url,title,description,day) VALUES('$link','$title','$description','$date')");
            require_once('twitteroauth/autoload.php');
            use Abraham\TwitterOAuth\TwitterOAuth;

            //認証情報４つ
            $consumerKey       = "Wne0SgFMkbS7QNkt2p7AVBgop";
            $consumerSecret    = "oAh5MesR9hhmghn6NfUYT1chcY7g2DmsLw5o3Srr56YB9X0cTs";
            $accessToken       = "3197195484-ripEmUKUVoLn1zYogvAhyhv9MUtKdoHT4mT5LuX";
            $accessTokenSecret = "ga8rHeIASryFGSne4YrDfNg9kTnzHhVEniFOUJH98ALnA";

            //接続&ツイート&レスポンス確認
            $connection = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);
            $txt        = $title . $link;
            $res        = $connection->post("statuses/update",array("status"=>$txt));
            var_dump($txt);
        }
	}
}

function make_card(){
    $html = '';
    $data = get_data();
    while(list($key,$val)=each($data)){
        $html .= '
        <div class="card">
            <a href="' . $val["link"] . '" target="_blank">
                <h3 class="title">' . $val["title"] . '</h3>
                <span class="date">' . $val["date"] . '</span><br>
                <p class="text">' . $val["description"] . '</p>
            </a>
        </div>';
    }
    echo $html;
}

function make_rss(){
    require_once('./src/Item.php');
	require_once('./src/Feed.php');
    require_once('./src/ATOM.php');
    date_default_timezone_set('Asia/Tokyo');
    $data = get_data();
    $feed = new ATOM;
    $feed->setTitle('wkwkrnht');
    $feed->setLink('http://wkwkrnht.gegahost.net/');
    $feed->setDate(new DateTime());
    while(list($key,$val)=each($data)){
        $item = $feed->createNewItem();
        $item->setTitle($val["title"]);
        $item->setLink($val["link"]);
        //$item->setDate(strtotime($val["date"]));
        $item->setDescription($val["description"]);
        $feed->addItem($item);
    }
    $xml  = $feed->generateFeed();
    $file = 'atom.xml';
    @file_put_contents($file,$xml);
}
?>
