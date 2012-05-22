<?php

/*
 * just prints rate limit of Twitter API
 */

require(dirname(__FILE__).'/configs/bootstrap.php');

$consumer_key = CONSUMER_KEY;
$consumer_secret= CONSUMER_SECRET;
$access_key = ACCESS_KEY;
$access_secret = ACCESS_SECRET;

$client = new TwitterOAuth($consumer_key,$consumer_secret,$access_key,$access_secret);
$screen_name = BOT_SCREEN_NAME;
$status = $client->get('account/rate_limit_status');
$tools = new myFunctions();

$tools->pr($status);