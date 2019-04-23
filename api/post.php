<?php
    header("Content-Type: application/json; charset=UTF-8");
    
    if(isset( $_GET['forum']) && isset($_GET['popular'])){
        $forum =$_GET['forum'];
        $output = array();
        $after = null;
        if(isset($_GET['after']))$after = $_GET['after'];
        $popular=$_GET['popular'];
        $api = "https://www.dcard.tw/_api/forums/{$forum}/posts?popular={$popular}&limit=100";
        if($after!=null)$api = "https://www.dcard.tw/_api/forums/{$forum}/posts?popular={$popular}&after={$after}&limit=100";

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$api);
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $data = curl_exec($ch);
        $json = json_decode($data);
        foreach ($json as $index => $post) {
            $obj=array("id"=>$post->id,"title"=>$post->title,"floor"=>0,"media"=>$post->media);
            array_push($output,$obj);
        }
        echo json_encode($output);
        curl_close($ch);
    }else{
        echo json_encode(array('data'=>'','error'=>true));
    }

?>