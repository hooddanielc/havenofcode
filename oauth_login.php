<?php
  function get_redirect_domain() {
    // prod urls should always contain 'www' in domain
    $parts = explode('.', $_SERVER['SERVER_NAME']);
    if (count($parts) == 2 && $parts[count($parts) - 1] == 'com') {
      return 'www.' . $_SERVER['SERVER_NAME'];
    }
    return $_SERVER['SERVER_NAME'];
  }

  if (get_redirect_domain() != $_SERVER['SERVER_NAME']) {
    header('Location: ' . 'http://' . get_redirect_domain() . $_SERVER['REQUEST_URI']);
    die();
  }

  include 'config.php';
  $state = base64_encode(rand());
  $client_id = GITHUB_CLIENT;
  
  // save the state in the cookie
  session_start();
  $_SESSION['state'] = $state;

  header('Location: https://github.com/login/oauth/authorize?client_id='.$client_id.'&state='. urlencode($state));
?>
