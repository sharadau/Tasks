<?php
 require_once "config.php";
 require_once __DIR__ . '\vendor\autoload.php';

 header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
 ?>

<script
src="https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/2.6.0/js/okta-sign-in.min.js"
type="text/javascript"></script>
<link
href="https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/2.6.0/css/okta-sign-in.min.css"
type="text/css"
rel="stylesheet"/>
<link
href="https://ok1static.oktacdn.com/assets/js/sdk/okta-signin-widget/2.6.0/css/okta-theme.css"
type="text/css"
rel="stylesheet"/>
<script type="text/javascript">
  var oktaSignIn = new OktaSignIn({
    baseUrl: "https://".OKTA_WEB_APP.".com",
    clientId: CLIENT_ID,
    authParams: {
      issuer: "https://".OKTA_WEB_APP.".com/oauth2/default",
      responseType: ['token', 'id_token'],
      display: 'page'
    }
  });
  if (oktaSignIn.token.hasTokensInUrl()) {
    oktaSignIn.token.parseTokensFromUrl(
      function success(res) {
        // The tokens are returned in the order requested by `responseType` above
        var accessToken = res[0];
        var idToken = res[1]

        // Say hello to the person who just signed in:
        console.log('Hello, ' + idToken.claims.email);
        alert('Hello, ' + idToken.claims.email);

        // Save the tokens for later use, e.g. if the page gets refreshed:
        oktaSignIn.tokenManager.add('accessToken', accessToken);
        oktaSignIn.tokenManager.add('idToken', idToken);

        // Remove the tokens from the window location hash
        window.location.hash='';
      },
      function error(err) {
        // handle errors as needed
        console.error(err);
      }
    );
  } else {
    oktaSignIn.session.get(function (res) {
      // Session exists, show logged in state.
      if (res.status === 'ACTIVE') {
        //console.log('Welcome back, ' + res.login);
        console.log('Welcome back, ' + res);
		
		alert('Welcome back, ' + res.login);
        return;
      }
      // No session, show the login form
      oktaSignIn.renderEl(
        { el: '#okta-login-container' },
        function success(res) {
          // Nothing to do in this case, the widget will automatically redirect
          // the user to Okta for authentication, then back to this page if successful
        },
        function error(err) {
          // handle errors as needed
          console.error(err);
        }
      );
    });
  }
</script>
<body>
    <div id="okta-login-container"></div>
    Welcome 
</body>
<?php
require __DIR__ . '\vendor\autoload.php';

$query = http_build_query([
    'client_id' => CLIENT_ID,
    'response_type' => 'code',
    'response_mode' => 'query',
    'scope' => 'openid profile',
	
    'redirect_uri' => 'http://localhost/temboSocial/login_callback.php',
    'state' => 'dashboard',
    'nonce' => $nonce
]);

header('Location: ' . 'https://'.OKTA_WEB_APP.'.com/oauth2/default/v1/authorize?'.$query);
?>