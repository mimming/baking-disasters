<?php
include_once("util.php");
$client = init_api_client();
$auth_url = $client->createAuthUrl();
// If there's nothing, we're probably starting a new auth flow so store the
//   previous page and bounce to Google's OAuth page
if(!isset($_GET['code'])) {
  $_SESSION['original_referrer'] = $_SERVER['HTTP_REFERER'];
  header("location: " . $auth_url);
}
// If there's a code we need to swap it for an access token
else { //if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['access_token'] = $client->getAccessToken();
  
  if(isset($_SESSION['original_referrer'])) {
    header('Location: ' . $_SESSION['original_referrer']);
    unset($_SESSION['original_referrer']);
  } else {
    header('Location: '.$app_base_path);
  }
}