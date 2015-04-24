<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");

include_once("util.php");
if ($_GET['ingredients']) {
  $recipe = get_recipe($_GET['id']);
  if($recipe) {
    $response = array(
      'name'=>$recipe['name'],
      'ingredients'=>explode("\r\n", $recipe['ingredients']));
    echo str_replace('\/', '/', json_encode($response));
  }
} else if ($_GET['recipes']) {
  $recipes = list_recipes();
  $response = array();

  foreach ($recipes as $recipe) {
    array_push($response, array(
      'id'=>$recipe['rowid'],
      'name'=>$recipe['name'],
      'imageUrl'=>$recipe['photo_url']
    ));
  }
  echo str_replace('\/', '/', json_encode($response));
  
} else {
  header("HTTP/1.0 404 Not Found");
  echo '{"error":"not found"}';
}

