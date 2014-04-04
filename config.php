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
    define('GITHUB_CLIENT', ''); // used for github login
    define('GITHUB_SECRET', ''); // used for github login
    // mysql database
    define('DB_HOST',       '');
    define('DB_USER',       '');
    define('DB_PASS',       '');
    define('DB_NAME',       '');
  }
?>