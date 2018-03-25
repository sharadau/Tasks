<?php
$state = "dashboard";
 require __DIR__ . '/autoload.php';
//include_once "vendor\okta\jwt-verifier\src\JwtVerifierBuilder.php";
//include_once "vendor\okta\jwt-verifier\src\Discovery\Oauth.php";
//include_once "vendor\okta\jwt-verifier\src\Adaptors\SpomkyLabsJose.php";

include_once("Okta\JwtVerifier\JwtVerifierBuilder.php");
include_once("Okta\JwtVerifier\Discovery\Oauth.php");
include_once("Okta\JwtVerifier\Adaptors\SpomkyLabsJose.php");
//var_dump($_REQUEST['state']);
if(array_key_exists('state', $_REQUEST) && $_REQUEST['state'] !== $state) {
    throw new \Exception('State does not match.');
}

if(array_key_exists('code', $_REQUEST)) {
    $exchange = exchangeCode($_REQUEST['code']);
}

function exchangeCode($code) {
    $authHeaderSecret = base64_encode( '0oaefscaksGIMdMd50h7:BmGoSCqCVyAyzW9oL_07wnkxt3kGdx3ckkiKkHLN' );
    $query = http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => 'http://localhost/temboSocial/login_callback.php'
    ]);
    $headers = [
        'Authorization: Basic ' . $authHeaderSecret,
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        'Connection: close',
        'Content-Length: 0'
    ];
    $url = 'https://dev-538998.oktapreview.com/oauth2/default/v1/token?' . $query;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if(curl_error($ch)) {
        $httpcode = 500;
    }
    curl_close($ch);
		//echo "here";
	//var_dump($output);
    return json_decode($output);
}
$jwt = $exchange->access_token;
//$jwt = 'eyJhbGciOiJSUzI1Nqd0FfRzh6X0ZsOGlJRnNoUlRuQUkweVUifQ.eyJ2ZXIiOjEsiOiJwaHBAb2t0YS5jb20ifQ.ZGrn4fvIoCq0QdSyA';
$jwtVerifier = (new \Okta\JwtVerifier\JwtVerifierBuilder())
    ->setDiscovery(new \Okta\JwtVerifier\Discovery\Oauth) // This is not needed if using oauth.  The other option is OIDC
    ->setAdaptor(new \Okta\JwtVerifier\Adaptors\SpomkyLabsJose)
    ->setAudience('api://default')
    ->setClientId('0oaefscaksGIMdMd50h7')
    ->setIssuer('https://dev-538998.oktapreview.com/oauth2/default')
    ->build();

$jwt = $jwtVerifier->verify($jwt);

//var_dump($jwt); //Returns instance of \Okta\JwtVerifier\JWT


print_r($jwt->toJson()); // Returns Claims as JSON Object
print_r("<br> Welcome User:");
print_r($jwt->toJson()->sub); // Returns Claims as JSON Object

//var_dump($jwt->getClaims()); // Returns Claims as they come from the JWT Package used

//var_dump($jwt->getIssuedAt()); // returns Carbon instance of issued at time
//var_dump($jwt->getIssuedAt(false)); // returns timestamp of issued at time

//var_dump($jwt->getExpirationTime()); //returns Carbon instance of Expiration Time
//var_dump($jwt->getExpirationTime(false)); //returns timestamp of Expiration Time