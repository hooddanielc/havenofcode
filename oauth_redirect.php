<?php
  session_start();
  include 'config.php';
  // check to see if our states match
  if($_GET['state'] == $_SESSION['state']) {
    // proceed with oauth process and fetch token
    $url = 'https://github.com/login/oauth/access_token';
    $data = [
      'client_id' => GITHUB_CLIENT,
      'client_secret' => GITHUB_SECRET,
      'code' => $_GET['code']
    ];

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\nAccept: application/json\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    $json = json_decode($result);

    if($json) {
      if(isset($json->access_token)) {
        // set the access token as session
        // and redirect to home page
        $_SESSION['access_token'] = $json->access_token;
        header("Location: index.php");
      } else {
        // oauth is broken
        echo ':-(';
      }
    } else {
      // json_decode didn't work
      echo ':-(';
    }
  } else {
    // this line should redirect
    // to 404 not found page
    echo ':-(';
  }
?>