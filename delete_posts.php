<?php

/*
 * delete statuses less than specified number
 */

require(dirname(__FILE__).'/configs/bootstrap.php');

$consumer_key = CONSUMER_KEY;
$consumer_secret= CONSUMER_SECRET;
$access_key = ACCESS_KEY;
$access_secret = ACCESS_SECRET;

$client = new TwitterOAuth($consumer_key,$consumer_secret,$access_key,$access_secret);
$screen_name = BOT_SCREEN_NAME;
$statuses = array();

// [debug]
//$limit = $client->get('account/rate_limit_status');print_r($limit);exit;
// [/debug]

//fetch latest 20 statuses already posted.
$statuses = $client->get('statuses/user_timeline',array('count'=>'20'));

foreach($statuses as $val){
    if((int)$val['text'] < 30){
        $client->post('statuses/destroy',array('id'=>$val['id_str']));
    }else{
        break;
    }
}

echo "complete";

function createApiUrl($method,$params = null){
    $url = 'http://twitter.com/'.$method;
    $ret = '';
    if($params){
        $params_on_url= '?';
        $loop_count = 0;
        foreach($params as $key=>$val){
            $loop_count++;
            $params_on_url .= $key .'='.$val;
            if($loop_count > 0 && $loop_count < count($params)){
                $params_on_url .= '&';
            }
        }
        $ret = $url.$params_on_url;
    }else{
        $ret = $url;
    }
    return $ret;
}