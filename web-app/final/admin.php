<?php include_once("util.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Baking Disasters 2.0</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="shortcut icon" href="images/logo_favicon.png" />
</head>
<body>
<header class="blog-header">
  <span class="login">
    <?php if(is_logged_in()) { ?>
      <a href="logout.php">Logout</a>
    <?php } else { ?>
    <a href="login.php">Log in with Google+</a>
    <?php } ?>
  </span>
  <a href="index.php"><img id="blog-logo" src="images/logo.png"></a>
  <h1>Baking Disasters</h1>
  <p>Because sometimes molecular gastronomy explodes.</p>
</header>
<?php
if(is_logged_in()) {
  $me = get_plus_profile();
  if($me['id'] == "102817283354809142195") {
    if ($_POST) {
      insert_recipe($_POST['name'], $_POST['description'],
                    $_POST['ingredients'], $_POST['directions'],
                    $_POST['photo_url']);
      echo "<p class='notice'>Recipe inserted.</p>";
    }
?>
<section class="content">
  <p>Create a new recipe</p>

  <form method="post">
    <label>Name: <input name="name"></label>
    <label>Description: <textarea name="description"></textarea></label>
    <label>Ingredients: <textarea name="ingredients"></textarea></label>
    <label>Directions: <textarea name="directions"></textarea></label>
    <label>Photo URL: <input type="text" name="photo_url"/></label>
    <input type="submit"/>
  </form>
</section>
  <?php   } else { echo "Only Jenny Murphy can access this page.";  }}?>
<footer>
  By <a href="http://plus.google.com/116852994107721644038">Baking Disasters</a>
</footer>
</body>
</html>

