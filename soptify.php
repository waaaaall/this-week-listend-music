<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$env = Dotenv::createImmutable(__DIR__);
$env->load();

$session = new SpotifyWebAPI\Session(
    $_ENV['SP_CLI_ID'],
    $_ENV['SP_CLI_SCT'],
    // $_ENV['SP_CALLBACK']
);

$api = new SpotifyWebAPI\SpotifyWebAPI();
$session->requestCredentialsToken();
$accessToken = $session->getAccessToken();
$api->setAccessToken($accessToken);

// $top = $api->getMyTop('tracks', ['limit' => 4]);
$result = $api->search('"nariaki obukuro" "butter"','track',['limit'=>1,'market'=>'JP']);
$artist = $result->tracks->items[0]->artists[0]->name;
$track = $result->tracks->items[0]->name;
$url = $result->tracks->items[0]->external_urls->spotify;
var_dump($result);
var_dump($artist);
var_dump($track);
var_dump($url);

// spotifyからリンクの取得

// twitterに投稿
