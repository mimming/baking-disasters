<?php include_once("util.php");

// get the recipe ID
$recipe_id = $_GET['recipe_id'];

// insert a moment that targets that recipe page
$client = init_api_client();

$history = new apiPlusMomentsService($client);

$target = new ItemScope();
$target->setUrl("http://example.com");
$body = new Moment();
$body->setTarget($target);
$body->setType("http://schemas.google.com/AddActivity");

$response = $history->moments->insert('me', 'vault', $body);

// redirect back to source page
if(isset($_SESSION['original_referrer'])) {
    header('Location: ' . $_SESSION['original_referrer']);
    unset($_SESSION['original_referrer']);
} else {
    header('Location: '.$app_base_path);
}