<?php
require_once 'init.php';
$connection = new tmhOAuth(array(
    'consumer_key' => 'DLy9S21DYt21ibV0JGdUX2Cs9',
    'consumer_secret' => '5yuxPnND9YwqQXTfqcVRGfV6qJKiYhmDMNLMxMWdDbI6Z7RDD8',
    'user_token' => '3216672460-tHKPcZrxhpEUp0nVpkbKAA7JcN0XUyszOhZz54D', //access token
    'user_secret' => 'LUbs9TXBBwhTRmMQuXE5jfDm9tiZO8ZC5x2KBp1o05Z0G' //access token secret
));
$parameters = array();

$parameters['geocode'] = (isset($_GET['geocode'])) ? $_GET['geocode'] : "";
$parameters['q'] = (isset($_GET['q'])) ? $_GET['q'] : "";
$parameters['count'] = (isset($_GET['count'])) ? $_GET['count'] : 10;
$twitter_path = '1.1/search/tweets.json';

$http_code = $connection->request('GET', $connection->url($twitter_path), $parameters );

if ($http_code === 200) { // if everything's good
    $response = strip_tags($connection->response['response']);

    if (isset($_GET['callback'])) { // if we ask for a jsonp callback function
        echo $_GET['callback'],'(', $response,');';
    } else {
        echo $response;
    }
} else {
    echo "Error ID: ",$http_code, "<br>\n";
    echo "Error: ",$connection->response['error'], "<br>\n";
}