<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';
  include 'modules/article_list.php';
http://havenofcode.com/login_error.php?error=redirect_uri_mismatch&error_description=The+redirect_uri+MUST+match+the+registered+callback+URL+for+this+application.&error_uri=https%3A%2F%2Fdeveloper.github.com%2Fv3%2Foauth%2F%23redirect-uri-mismatch&state=MTg1MDI2NTcw
  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();
      $this->addData('error', $_GET['error']);
      $this->addData('error_description', $_GET['error_description']);
      $this->addData('error_uri', $_GET['error_uri']);
    }

    public static $js = [
      'pages/login-error/login-error.js'
    ];

    public static $text = [
      'login-error' => 'pages/login-error/login-error.mustache'
    ];
  }

  $page = new MyPage();
  $page->render();
?>
