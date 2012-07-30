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
<?php
include_once("util.php");
if ($_POST) {
  insert_attempt($_POST['recipe_id'], $_POST['author_name'],
                 $_POST['description'], $_POST['photo_url']);
  echo "<p class='notice'>Attempt inserted!</p>";
}
?>
<?php
    if ($_GET['recipe_id']) {
      $recipe = get_recipe($_GET['recipe_id']);
      $attempts = list_attempts($_GET['recipe_id']);
  ?>
<section class="content">
  <section class="recipe">
    <header>
      <h2><?= $recipe['name'] ?></h2>
    </header>
    <img class="recipe-photo" src="<?= $recipe['photo_url'] ?>" />

    <p>
      <?= str_replace("\n", "<br/>\n", stripslashes($recipe['description'])) ?>
    </p>

    <h3>Ingredients</h3>
    <ul>
      <li><?= str_replace("\n", "</li>\n<li>",
                          stripslashes(rtrim($recipe['ingredients']))) ?></li>
    </ul>
    <h3>Directions</h3>
    <ol>
      <li>
        <?= str_replace("\n", "</li>\n<li>",
                        stripslashes(rtrim($recipe['directions']))) ?>
      </li>
    </ol>
  </section>
</section>
<section class="content attempts">
  <?php foreach ($attempts as $attempt) { ?>
  <div class="attempt">
    <img class="attempt-photo" src="<?= $attempt['photo_url'] ?>" height="100"
         width="100"/>

    <h3><?= $attempt['author_name'] ?>'s Attempt</h3>

    <p>
      <?= str_replace("\n", "<br/>\n", stripslashes($attempt['description'])) ?>
    </p>
  </div>
  <?php } ?>
  <div style="clear:both;"></div>
</section>
<?php } ?>
<section class="content attempt-form">
  <h2>Report Your Attempt</h2>

  <p>Have you attempted this recipe with disastrous results? Tell us about
    it!</p>

  <form method="post">
    <input type="hidden" name="recipe_id" value="<?= $_GET['recipe_id'] ?>"/>
    <label>Your Name: <input name="author_name"></label>
    <label>Description: <textarea name="description"></textarea></label>
    <label>Photo URL: <input type="text" name="photo_url"/></label>
    <input type="submit"/>
  </form>
</section>
<footer>
  By <a href="http://plus.google.com/116852994107721644038">Baking Disasters</a>
</footer>
<!-- Asynchronously load the +1 button JavaScript -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script');
    po.type = 'text/javascript';
    po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
  })();
</script>
</body>
</html>
