<!DOCTYPE html>
<html>
<head>
  <title>Baking Disasters 2.0</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="shortcut icon" href="images/logo_favicon.png" />
</head>
<body>
<header class="blog-header">
  <a href="index.php"><img id="blog-logo" src="images/logo.png"></a>
  <h1>Baking Disasters</h1>
  <p>Because sometimes molecular gastronomy explodes.</p>
</header>
<section class="content"  itemscope itemtype="http://schema.org/Thing">
  <h2 itemprop="name">Potluck Reminder</h2>
  <p>Remember to bring:</p>
  <pre itemprop="description">
<?= htmlspecialchars(urldecode($_GET["reminder"]), ENT_QUOTES, 'UTF-8'); ?>
  </pre>
  <div style="clear: both;"></div>
</section>
<footer>
  By <a href="http://plus.google.com/116852994107721644038">Baking Disasters</a>
</footer>
</body>
</html>


