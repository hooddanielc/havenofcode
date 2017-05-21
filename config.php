<?php
  /*
  *
  * Do NOT commit changes to this file.
  *
  * change this file to set
  * up your developement environment.
  *
  * goto /install.php install database
  * * * * * * * * * * * * * * * * * * */

  // website uses github application for authentication
  if(!defined('GITHUB_CLIENT')) {
    define('GITHUB_CLIENT', $_ENV['HOC_GITHUB_CLIENT']); // used for github login
    define('GITHUB_SECRET', $_ENV['HOC_GITHUB_SECRET']); // used for github login
    // mysql database
    define('DB_HOST', $_ENV['HOC_MYSQL_HOST']);
    define('DB_USER', $_ENV['HOC_MYSQL_USER']);
    define('DB_PASS', $_ENV['HOC_MYSQL_PASS']);
    define('DB_NAME', $_ENV['HOC_MYSQL_NAME']);
  }
?>