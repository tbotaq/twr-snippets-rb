<?php

/*
 * posts 3200 statuses via Twitter API
 */

require(dirname(__FILE__).'/configs/bootstrap.php');

$token = isset($_GET['token']) ? $_GET['token'] : "no_token";

if($token === SAFE_TOKEN){
    post_tweets();
    detect_error();
}else{
    echo "Bad request.";
}

function post_tweets(){
    
    $consumer_key = CONSUMER_KEY;
    $consumer_secret= CONSUMER_SECRET;
    $access_key = ACCESS_KEY;
    $access_secret = ACCESS_SECRET;

    $client = new TwitterOAuth($consumer_key,$consumer_secret,$access_key,$access_secret);
    $screen_name = BOT_SCREEN_NAME;
    $status = $client->get('statuses/user_timeline',array('count'=>'200'));

    $last_num = (int)$status['0']['text'];

    for($post_num = $last_num + 1;$post_num <= $last_num + 10;$post_num++){

        if($post_num > 3200){
            $client->post('direct_messages/new',array('screen_name'=>MASTER_SCREEN_NAME,'text'=>'finished posting 3200 statuses @'.BOT_SCREEN_NAME.'.'));
            echo "finished posting 3200 statuses at ".time();
            break;
        }

        $post_body = $post_num;
        $result = $client->post('statuses/update',array('include_entities'=>true,'status'=>$post_body));
    
        if(isset($result->error)){
            echo "broke at ".$post_num." .";
            break;
        }
    }

    echo "Posted at ".date("M-d-G:i:s")."\n";
}

function detect_error(){

    $consumer_key = CONSUMER_KEY;
    $consumer_secret= CONSUMER_SECRET;
    $access_key = ACCESS_KEY;
    $access_secret = ACCESS_SECRET;   

    $client = new TwitterOAuth($consumer_key,$consumer_secret,$access_key,$access_secret);
    $statuses = array();

    //fetch all the statuses already posted.

    $status = $client->get('statuses/user_timeline',array('count'=>'200'));
    $i=0;
    while(true){
        if($i==0){
            $option = array('count'=>200);

        }else{
            $previous_oldest_id = $oldest_id;
            $option = array('count'=>200,'include_rts'=>true,'max_id'=>$previous_oldest_id);
        }

        $status = $client->get('statuses/user_timeline',$option);
        $cnt=0;
        foreach($status as $val){
            if($cnt>0 || $i==0){
                $statuses[] = $val;
            }
            $cnt++;
        }
    
        if(count($status)<200){
            break;
        }else{
            $oldest_id = $status['199']['id_str'];
        }
        $i++;
    } 

    //check if the numbers posted are sequential
    $i=0;
    $tmp=0;
    $smallest_lacking_number = "";
    foreach($statuses as $val){
    
        if($i>0){
            $check_number[$i] = (int)$val['text'];
            $check_number[$i-1] = $tmp;

            $deff = $check_number[$i-1] - $check_number[$i];
            if($deff >1){
                $smallest_lacking_number = $check_number[$i]+1;
            }
        }

        $tmp = (int)$val['text'];
        $i++;
    }
    if($smallest_lacking_number){
        echo "smallest lacking number is ".$smallest_lacking_number.".";
    }else{
        echo "There is no number lacking.";
        echo " at ".date("M-d-G:i:s")."\n";
        return;
    }

    $point = (int)$statuses['0']['text'];//initializing
    $count=0;
    foreach($statuses as $val){
        $point = (int)$val['text'];
        if($point <= $smallest_lacking_number){
            break;
        }else{
            $client->post('statuses/destroy',array('id'=>$val['id_str']));
            $count++;
        }
    }

    echo "And destroyed ".$count." values.";
    echo " at ".date("M-d-G:i:s")."\n";
}