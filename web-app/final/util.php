<?php
$app_base_path = "http://bakingdisasters.com/web-app/final";

// Create the tables if they do not exist
function init_db()
{
  $db = sqlite_open('PATH_TO_YOUR_DATABASE');
  $test_query = "select count(*) from sqlite_master where name = 'recipes'";
  if (sqlite_fetch_single(sqlite_query($db, $test_query)) == 0) {
    sqlite_exec($db, 'create table recipes (name text, description text,
                      ingredients text, directions text, photo_url text);');
    sqlite_exec($db, 'create table attempts (recipe_id int,
                      google_plus_activity_id text);');
  }
  return $db;
}

// Insert a new recipe
function insert_recipe($name, $description, $ingredients, $directions,
  $photo_url)
{
  $db = init_db();
  $name = sqlite_escape_string(strip_tags($name));
  $description = sqlite_escape_string(strip_tags($description));
  $ingredients = sqlite_escape_string(strip_tags($ingredients));
  $directions = sqlite_escape_string(strip_tags($directions));
  $photo_url = sqlite_escape_string(strip_tags($photo_url));

  sqlite_exec($db, "insert into recipes values ('$name', '$description',
                    '$ingredients', '$directions', '$photo_url')");
}

function list_recipes()
{
  $db = init_db();

  // Must use explicit select instead of * to get the rowid
  $query = sqlite_query($db, 'select rowid, name, description, ingredients,
                              directions, photo_url from recipes');
  return sqlite_fetch_all($query, SQLITE_ASSOC);

}

function get_recipe($rowid)
{
  $db = init_db();
  $rowid = sqlite_escape_string(strip_tags($rowid));

  $query = sqlite_query($db, "select * from recipes where rowid = '$rowid'");

  return sqlite_fetch_array($query);
}

function insert_attempt($recipe_id, $google_plus_activity_id)
{
  $db = init_db();
  $recipe_id = sqlite_escape_string(strip_tags($recipe_id));
  $google_plus_activity_id =
      sqlite_escape_string(strip_tags($google_plus_activity_id));

  sqlite_exec($db, "insert into attempts values ('$recipe_id',
                    '$google_plus_activity_id')");
}

// Google+ specific code
require_once 'google-api-php-client/src/apiClient.php';
require_once 'google-api-php-client/src/contrib/apiPlusService.php';

session_start();

date_default_timezone_set('America/Los_Angeles');

function init_api_client()
{
  global $app_base_path;

  $client = new apiClient();
  $client->setApplicationName("Baking Disasters");
  $client->setClientId('YOUR_CLIENT_ID');
  $client->setClientSecret('YOUR_CLIENT_SECRET');
  $client->setRedirectUri($app_base_path . '/login.php');
  $client->setDeveloperKey('YOUR_API_KEY');
  $client->setScopes(array('https://www.googleapis.com/auth/plus.me'));
  return $client;
}

function is_logged_in()
{
  if (isset($_SESSION['access_token'])) {
    return true;
  } else {
    return false;
  }
}

function get_plus_profile()
{
  if (!is_logged_in()) {
    die("Expected to be logged in here");
  }

  $client = init_api_client();
  $client->setAccessToken($_SESSION['access_token']);
  $plus = new apiPlusService($client);
  $me = $plus->people->get('me');
  return $me;
}

function get_recent_activities()
{
  if (!is_logged_in()) {
    die("Expected to be logged in here");
  }
  $client = init_api_client();
  $client->setAccessToken($_SESSION['access_token']);
  $plus = new apiPlusService($client);

  $optional_parameters = array('maxResults' => 20);
  $activities =
      $plus->activities->listActivities('me', 'public', $optional_parameters);
  return $activities;
}


// A little bit of both
function list_attempts($recipe_id)
{
  $db = init_db();
  $recipe_id = sqlite_escape_string(strip_tags($recipe_id));

  $query = sqlite_query($db,
                       "select * from attempts where recipe_id = '$recipe_id'");
  $attempt_stubs = sqlite_fetch_all($query, SQLITE_ASSOC);

  $client = init_api_client();
  $plus = new apiPlusService($client);

  $attempts = Array();
  foreach ($attempt_stubs as $attempt_stub) {
    $google_plus_activity_id = $attempt_stub['google_plus_activity_id'];
    try {
      $activity = $plus->activities->get($google_plus_activity_id);

      $attempt = Array();
      $attempt['url'] = $activity['url'];
      $attempt['author'] = $activity['actor'];
      $attempt['description'] = $activity['object']['content'];
      if (count($activity['object']['attachments']) > 0) {
        $attempt['photo_url'] =
            $activity['object']['attachments'][0]['image']['url'];
      }
      array_push($attempts, $attempt);
    } catch (Exception $e) {
      if ($e->getCode() == 404) {
        // If it's a 404, it has been deleted by the user. Clean it up.
        sqlite_exec($db, "delete from attempts where
                          google_plus_activity_id='$google_plus_activity_id';");
      }
    }
  }
  return $attempts;
}
