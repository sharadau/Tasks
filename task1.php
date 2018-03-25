<?php

$query = http_build_query([
    'client_id' => '0oaefscaksGIMdMd50h7',
    'response_type' => 'code',
    'response_mode' => 'query',
    'scope' => 'openid profile',
	
    'redirect_uri' => 'http://localhost/temboSocial/login_callback.php',
    'state' => 'dashboard',
    'nonce' => $nonce
]);

header('Location: ' . 'https://dev-538998.oktapreview.com/oauth2/default/v1/authorize?'.$query);