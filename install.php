<?php
  include 'config.php';

  // lets check if the config file was edited
  if(GITHUB_CLIENT === '' && GITHUB_SECRET === '') {
    die('install failed. You need to edit config file for github client and secret. Goto github.com and register a test application.');
  }

  // lets test database connenction
  $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

  if($con->connect_errno) {
    // mysql create havenofcode database failed
    mysqli_close($con);
    die('install failed. Mysql connection failed. Please check your config.php file for correct DB values.');
  } else {
    if (!$con->multi_query(file_get_contents('havenofcode.sql'))) {
      die('install failed. There is something wrong with this havenofcode.sql?');
    }
    echo 'install successful! <a href="/index.php">Go to the home page!</a>';
  }
?>