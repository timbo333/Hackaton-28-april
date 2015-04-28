<?php
require_once 'init.php';
$connection = new tmhOAuth(array(
    'consumer_key' => 'DLy9S21DYt21ibV0JGdUX2Cs9',
    'consumer_secret' => '5yuxPnND9YwqQXTfqcVRGfV6qJKiYhmDMNLMxMWdDbI6Z7RDD8',
    'user_token' => '3216672460-tHKPcZrxhpEUp0nVpkbKAA7JcN0XUyszOhZz54D', //access token
    'user_secret' => 'LUbs9TXBBwhTRmMQuXE5jfDm9tiZO8ZC5x2KBp1o05Z0G' //access token secret
));
$parameters = array();

$parameters['geocode'] = (isset($_POST['geocode'])) ? $_POST['geocode'] : "";
$parameters['q'] = (isset($_POST['q'])) ? $_POST['q'] : "";
$parameters['count'] = (isset($_POST['count'])) ? $_POST['count'] : 10;
$parameters['since'] = (isset($_POST['since'])) ? $_POST['since'] : "2015-01-01";
$twitter_path = '1.1/search/tweets.json';

$http_code = $connection->request('GET', $connection->url($twitter_path), $parameters );

if ($http_code === 200) {
    $response = strip_tags($connection->response['response']);
    echo $response;
} else {
    http_response_code($http_code);
    echo "Error ID: ",$http_code, "<br>\n";
    echo "Error: ",$connection->response['error'], "<br>\n";
}
?>
