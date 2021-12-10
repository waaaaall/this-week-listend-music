<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use Abraham\TwitterOAuth\TwitterOAuth;

$env = Dotenv::createImmutable(__DIR__);
$env->load();

// lastfmからtoptrackの取得
$client = new Client();
$params = [
    'query' =>
    [
        'method' => 'user.gettoptracks',
        'user' => $_ENV['LASTFM_USER'],
        'api_key' => $_ENV['LASTFM_API_KEY'],
        'period' => '7day',
        'limit' => 1,
        'format' => 'json'
    ]
];

$response = $client->request('get', 'http://ws.audioscrobbler.com/2.0', $params);
$response = $response->getBody()->getContents();
$response = json_decode($response);

$track = $response->toptracks->track[0]->name;
$artist = $response->toptracks->track[0]->artist->name;

$query = '"' . $track . '" ' . '"' . $artist . '"';

//spotifyでトラック情報の取得
$session = new Session(
    $_ENV['SPOTIFY_CLIENT_ID'],
    $_ENV['SPOTIFY_CLIENT_SECRET'],
);

$spotify = new SpotifyWebAPI();
$session->requestCredentialsToken();
$accessToken = $session->getAccessToken();
$spotify->setAccessToken($accessToken);

$result = $spotify->search($query, 'track', ['limit' => 1]);
$artist = $result->tracks->items[0]->artists[0]->name;
$track = $result->tracks->items[0]->name;
$url = $result->tracks->items[0]->external_urls->spotify;

//ツイートする
$twitter = new TwitterOAuth(
    $_ENV['TWITTER_API_KEY'],
    $_ENV['TWITTER_API_SCT'],
    $_ENV['TWITTER_ACCESS_TOKEN'],
    $_ENV['TWITTER_ACCESS_SECRET']
);

$twitter->setApiVersion('2');
$twitter->get('users', ['ids' => $_ENV['TWITTER_USER_ID'], 'expansions' => 'pinned_tweet_id']);
if ($twitter->getLastHttpCode() == 200) {
    $pinned_id = $twitter->getLastBody()->data[0]->pinned_tweet_id;
} else {
    exit();
}

$text = $track . ' by ' . $artist . ' ' . $url . '
今週聴いていた曲です';

$twitter->post(
    'tweets',
    [
        'text' => $text,
        'reply' => ['in_reply_to_tweet_id' => $pinned_id]
    ],
    true
);
if ($twitter->getLastHttpCode() === 201) {
    $tweet_id = $twitter->getLastBody()->data->id;
    echo "success! https://twitter.com/good_wall/status/$tweet_id\n";
} else {
    echo 'failed!';
}
