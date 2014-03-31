<?php
  session_start();
  include 'config.php';
  include 'api/methods/user.php';

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

        // get some user data
        ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
        $result = file_get_contents('https://api.github.com/user?access_token='.$json->access_token);
        $json = json_decode($result);
        $github_id = $json->id;

        // create new user if not exists
        $user = new User();
        $user->post(['github_id' => $github_id, 'user_cache' => $result]);
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