<?php
require "vendor/autoload.php";

use Dotenv\Dotenv;
use Abraham\TwitterOAuth\TwitterOAuth;

$env = Dotenv::createImmutable(__DIR__);
$env->load();

$connection = new TwitterOAuth(
    $_ENV['TW_API_KEY'],
    $_ENV['TW_API_SCT'],
    $_ENV['TW_ACCESS_TOKEN'],
    $_ENV['TW_ACCESS_SECRET']
);

$connection->setApiVersion('2');
$connection->get('users',['ids'=>'127167709','expansions'=>'pinned_tweet_id']);
if($connection->getLastHttpCode() == 200){
    echo "success";
    $pinned_id = $connection->getLastBody()->data[0]->pinned_tweet_id;
}else{
    echo 'failed';
}

// $connection->setApiVersion('2');
$text = 'test';
$connection->post('tweets', ['text'=> $text,'reply'=>['in_reply_to_tweet_id'=> $pinned_id]],true);
if($connection->getLastHttpCode() == 200){
    echo "success";
}else{
    echo 'failed';
var_dump($connection);

}


