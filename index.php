<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

$env = Dotenv::createImmutable(__DIR__);
$env->load();

$session = new SpotifyWebAPI\Session(
    $_ENV['SPOTIFY_CLI_ID'],
    $_ENV['SPOTIFY_CLI_SCT'],
    $_ENV['SPOTIFY_CALLBACK']
);

$api = new SpotifyWebAPI\SpotifyWebAPI();

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $api->setAccessToken($session->getAccessToken());

} else {
    header('Location: ' . $session->getAuthorizeUrl([
        'scope' => [
            'playlist-read-private', 
            'playlist-modify-private', 
            'user-read-private',
            'playlist-modify',
            'user-top-read'
        ]
    ]));
    die();
}

$top = $api->getMyTop('tracks', ['limit' => 4]);
var_dump($top);