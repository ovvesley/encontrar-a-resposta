<?php

function getUrl()
{
    $url = "http://applicant-test.us-east-1.elasticbeanstalk.com/";
    return $url;
}

function getCookiesFromResponse($http_response_header)
{
    $cookies = null;
    if (preg_match_all('/Set-Cookie:[\s]([^;]+)/', $http_response_header[3], $matches)) {
        $cookies = $matches[1][0];
    }
    
    return $cookies;
}

function getTokenInitialPage($page)
{
    $token_id = 'token';
    $token_element = $page->getElementById($token_id);
    $token_value = $token_element->getAttribute('value');
    return $token_value;
}

function replacementToken($token)
{
    $new_token_value = null;
    $replacement = json_decode('{"0":"9","1":"8","2":"7","3":"6","4":"5","5":"4","6":"3","7":"2","8":"1","9":"0","a":"z","b":"y","c":"x","d":"w","e":"v","f":"u","g":"t","h":"s","i":"r","j":"q","k":"p","l":"o","m":"n","n":"m","o":"l","p":"k","q":"j","r":"i","s":"h","t":"g","u":"f","v":"e","w":"d","x":"c","y":"b","z":"a"}', true);

    for ($i = 0; $i < strlen($token); $i++) {
        $new_token_value .= $replacement[$token[$i]];
    }
    return $new_token_value;
}

function handleDataToRequest($data)
{
    return http_build_query($data);
}


function handleInitialPage()
{

    $urlInitialPage = getUrl();

    $rawHtmlInitialPage = file_get_contents($urlInitialPage);
    $cookies = getCookiesFromResponse($http_response_header);

    $pageHtmlInitial = new DOMDocument();
    $pageHtmlInitial->loadHTML($rawHtmlInitialPage);

    $token  = getTokenInitialPage($pageHtmlInitial);

    return [
        'token' => $token,
        'cookies' => $cookies
    ];
}

function handleAnswerResponse($rawHtml)
{
    $pageHtmlInitial = new DOMDocument();
    $pageHtmlInitial->loadHTML($rawHtml);

    $answer = $pageHtmlInitial->getElementById("answer")->textContent;

    return $answer;
}

function handleAnswerPage($token, $cookies)
{
    $url = getUrl();

    $data = handleDataToRequest(['token' => $token]);


    $response = file_get_contents($url, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header'  => [
                "Cookie: {$cookies}",
                'Referer: http://applicant-test.us-east-1.elasticbeanstalk.com/',
            ],
            'content' => $data,
        ]
    ]));

    $answer = handleAnswerResponse($response);

    return $answer;
}


function index()
{

    $initialPage = handleInitialPage();

    $tokenInitialPage = $initialPage['token'];
    $cookies = $initialPage['cookies'];

    $newToken = replacementToken($tokenInitialPage);

    $answer = handleAnswerPage($newToken, $cookies);

    error_log("Resposta: {$answer}");
}


index();
