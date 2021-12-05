<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;

$env = Dotenv::createImmutable(__DIR__);
$env->load();

$client = new Client();
$params=[
    'query'=>[
        'method'=>'user.gettoptracks',
        'user'=>$_ENV['LF_USER'],
        'api_key'=>$_ENV['LF_API_KEY'],
        'period'=>'7day',
        'limit'=>1,
        'format'=>'json'
    ]
];

$response = $client->request('get','http://ws.audioscrobbler.com/2.0',$params);

echo $response = $response->getBody()->getContents();

$response = json_decode($response);
var_dump($response->toptracks->track[0]->name);
var_dump($response->toptracks->track[0]->artist->name);
