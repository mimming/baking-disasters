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
      <script src="https://apis.google.com/js/plusone.js"></script>

      <g:plus action="connect"
              clientid="116363269786.apps.googleusercontent.com"
              state="<?= get_new_state() ?>"
              redirecturi="https://bakingdisasters.com/web-app/experimental/oauth-redirect.php"
              scope="https://www.googleapis.com/auth/plus.moments.write"
              callback="onSignIn">
      </g:plus>
      <script>
          function onSignIn(result) {
              if(result.error) {
                  console.log("Sign in failed to render :( " + result.error.message);
              } else {
                  window.location.reload();
              }
          }
      </script>
      <?php } ?>
  </span>
  <a href="index.php"><img id="blog-logo" src="images/logo.png"/></a>
  <h1>Baking Disasters</h1>
  <p>Because sometimes molecular gastronomy explodes.</p>
</header>
<section class="content">
  <?php
  $recipes = list_recipes();
  foreach ($recipes as $recipe) {
    ?>
    <section class="post-summary">
      <header><h2><?= $recipe['name'] ?></h2></header>
      <img class="recipe-photo" src="<?= $recipe['photo_url'] ?>" />

      <p><?= str_replace("\n", "<br/>\n",
                         stripslashes($recipe['description'])) ?>
        <a href="recipe.php?recipe_id=<?= $recipe['rowid'] ?>">read more</a>
      </p>
    </section>

    <?php } ?>
  <div style="clear: both;"></div>
</section>
<footer>
  By <a href="http://plus.google.com/116852994107721644038">Baking Disasters</a>
</footer>
</body>
</html>


