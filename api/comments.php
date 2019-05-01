<?php
    header("Content-Type: application/json; charset=UTF-8");
    if(isset($_GET["id"])){
        $id = urlencode($_GET["id"]);
        $limit = "100";
        $after = 0;
        if(isset($_GET["limit"]))$limit = $_GET["limit"];
        if(isset($_GET["after"]))$after = $_GET["after"];
        $api = "https://www.dcard.tw/_api/posts/{$id}/comments?limit={$limit}&after={$after}";
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$api);
            curl_setopt($ch,CURLOPT_HEADER,false);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $data = curl_exec($ch);
            echo $data;
            curl_close($ch);
    }


?>