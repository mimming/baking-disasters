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
    <?php if (is_logged_in()) { ?>
    <a href="logout.php">Logout</a>
    <?php } else { ?>
    <a href="login.php">Log in with Google+</a>
    <?php } ?>
  </span>
  <a href="index.php"><img id="blog-logo" src="images/logo.png"/></a>

  <h1>Baking Disasters</h1>

  <p>Because sometimes molecular gastronomy explodes.</p>
</header>
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
    <?php if(isset($attempt['photo_url'])) { ?>
    <img class="attempt-photo" src="<?= $attempt['photo_url'] ?>" />
    <?php } ?>

    <h3>
      <a href="<?= $attempt['author']['url']?>">
        <img src="<?= $attempt['author']['image']['url']?>"/></a>
      <?= $attempt['author']['displayName'] ?>'s Attempt
      <a class="import-link" href="<?= $attempt['url'] ?>">imported from Google+</a>
    </h3>

    <p>
      <?= str_replace("\n", "<br/>\n", stripslashes($attempt['description'])) ?>
    </p>
    <div style="clear:both;"></div>
  </div>
  <?php } ?>
</section>
  <?php } ?>

<section class="content attempt-form">

<?php
if (is_logged_in()) {
  ?>
  <h2>Report Your Attempt</h2>

  <p>Have you attempted this recipe with disastrous results? Tell us about
    it! <a href="import.php?recipe_id=<?= $_GET['recipe_id']?>">Import from your
    recent public Google+ activity</a></p>

  <?php } else { ?>
  <p><a href="login.php">Log in with Google+</a> to tell us about your attempt</p>
  <?php } ?>
</section>

<footer>
  By <a href="http://plus.google.com/116852994107721644038">Baking Disasters</a>
</footer>
</body>
</html>
