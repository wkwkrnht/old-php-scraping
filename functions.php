<?php
class MyDB{
    public $mysqli; // mysqliオブジェクト
    public $mode;   // 戻り値の形式："json" or "array"（連想配列）
    public $count;  // SQLによって取得した行数 or 影響した行数

    // コンストラクタ
    function __construct($mode = "array"){
        $this->mode = $mode;
        // DB接続
        $this->mysqli = new mysqli('localhost','DB-USER','DB-PASS','DB-NAME');
        if ($this->mysqli->connect_error){
            echo $this->mysqli->connect_error;
            exit;
        }else{
            $this->mysqli->set_charset("utf8");
        }
    }

    // デストラクタ(DB接続を閉じる)
    function __destruct(){$this->mysqli->close();}

    // SQL実行（SELECT/INSERT/UPDATE/DELETE に対応）
    function query($sql){
        // SQL実行
        $result = $this->mysqli->query($sql);
        // エラー
        if ($result === FALSE){
            // エラー内容
            $error = $this->mysqli->errno.": ".$this->mysqli->error;
            // 戻り値
            $rtn = array(
                'status' => FALSE,
                'count'  => 0,
                'result' => "",
                'error'  => $error
            );
            if($this->mode == "array"){return $rtn;}else{return json_encode($rtn);} // JSON形式で返す（デフォルト）
        }

        if($result === TRUE){
            // SELECT文以外
            // 影響のあった行数を格納
            $this->count = $this->mysqli->affected_rows;
            // 戻り値
            $rtn = array(
                'status' => TRUE,
                'count'  => $this->count,
                'result' => "",
                'error'  => ""
            );
            if($this->mode == "array"){return $rtn;}else{return json_encode($rtn);} // JSON形式で返す（デフォルト）
        }else{
            // SELECT文
            // SELECTした行数を格納
            $this->count = $result->num_rows;
            // 連想配列に格納
            $data = array();
            while($row = $result->fetch_assoc()){$data[] = $row;}
            // 結果セットを閉じる
            $result->close();
            // 戻り値
            $rtn = array(
                'status' => TRUE,
                'count'  => $this->count,
                'result' => $data,
                'error'  => ""
            );
            if($this->mode == "array"){return $rtn;}else{return json_encode($rtn);} // JSON形式で返す（デフォルト）
        }
    }

    function escape($str){return $this->mysqli->real_escape_string($str);}// 文字列をエスケープする
}

function set_card_info($url){
	$rss = simplexml_load_file($url);
	foreach($rss->channel->item as $item){
		$link        = $item->link;
		$title       = $item->title;
		$date        = date("Y年n月j日",strtotime($item->pubDate));
		$description = mb_strimwidth(strip_tags($item->description),0,150,"…","utf-8");
		$data        = array('url'=>$link,'title'=>$title,'description'=>$description,'date'=>$date);
        $db = new MyDB();
        echo $db->query("INSERT INTO rssdata(url,title,description,day) VALUES('$data->url','$data->title','$data->description','$data->date')");
	}
}

function make_card(){
    $db   = new MyDB();
    $json = $db->query("SELECT * FROM rssdata");
    $data = json_decode($json,true);
    var_dump($data);
}
?>
