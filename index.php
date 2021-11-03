<?php


$url_initial_page = "http://applicant-test.us-east-1.elasticbeanstalk.com/";

$raw_html_initial_page = file_get_contents($url_initial_page);

$page_html_initial_page = new DOMDocument();
$page_html_initial_page->loadHTML($raw_html_initial_page);

$token_id = 'token';
$token_element = $page_html_initial_page->getElementById($token_id);
$token_value = $token_element->getAttribute('value');

$data = ['token' => $token_value];

$response_post_request_initial_page = file_get_contents($url_initial_page, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header'  => "Content-type: application/x-www-form-urlencoded",
        'content' => http_build_query($data)
    ]
]));

var_dump($response_post_request_initial_page);
