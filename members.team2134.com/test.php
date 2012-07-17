<?php

require_once('cdsengineers.php');
require_once('googleOAuthApi/apiClient.php');
require_once 'googleOAuthApi/contrib/apiCalendarService.php';
session_start();

$client = new apiClient();
$client->setApplicationName("Google Calendar PHP Starter Application");

// Visit https://code.google.com/apis/console?api=calendar to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('922269743018.apps.googleusercontent.com');
$client->setClientSecret('wlBfhZcm-smGIwTEptHjwu_h');
$client->setRedirectUri('http://members.team2134.com/functions/googleCalendarCallback');
$client->setDeveloperKey('AI39si4ebKPBVgPMl0BaNYo_VUJoicvhK6R4CakT3rtHIV1upg22IKrXGDN8QjBPNfuQXGLAc6vJLFFpkUNMw6MC5gp8K7vdAw');

$cal = new apiCalendarService($client);
if (isset($_GET['logout'])) {
  unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $calList = $cal->calendarList->listCalendarList();
  print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";


$_SESSION['token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
}
?>



<!--<html>
  <head>
    <script src="https://apis.google.com/js/client.js"></script>
    <script>
      function auth() {
        var config = {
          'client_id': 'YOUR CLIENT ID',
          'scope': 'https://www.googleapis.com/auth/urlshortener'
        };
        gapi.auth.authorize(config, function() {
          console.log('login complete');
          console.log(gapi.auth.getToken());
        });
      }
    </script>
  </head>

  <body>
    <button onclick="auth();">Authorize</button>

		https://accounts.google.com/o/oauth2/auth?scope=https://www.googleapis.com/auth/calendar&response_type=token&redirect_uri=http://members.team2134.com/functions/googleCalendarCallback&client_id=922269743018.apps.googleusercontent.com&from_login=1

  </body>
</html>-->
