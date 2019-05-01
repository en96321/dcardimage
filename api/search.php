<?php
    header("Content-Type: application/json; charset=UTF-8");
    if(isset($_GET["query"])){
        $forum = "";
        $query = urlencode($_GET["query"]);
        $offset = "";
        $limit = "10";
        if(isset($_GET["forum"]))$forum = "&forum=".$_GET["forum"];
        if(isset($_GET["offset"]))$offset = "&offset=".$_GET["offset"];
        if(isset($_GET["limit"]))$limit = $_GET["limit"];
        $api = "https://www.dcard.tw/_api/search/posts?query={$query}&limit={$limit}{$forum}{$offset}";
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$api);
            curl_setopt($ch,CURLOPT_HEADER,false);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $data = curl_exec($ch);
            echo $data;
            curl_close($ch);
    }


?>