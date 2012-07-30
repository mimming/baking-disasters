<!DOCTYPE html>
<html>
<head>
  <title>Baking Disasters 2.0</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="shortcut icon" href="images/logo_favicon.png" />
</head>
<body>
<header class="blog-header">
  <a href="index.php"><img id="blog-logo" src="images/logo.png"/></a>
  <h1>Baking Disasters</h1>
  <p>Because sometimes molecular gastronomy explodes.</p>
</header>
<section class="content">
  <?php

  include_once("util.php");
  $recipes = list_recipes();
  foreach ($recipes as $recipe) {
    ?>
    <section class="post-summary">
      <header><h2><?= $recipe['name'] ?></h2></header>
      <img class="recipe-photo" src="<?= $recipe['photo_url'] ?>" />

      <p><?= str_replace("\n", "<br/>\n",
                         stripslashes($recipe['description'])) ?>
        <a href="recipe.php?id=<?= $recipe['rowid'] ?>">read more</a>
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


