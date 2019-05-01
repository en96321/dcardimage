<?php
    header("Content-Type: application/json; charset=UTF-8");
    if(isset($_GET["id"])){
        $id = $_GET["id"];
        $api = "https://www.dcard.tw/_api/posts/{$id}";
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$api);
            curl_setopt($ch,CURLOPT_HEADER,false);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $data = curl_exec($ch);
            echo $data;
            curl_close($ch);
    }


?>