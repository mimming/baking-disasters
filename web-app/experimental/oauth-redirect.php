<!DOCTYPE html>
<?php
include_once("util.php");
$client = init_api_client();

$client->authenticate($_GET['code']);
$_SESSION['access_token'] = $client->getAccessToken();
    print_r($client);

?>

<html>
<head>
  <title>Done Signing In</title>
  <script src="https://apis.google.com/js/plusone.js"></script>
  <script>
    gapi.load('connect', function () {
      // Complete the connection and automatically close this window
      // TODO: put the real values in here
      gapi.connect.complete({
        "access_token":"<?= $_SESSION['access_token'] ?>"
      });
    });
  </script>
</head>

<body>
<p>Done signing in your account. You can close this window.</p>
</body>
</html>