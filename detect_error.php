<?php

/*
 * search and detect error among the statsues posted by post_statuses.php
 */

require(dirname(__FILE__).'/configs/bootstrap.php');

$consumer_key = CONSUMER_KEY;
$consumer_secret= CONSUMER_SECRET;
$access_key = ACCESS_KEY;
$access_secret = ACCESS_SECRET;

$client = new TwitterOAuth($consumer_key,$consumer_secret,$access_key,$access_secret);
$screen_name = BOT_SCREEN_NAME;
$statuses = array();

//fetch newest 20 statuses already posted.

$statuses = $client->get('statuses/user_timeline',array('count'=>20));
if(isset($statuses['error'])){
    echo "api error.";
    exit;
}

//check if the numbers posted are sequential
$i = 0;
$tmp = 0;
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
    exit;
}

//delete statuses posted after the error post
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