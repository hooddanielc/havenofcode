<?php
  session_start();
  include 'config.php';
  include 'api/methods/user.php';
  $authorizeURL = 'https://github.com/login/oauth/authorize';
  $tokenURL = 'https://github.com/login/oauth/access_token';
  $apiURLBase = 'https://api.github.com/';

  // When Github redirects the user back here, there will be a "code" and "state" parameter in the query string
  if (get('code')) {
    // Verify the state matches our stored state
    if(!get('state') || $_SESSION['state'] != get('state')) {
      header('Location: ' . $_SERVER['PHP_SELF']);
      die();
    }
    // Exchange the auth code for a token
    $token = apiRequest($tokenURL, array(
      'client_id' => GITHUB_CLIENT,
      'client_secret' => GITHUB_SECRET,
      'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
      'state' => $_SESSION['state'],
      'code' => get('code')
    ));
    $_SESSION['access_token'] = $token->access_token;
    header('Location: ' . $_SERVER['PHP_SELF']);
  }

  if(session('access_token')) {
    $res = apiRequest($apiURLBase . 'user');
    $user = new User();
    $user->post(['github_id' => $res->{'id'}, 'user_cache' => json_encode($res)]);    
    header("Location: index.php");
  } else {
    echo '<h3>Not logged in</h3>';
    echo '<p><a href="?action=login">Log In</a></p>';
  }

  function apiRequest($url, $post=FALSE, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if($post)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $headers[] = 'Accept: application/json';
    $headers[] = 'User-Agent: Haven of Code';
    if(session('access_token'))
      $headers[] = 'Authorization: Bearer ' . session('access_token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    return json_decode($response);
  }

  function get($key, $default=NULL) {
    return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
  }

  function session($key, $default=NULL) {
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
  }
?>