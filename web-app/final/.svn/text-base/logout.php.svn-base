<?php
include_once("util.php");
unset($_SESSION['access_token']);
if(isset($_SERVER['HTTP_REFERER'])) {
  header('Location: '.$_SERVER['HTTP_REFERER']);
} else {
  header('Location: '.$app_base_path);
}
