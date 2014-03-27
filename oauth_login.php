<?php
  include 'config.php';
  $state = base64_encode(rand());
  $client_id = GITHUB_CLIENT;
  
  // save the state in the cookie
  session_start();
  $_SESSION['state'] = $state;

  header('Location: https://github.com/login/oauth/authorize?client_id='.$client_id.'&state='. urlencode($state));
?>