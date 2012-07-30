<?php
// Create the tables if they do not exist
function init_db()
{
  $db = sqlite_open('PATH_TO_YOUR_DATABASE');
  $test_query = "select count(*) from sqlite_master where name = 'recipes'";
  if (sqlite_fetch_single(sqlite_query($db, $test_query)) == 0) {
    sqlite_exec($db, 'create table recipes (name text, description text,
                      ingredients text, directions text, photo_url text);');
    sqlite_exec($db, 'create table attempts (recipe_id int, author_name text,
                      description text, photo_url text);');
  }
  return $db;
}

# Insert a new recipe
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

function insert_attempt($recipe_id, $author_name, $description, $photo_url)
{
  $db = init_db();
  $recipe_id = sqlite_escape_string(strip_tags($recipe_id));
  $author_name = sqlite_escape_string(strip_tags($author_name));
  $description = sqlite_escape_string(strip_tags($description));
  $photo_url = sqlite_escape_string(strip_tags($photo_url));

  sqlite_exec($db, "insert into attempts values ('$recipe_id', '$author_name',
                    '$description', '$photo_url')");
}

function list_attempts($recipe_id)
{
  $db = init_db();
  $recipe_id = sqlite_escape_string(strip_tags($recipe_id));

  $query = sqlite_query($db,
                       "select * from attempts where recipe_id = '$recipe_id'");
  return sqlite_fetch_all($query, SQLITE_ASSOC);
}
