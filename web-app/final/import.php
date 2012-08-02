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
  <a href="index.php"><img id="blog-logo" src="images/logo.png"></a>
  <h1>Baking Disasters</h1>
  <p>Because sometimes molecular gastronomy explodes.</p>
</header>
<?php

if (is_logged_in()) {
  if ($_POST) {
    insert_attempt($_POST['recipe_id'], $_POST['google_plus_activity_id']);
    ?>
  <p class='notice'>Activity imported! Go see it on the
    <a href="recipe.php?recipe_id=<?= $_POST['recipe_id'] ?>">recipe page</a></p>
      <?php
  } else {
    $recent_activities = get_recent_activities();
    ?>
<section class="content attempts">
  <p>Select an activity to import as an attempt.</p>
    <?php
        foreach ($recent_activities['items'] as $activity) {
      ?>
      <div class="attempt import" onclick="<?= $activity['id'] ?>.submit()">
        <?php
        if (count($activity['object']['attachments']) > 0) {
        ?>
          <img class="attempt-photo" src="
            <?= $activity['object']['attachments'][0]['image']['url'] ?>">
        <?php } ?>

        <h3><?= $activity['actor']['displayName'] ?>'s Attempt</h3>

        <p>
          <?= $activity['object']['content'] ?>
        </p>
        <form id="<?= $activity['id'] ?>" method="post">
          <input type="hidden" name="recipe_id"
                 value="<?= $_GET['recipe_id'] ?>"/>
          <input type="hidden" name="google_plus_activity_id"
                 value="<?= $activity['id'] ?>"/>
        </form>
      </div>
        <?php
    }
  }
  ?>
</section>
  <?php } else { ?>
<p class='notice'>
  Please <a href="login.php">Log in with Google+</a> to access this page.
</p>
  <?php }?>
<footer>
  By <a href="http://plus.google.com/116852994107721644038">Baking Disasters</a>
</footer>
</body>
</html>

