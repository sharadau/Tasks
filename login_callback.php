<?php
$state = "dashboard";
 require_once "config.php";

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
    $authHeaderSecret = base64_encode( CLIENT_ID.':'.SECRET );
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
    $url = 'https://'.OKTA_WEB_APP.'.com/oauth2/default/v1/token?' . $query;
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
    ->setClientId(CLIENT_ID)
    ->setIssuer('https://'.OKTA_WEB_APP.'.com/oauth2/default')
    ->build();

$jwt = $jwtVerifier->verify($jwt);

//var_dump($jwt); //Returns instance of \Okta\JwtVerifier\JWT

		echo "<BR> Authorization response <br>";

print_r($jwt->toJson()); // Returns Claims as JSON Object
print_r("<br> Welcome User:");
print_r($jwt->toJson()->sub); // Returns Claims as JSON Object

//var_dump($jwt->getClaims()); // Returns Claims as they come from the JWT Package used

//var_dump($jwt->getIssuedAt()); // returns Carbon instance of issued at time
//var_dump($jwt->getIssuedAt(false)); // returns timestamp of issued at time

//var_dump($jwt->getExpirationTime()); //returns Carbon instance of Expiration Time
//var_dump($jwt->getExpirationTime(false)); //returns timestamp of Expiration Time

//=========================introspection_request======================

if(array_key_exists('code', $_REQUEST)) {
    $introspection_request = introspection_request($_REQUEST['code'],$exchange->access_token);
}
function introspection_request($code,$token) {
    $authHeaderSecret = base64_encode( CLIENT_ID.':'.SECRET);
    $query = http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $code,
        'token' => $token,
        'redirect_uri' => 'http://localhost/temboSocial/login_callback.php'
    ]);
    $headers_i = [
        'Authorization: Basic ' . $authHeaderSecret,
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        'Connection: close',
        'Content-Length: 0'
    ];
    $url_i = 'https://'.OKTA_WEB_APP.'.com/oauth2/default/v1/introspect?' . $query;
    $ch1 = curl_init();
    curl_setopt($ch1, CURLOPT_URL, $url_i);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch1, CURLOPT_HEADER, 0);
    curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers_i);
    curl_setopt($ch1, CURLOPT_POST, 1);
    $output = curl_exec($ch1);
    $httpcode = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
    if(curl_error($ch1)) {
        $httpcode = 500;
    }
    curl_close($ch1);
		echo "<BR> introspection response<br>";
	var_dump($output);
    return json_decode($output);
}
//$jwt_i = $introspection_request->access_token;
/*$jwtVerifier = (new \Okta\JwtVerifier\JwtVerifierBuilder())
    ->setDiscovery(new \Okta\JwtVerifier\Discovery\Oauth) // This is not needed if using oauth.  The other option is OIDC
    ->setAdaptor(new \Okta\JwtVerifier\Adaptors\SpomkyLabsJose)
    ->setAudience('api://default')
    ->setClientId(CLIENT_ID)
    ->setIssuer('https://dev-538998.oktapreview.com/oauth2/default')
    ->build();

$jwt_i = $jwtVerifier->verify($jwt_i);

//var_dump($jwt); //Returns instance of \Okta\JwtVerifier\JWT


print_r($jwt_i->toJson()); // Returns Claims as JSON Object*/
print_r("<br> Welcome User(introspection_request): ");
print_r($introspection_request->username); // Returns Claims as JSON Object